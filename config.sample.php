<?

$link = mysqli_connect("HOST","USER","PASSWORD","DATABASENAME") or die("Error " . mysqli_error($link)); 
mysqli_set_charset($link,"utf8");

// // Variables // //
// ANTIGATE.COM КЛЮЧ //
$config['antigate'] = ""; // не обязательное поле. если оставите пустым - капчу решать не будет

// Уникальный token профиля вконтакте //
$config['token'] = "VK_API_TOKEN";

// Ключ для декриптования ответов с iii.ru //
$config['key'] = "some very-very long string without any non-latin characters due to different string representations inside of variable programming languages"; // здесь ничего никогда не нужно менять. просто не трогайте

// ID БОТА НА САЙТЕ iii.ru //
$config['botid'] = 'ID_БОТА_С_САЙТА_iii.ru'; // ID бота. если нет своего используйте мой - 6804a238-2f99-4e1a-9a38-d90b71401b88 только ваш бот будет представляться Ларисой.

// URL ХОСТА //
$config['url'] = 'http://' . $_SERVER['HTTP_HOST'];

// ID пользователей, которым бот не будет отвечать //
$debug = array("5269222", "1");