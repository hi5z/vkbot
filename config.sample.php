<?

$link = mysqli_connect("HOST","USER","PASSWORD","DATABASENAME") or die("Error " . mysqli_error($link)); 
mysqli_set_charset($link,"utf8");

// // Variables // //
// ANTIGATE.COM КЛЮЧ //
$config['antigate'] = "-- YOUR ANTIGATE KEY HERE --";

// Уникальный token профиля вконтакте //
$config['token'] = "-- ТОКЕН ВАШЕГО ПРОФИЛЯ НА ВК --";

// Ключ для декриптования ответов с iii.ru //
$config['key'] = "some very-very long string without any non-latin characters due to different string representations inside of variable programming languages";

// ID БОТА НА САЙТЕ iii.ru //
$config['botid'] = '-- ID БОТА С САЙТА iii.ru --';

// URL ХОСТА //
$config['url'] = 'http://' . $_SERVER['HTTP_HOST'];