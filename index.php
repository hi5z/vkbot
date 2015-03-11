<?
require_once 'classes.php';
require_once 'config.php';

$accountinfo = curl("https://api.vk.com/method/users.get?fields=photo_max,online,counters&access_token=" . $config['token']);
$friendsget = curl("https://api.vk.com/method/friends.getRequests?v=5.28&access_token=" . $config['token']);
$statssget = curl("https://api.vk.com/method/stats.trackVisitor?v=5.28&access_token=" . $config['token']);
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
    function updateShouts() {
        $('#shoutbox').load('autoupdate.php');

    }
    $(document).ready(function () {
        setInterval("updateShouts()", 4000);
    });
</script>


<div class="container">
    <h2>Управление ботом</h2>

    <div class="row">

        <div class="col-lg-4">
            <h3>Информация</h3>
            <img width="200px" src="<?= $accountinfo['response'][0]['photo_max'] ?>" class="img-thumbnail"/>

            <h3><?= $accountinfo['response'][0]['first_name'] ?> <?= $accountinfo['response'][0]['last_name'] ?></h3>
            Статус: <? if ($accountinfo['response'][0]['online'] == '1') {
                echo '<font color="green">Online</font>';
            } else {
                echo '<font color="red">Offline</font>';
            } ?><br/>
            Друзей: <?= $accountinfo['response'][0]['counters']['friends'] ?><br/>
            Друзей онлайн: <?= $accountinfo['response'][0]['counters']['online_friends'] ?><br/>
            Подписчиков: <?= $accountinfo['response'][0]['counters']['followers'] ?><br/>
        </div>

        <div class="col-lg-4" id="shoutbox">
            <h3>История сообщений</h3>
            <img align="center" width="50px" src="/preloader.gif"/>
        </div>

        <div class="col-lg-4">
            <h3>Заявки в друзья</h3>
            <?
            for ($i = 0; $i < $friendsget['response']['count']; ++$i) {

                $friendinfo = curl("https://api.vk.com/method/users.get?fields=photo_max&user_ids=" . $friendsget['response']['items'][$i]);
                ?>
                <div class="media">
                    <div class="media-left">
                        <a href="http://vk.com/id<?= $friendsget['response']['items'][$i] ?>">
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
    </div>


</div>
</body>


</html>