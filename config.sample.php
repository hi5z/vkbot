<?

$link = mysqli_connect("HOST", "USER", "PASSWORD", "DATABASENAME") or die("Error " . mysqli_error($link));
mysqli_set_charset($link,"utf8");

// // Variables // //
// ANTIGATE.COM КЛЮЧ //
$config['antigate'] = ""; // не обязательное поле. если оставите пустым - капчу решать не будет



// Ключ для декриптования ответов с iii.ru //
$config['key'] = "some very-very long string without any non-latin characters due to different string representations inside of variable programming languages"; // здесь ничего никогда не нужно менять. просто не трогайте

// ID БОТА НА САЙТЕ iii.ru //
$config['botid'] = ""; // ID бота.

// URL ХОСТА //
$config['url'] = 'http://' . $_SERVER['HTTP_HOST'];

// ID пользователей, которым бот не будет отвечать //
$debug = array("5269222", "1");

// Номер приложения
$config['app_id'] = "VK_API_ID";
// Секретный ключ
$config['app_secret'] = "VK_API_SECRET";
// Уникальный token профиля вконтакте //
$config['token'] = "VK_API_TOKEN";


// Почта для отправки каптчи
$config['email'] = "";