<?php

require_once 'vendor/autoload.php';
require_once 'config.php';


$vk_config = array(
    'app_id' => $config['app_id'],
    'api_secret' => $config['app_secret'],
    'access_token' => $config['token']

);

try {


    $vk = new \models\API($vk_config);
    $accountinfo = $vk->getUserInfo();
    $friendsget = $vk->getFriendsRequest();
    $vk->setOnline();


} catch (VK\VKException $error) {
    echo $error->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Бот - <?= $accountinfo['first_name'] ?> <?= $accountinfo['last_name'] ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
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
                $('#preloader').show("slow");
                $('#notrunned').hide("slow");
                $('#shoutbox').load('autoupdate.php');
                interval = setInterval("updateShouts()", 4000);
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
                interval = setInterval("updateFriends()", 1800000);
            } else {
                clearInterval(interval);
            }})
    });

</script>


<div class="container">
    <div class="row">

        <div class="col-lg-4">
            <h3>Информация</h3>
            <b>Автоматический чат?</b> <input type="checkbox" id="check" /><br />
            <b>Рандомные репосты?</b>  <input type="checkbox" id="reposts" /><br />
            <b>Автоподтверждение друзей?</b>  <input type="checkbox" id="friends" /><br />

            <img width="200px" src="<?= $accountinfo['photo_max'] ?>" class="img-thumbnail"/>

            <h3><?= $accountinfo['first_name'] ?> <?= $accountinfo['last_name'] ?></h3>
            Статус: <? if ($accountinfo['online'] == '1') {
                echo '<font color="green">Online</font>';
            } else {
                echo '<font color="red">Offline</font>';
            } ?><br/>
            Друзей: <?= $accountinfo['counters']['friends'] ?><br/>
            Друзей онлайн: <?= $accountinfo['counters']['online_friends'] ?><br/>
            Подписчиков: <?= $accountinfo['counters']['followers'] ?><br/>
            Авточат сейчас: <span id="status">Выключен</span><br />
            <hr />
            <h3>Заявки в друзья</h3>
            <?
            for ($i = 0; $i < count($friendsget); $i++) {

                $friendinfo = curl("https://api.vk.com/method/users.get?fields=photo_max&user_ids=" . $friendsget[$i]);
                ?>
                <div class="media">
                    <div class="media-left">
                        <a href="http://vk.com/id<?= $friendsget[$i] ?>">
                            <img class="media-object" width="50px" src="<?= $friendinfo['response'][0]['photo_max'] ?>"
                                 alt="<?= $friendinfo['response'][0]['first_name'] ?> <?= $friendinfo['response'][0]['last_name'] ?>">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?= $friendinfo['response'][0]['first_name'] ?> <?= $friendinfo['response'][0]['last_name'] ?></h4>

                    </div>
                </div>
            <?
            } ?>
        </div>

        <div class="col-lg-4" id="shoutbox">
            <h3>История сообщений</h3>
            <span id="notrunned">Для того, чтобы включить бота - воспользуйтесь кнопкой слева.</span>
            <span id="preloader" style="display:none; text-align: center;"><img width="150px"
                                                                                src="preloader.gif"/></span>
        </div>

        <div class="col-lg-4" id="news">
            <h3>Лента</h3>
            <span id="notrunned2">Для того, чтобы включить автоматический репостинг - воспользуйтесь кнопкой слева.</span>
            <span id="preloader2" style="display:none; text-align: center;"><img width="150px"
                                                                                 src="preloader.gif"/></span>
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


</html>