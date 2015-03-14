<?php
// Отображать все ошибки или нет//
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once "config.php";
require_once "vk.php";
require_once "vkexception.php";


$vk_config = array(
    'app_id' => '4798482',
    'api_secret' => 'yat6sCVTs6g4D8nCgWSJ',
    'access_token' => $config['token']
);

try {
    $vk = new VK\VK($vk_config['app_id'], $vk_config['api_secret'], $vk_config['access_token']);


    $friendsget = $vk->api('friends.getRequests');

for ($i = 0; $i < count($friendsget['response']); $i++) {

    $wall = $vk->api('friends.add', array(
        'user_id' => $friendsget['response'][$i],
    ));


}

} catch (VK\VKException $error) {
    echo $error->getMessage();
}