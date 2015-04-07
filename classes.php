<?php

class XORFUNC
{

    public static function XOR_encrypt($message, $key)
    {
        $ml = strlen($message);
        $kl = strlen($key);
        $newmsg = "";

        for ($i = 0; $i < $ml; $i++) {
            $newmsg = $newmsg . ($message[$i] ^ $key[$i % $kl]);
        }

        return base64_encode($newmsg);
    }

    public static function XOR_decrypt($encrypted_message, $key)
    {
        $msg = base64_decode($encrypted_message);
        $ml = strlen($msg);
        $kl = strlen($key);
        $newmsg = "";

        for ($i = 0; $i < $ml; $i++) {
            $newmsg = $newmsg . ($msg[$i] ^ $key[$i % $kl]);
        }

        return $newmsg;
    }

}

class liveupdate
{
   function messages()
   {

   }
}


function cmd($cmd)
{

    if ($cmd == 'status') {
        $resp = 'Сейчас ' . date('h:i:s A') . ' Бот функционирует нормально.';
    }
    if ($cmd == 'help') {
        $resp = 'Нет доступных команд.';
    }
    if ($cmd == 'curr') {
        $curr = json_decode(file_get_contents('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5'));
        $resp = 'В ПриватБанке сейчас так: ' . $curr[0]->ccy . ' ' . $curr[0]->buy . ', ' . $curr[1]->ccy . ' ' . $curr[1]->buy . ', ' . $curr[2]->ccy . ' ' . $curr[2]->buy . ', ';
    }

    return $resp;
}

function initme($vkid, $key, $botid)
{
    $getuid = file_get_contents('http://iii.ru/api/2.0/json/Chat.init/' . $botid . '/' . $vkid);
    $jsonparam = json_decode(base64_decode(XORFUNC::XOR_decrypt($getuid, $key)));

    return $jsonparam;
}

function curl($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}
