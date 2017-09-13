<?php
/**
 * @package vkbotphp
 * @author Dmitriy Kuts <me@exileed.com>
 * @date 3/20/2015
 * @time 5:05 PM
 * @link http://exileed.com
 */


namespace models;


class Iii
{


    private $key = "some very-very long string without any non-latin characters due to different string representations inside of variable programming languages"; // здесь ничего никогда не нужно менять. просто не трогайте


    public function __construct($key = null)
    {


        if (!is_null($key))
            $this->key = $key;


    }

    public function sendMsg($chat_id = 'f3e4de9e-261a-4833-97e6-1827b63c1099', $text)
    {

        $whattosend = '["' . $chat_id . '","' . urldecode($text) . '"]';
        $hashed = $this->encrypt(base64_encode($whattosend));


        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => 'http://iii.ru/api/2.0/json/Chat.request',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $hashed,
        ));
        $response = curl_exec($myCurl);
        curl_close($myCurl);


        $answer = json_decode(base64_decode($this->decrypt($response)));

        return $answer->result->text->value;


    }

    function initMe($vkid, $botid)
    {
        $getuid = file_get_contents('http://iii.ru/api/2.0/json/Chat.init/' . $botid . '/' . $vkid);
        $data = json_decode(base64_decode($this->decrypt($getuid)));

        return $data->result->cuid;
    }

    private function decrypt($encrypted_message)
    {
        $msg = base64_decode($encrypted_message);
        $ml = strlen($msg);
        $kl = strlen($this->key);
        $newmsg = "";

        for ($i = 0; $i < $ml; $i++) {
            $newmsg = $newmsg . ($msg[$i] ^ $this->key[$i % $kl]);
        }

        return $newmsg;
    }

    private function encrypt($message)
    {
        $ml = strlen($message);
        $kl = strlen($this->key);
        $newmsg = "";

        for ($i = 0; $i < $ml; $i++) {
            $newmsg = $newmsg . ($message[$i] ^ $this->key[$i % $kl]);
        }

        return base64_encode($newmsg);
    }


}