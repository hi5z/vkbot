<?php
/*
ВНИМАНИЕ!
Значения {значение} вы должны поменять на свои. 
	Например: {USER} меняем на meow или {DATABASENAME} меняем на bot

Для того, чтобы бот заработал необходимо выполнить все по инструкции здесь - https://github.com/z00k/vkbotphp
*/

$link = mysqli_connect("{HOST}","{USER}","{PASSWORD}","{DATABASENAME}") or die("Ошибка " . mysqli_error($link)); 
mysqli_set_charset($link,"utf8");

/* Автоматически решать капчу с помощью сервиса Antigate (можно оставить пустым если нет ключа) */
$config['antigate'] = "";

/* Пароль к главной странице */
$config['adminpass'] = "meowisthebest";

/* Access token профиля ВКонтакте */
$config['token'] = "Ваш Access Token к профилю ВКонтакте";

/* ID инфа на сайте iii.ru */
$config['botid'] = "ID инфа на сайте iii.ru";

/* ID пользователей, которым бот не будет отвечать */
$debug = array(5269992, 1);












/* Эти значения не меняем */
$config['url'] = 'http://' . $_SERVER['HTTP_HOST'];
$config['key'] = "some very-very long string without any non-latin characters due to different string representations inside of variable programming languages";

