<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$hashed = XORFUNC::XOR_encrypt(base64_encode($whattosend), $key);

$myCurl = curl_init();
curl_setopt_array($myCurl, array(
    CURLOPT_URL => 'http://iii.ru/api/2.0/json/Chat.request',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $hashed,
));
$response = curl_exec($myCurl);
curl_close($myCurl);


$answer = json_decode(base64_decode(XORFUNC::XOR_decrypt($response, $key)));

echo $answer->result->text->value;

// Озвучивание ответов если параметр tts = yes //
if (isset($_GET['tts']) AND $_GET['tts'] == 'yes') {

    $text = $answer->result->text->value;

    $text = urlencode($text);
    $lang = urldecode("ru");
    $file = "audio/" . md5($text) . ".mp3";
    if (!file_exists($file) || filesize($file) == 0) {
        $mp3 = file_get_contents('http://tts.voicetech.yandex.net/tts?format=mp3&quality=hi&platform=web&application=translate&lang=ru_RU&text=' . $text);

        if (file_put_contents($file, $mp3)) {
            echo "<!-- Saved -->";
        } else {
            echo "<!-- Wasn't able to save it ! -->";
        }
    } else {
        echo "<!-- Already exist -->";
    }
    ?>


    <audio controls="controls" autoplay="autoplay">
        <source src="<? echo $file; ?>" type="audio/mp3"/>
    </audio>

<? } ?>

