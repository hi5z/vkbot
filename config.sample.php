<?

$link = mysqli_connect("HOST","USER","PASSWORD","DATABASENAME") or die("Error " . mysqli_error($link)); 
mysqli_set_charset($link,"utf8");

// // Variables // //
// Уникальный token профиля вконтакте //
$token = "VKAPI TOKEN";

// Ключ для декриптования ответов с iii.ru //
$key = "some very-very long string without any non-latin characters due to different string representations inside of variable programming languages";

// ID БОТА НА САЙТЕ iii.ru //
$botid = '6804a238-2f99-4e1a-9a38-d90b71401b88';