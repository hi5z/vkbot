<?
require_once 'classes.php';
require_once 'config.php';

$accountinfo = curl("https://api.vk.com/method/account.getProfileInfo?access_token=" . $token);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<title>Бот - <?=$accountinfo[response][first_name]?> <?=$accountinfo[response][last_name]?></title>

    <link rel="stylesheet" href="/css/bootstrap.min.css">

    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
</head>

<body>
<script>
function updateShouts(){
    $('#shoutbox').load('autoupdate.php');
	
}
$( document ).ready(function() {
	setInterval( "updateShouts()", 15000 );
});
</script>


<div class="container">
<h3>Управление ботом</h3>

<div class="row">
    <div class="col-lg-4" id="shoutbox">
        <img width="50px" src="/preloader.gif" />
    </div>

    <div class="col-lg-4">
        moar info
    </div>

    <div class="col-lg-4">
        moar info
    </div>
</div>


    </div>
</body>


</html>