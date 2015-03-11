<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once "classes.php";
require_once "config.php";

$text = $_GET['text'];

if (isset($_GET['session'])) {
    $session = $_GET['session'];
} else {
    $session = 'f3e4de9e-261a-4833-97e6-1827b63c1099';
}
// Отсылаем текст и получаем ответ //
$whattosend = '["' . $session . '","' . urldecode($text) . '"]';
$hashed = XORFUNC::XOR_encrypt(base64_encode($whattosend), $config['key']);


$myCurl = curl_init();
curl_setopt_array($myCurl, array(
    CURLOPT_URL => 'http://iii.ru/api/2.0/json/Chat.request',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $hashed,
));
$response = curl_exec($myCurl);
curl_close($myCurl);


$answer = json_decode(base64_decode(XORFUNC::XOR_decrypt($response, $config['key'])));

echo $answer->result->text->value;
?>


