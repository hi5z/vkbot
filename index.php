<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/////////////////////// ЗАЩИТА ВО ВСЕ ПОЛЯ! ////////////////////////////////////
include 'config.php';

$pass = md5($config['adminpass']);




if($_COOKIE["pass"]!==$pass){
    sleep(1);

    if(isset($_POST["pass"])){
        setcookie("pass",md5($_POST["pass"]), time()+3600*24*14);

        die('<meta http-equiv="refresh" content="1; url=./">');
    }

    ?>

    <html>
    <head>
        <title>Админ-панель</title>
        <link rel="stylesheet" href="http://hash2vote.su/css/bootstrap.css">
        <link rel="stylesheet" href="http://hash2vote.su/css/bootstrap-theme.css">
        <link rel="stylesheet" href="http://hash2vote.su/css/admin.css">
        <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="http://hash2vote.su/js/bootstrap.min.js"></script>
        <script src="http://hash2vote.su/js/TweenLite.min.js"></script>
        <script>
            $(document).ready(function() {
                $(document).mousemove(function(event) {
                    TweenLite.to($("body"),
                        .5, {
                            css: {
                                backgroundPosition: "" + parseInt(event.pageX / 8) + "px " + parseInt(event.pageY / '12') + "px, " + parseInt(event.pageX / '15') + "px " + parseInt(event.pageY / '15') + "px, " + parseInt(event.pageX / '30') + "px " + parseInt(event.pageY / '30') + "px",
                                "background-position": parseInt(event.pageX / 8) + "px " + parseInt(event.pageY / 12) + "px, " + parseInt(event.pageX / 15) + "px " + parseInt(event.pageY / 15) + "px, " + parseInt(event.pageX / 30) + "px " + parseInt(event.pageY / 30) + "px"
                            }
                        })
                })
            })
        </script>
    </head>
    <body>
    <div class="container">
        <div class="row vertical-offset-100">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row-fluid user-row">
                            <img src="http://s11.postimg.org/7kzgji28v/logo_sm_2_mr_1.png" class="img-responsive" alt="Le mew wuz here"/>
                        </div>
                    </div>
                    <div class="panel-body">
                        <form method="POST" accept-charset="UTF-8" role="form" class="form-signin">
                            <fieldset>
                                <label class="panel-login">
                                    <div class="login_result"></div>
                                </label>
                                <input class="form-control" placeholder="Введите пароль" id="password" name="pass" type="password">
                                <br><br>
                                <input class="btn btn-lg btn-success btn-block" type="submit" id="submit" value="Войти »">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>

    </html>

    <?php
    exit();
}

//////////////////////// КОНЕЦ ЗАЩИТЫ ////////////////////////////////////////

require_once 'classes.php';
require_once 'config.php';
require 'vk.api.php';


define('VK_TOKEN',$config['token']);
$vk = new VK(VK_TOKEN);

$accountinfo = $vk->request('users.get', array(
    'fields' => 'photo_max,online,counters'
));

$friendsget = $vk->request('friends.getRequests', array(
    'out' => '0'
));


?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Бот
        - <?= $accountinfo['response'][0]['first_name'] ?> <?= $accountinfo['response'][0]['last_name'] ?></title>

    <link rel="stylesheet" href="/css/bootstrap.min.css">

    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
</head>

<body>



<script>

    var interval = null;

    function updateShouts() {
        $('#shoutbox').load('autoupdate.php');
    }

    function updateNews() {
        $('#news').load('autoupdatenews.php');
    }

    function updateFriends() {
        $('#friendsadd').load('autofriends.php');
    }


    $(document).ready(function () {
        $('#check').change(function(){
            if ($('#check').is(':checked')) {
                $('#status').text('Работает');
                $('#isonline').text('Online*');
                $('#preloader').show("slow");
                $('#notrunned').hide("slow");
                $('#shoutbox').load('autoupdate.php');
                interval = setInterval("updateShouts()", 10000);
            } else {
                $('#status').text('Не работает');
                clearInterval(interval);
            }})

        $('#reposts').change(function(){
            if ($('#reposts').is(':checked')) {
                $('#preloader2').show("slow");
                $('#notrunned2').hide("slow");
                $('#news').load('autoupdatenews.php');
                interval = setInterval("updateNews()", 1800000);
            } else {
                clearInterval(interval);
            }})

        $('#friends').change(function(){
            if ($('#friends').is(':checked')) {
                $('#friendsadd').load('autofriends.php');
                interval = setInterval("updateFriends()", 900000);
            } else {
                clearInterval(interval);
            }})


    });

</script>


<div class="container">
    <div class="row">

        <div class="col-lg-4">
            <h3><?= $accountinfo['response'][0]['first_name'] ?> <?= $accountinfo['response'][0]['last_name'] ?></h3>
            <img width="200px" src="<?= $accountinfo['response'][0]['photo_max'] ?>" class="img-thumbnail"/> <br />
<form method="get">
            <b>Автоматический чат?</b> <input type="checkbox" id="check" name="check" /><br />
            <b>Рандомные репосты?</b>  <input type="checkbox" id="reposts" name="reposts" /><br />
            <b>Автоподтверждение друзей?</b>  <input type="checkbox" id="friends" name="friends" /><br />
</form>
            Статус: <span id="isonline"><? if ($accountinfo['response'][0]['online'] == '1') {
                echo '<font color="green">Online</font>';
            } else {
                echo '<font color="red">Offline</font>';
            } ?></span><br/>
            Друзей: <?= $accountinfo['response'][0]['counters']['friends'] ?><br/>
            Друзей онлайн: <?= $accountinfo['response'][0]['counters']['online_friends'] ?><br/>
            Подписчиков: <?= $accountinfo['response'][0]['counters']['followers'] ?><br/>
            Авточат сейчас: <span id="status">Выключен</span><br />
            <hr />
            <h3>Заявки в друзья</h3>
            <?
            if ($friendsget['response'] != null) {
            for ($i = 0; $i < count($friendsget['response']); $i++) {
                $friendinfo = curl("https://api.vk.com/method/users.get?fields=photo_max&user_ids=" . $friendsget['response'][$i]);
                ?>
                <div class="media">
                    <div class="media-left">
                        <a href="http://vk.com/id<?= $friendsget['response'][$i] ?>">
                            <img class="media-object" width="50px" src="<?= $friendinfo['response'][0]['photo_max'] ?>"
                                 alt="<?= $friendinfo['response'][0]['first_name'] ?> <?= $friendinfo['response'][0]['last_name'] ?>">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?= $friendinfo['response'][0]['first_name'] ?> <?= $friendinfo['response'][0]['last_name'] ?></h4>
                    </div>
                </div>
            <?
            } } else { echo 'Заявок в друзья нет';}?>
        </div>

        <div class="col-lg-4" id="shoutbox">
            <h3>История сообщений</h3>
            <span id="notrunned">Для того, чтобы включить бота - воспользуйтесь кнопкой слева.</span>
            <span id="preloader" style="display:none; text-align: center;"><img width="150px" src="/preloader.gif"/></span>
        </div>

        <div class="col-lg-4" id="news">
            <h3>Лента</h3>
            <span id="notrunned2">Для того, чтобы включить автоматический репостинг - воспользуйтесь кнопкой слева.</span>
            <span id="preloader2" style="display:none; text-align: center;"><img width="150px" src="/preloader.gif"/></span>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-4">
        </div>


        <div class="col-lg-4">
        </div>

        <div class="col-lg-4">
        </div>
    </div>


</div>

<div id="friendsadd" style="display:none;">&nbsp;</div>
</body>

<footer>
    <div class="col-md-12 text-center">vkbotphp v0.1.8.1</div>
</footer>
</html>