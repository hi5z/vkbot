<?php

require_once "../config.php";
require_once "../vk.php";
require_once "../vkexception.php";


$vk_config = array(
    'app_id' => '4798482',
    'api_secret' => 'yat6sCVTs6g4D8nCgWSJ',
    'access_token' => $config['token']
);

try {
    $vk = new VK\VK($vk_config['app_id'], $vk_config['api_secret'], $vk_config['access_token']);


    $addfriend = $vk->api('friends.add', array(
        'user_id' => $_GET['id'],
    ));
?>
    <html>
    <head><script type="text/javascript">window.close();</script></head>
    <body></body>
    </html>
<?

} catch (VK\VKException $error) {
    echo $error->getMessage();
}