<?
require_once 'classes.php';
require_once 'config.php';

$accountinfo = curl("https://api.vk.com/method/users.get?fields=photo_max,online,counters&access_token=" . $config['token']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<title>Бот - <?=$accountinfo['response'][0]['first_name']?> <?=$accountinfo['response'][0]['last_name']?></title>

    <link rel="stylesheet" href="/css/bootstrap.min.css">

    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
</head>

<body>
<script>
function updateShouts(){
    $('#shoutbox').load('autoupdate.php');
	
}
$( document ).ready(function() {
	setInterval( "updateShouts()", 7000 );
});
</script>


<div class="container">
<h3>Управление ботом</h3>

<div class="row">

    <div class="col-lg-4">
        <img width="200px" src="<?=$accountinfo['response'][0]['photo_max']?>" class="img-thumbnail" />
        <h3><?=$accountinfo['response'][0]['first_name']?> [<?=$accountinfo['response'][0]['uid']?>] <?=$accountinfo['response'][0]['last_name']?></h3>
        Статус: <? if ($accountinfo['response'][0]['online'] == '1') {echo 'Online';} else {echo 'Offline';} ?>
    </div>

    <div class="col-lg-4" id="shoutbox">
        <img width="50px" src="/preloader.gif" />
    </div>

    <div class="col-lg-4">
        moar info
    </div>
</div>


    </div>
</body>


</html>