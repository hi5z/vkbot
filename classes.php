<?

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



function cmd($cmd)
{

    if ($cmd == 'status') {$resp = 'хуятус';}
    if ($cmd == 'help') {$resp = 'Нет доступных команд.';}

    return $resp;
}

function initme($vkid, $key)
{
    $getuid = file_get_contents('http://iii.ru/api/2.0/json/Chat.init/'. $config['botid'] .'/' . $vkid);
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
