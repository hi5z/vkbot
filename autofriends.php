<?php
// Отображать все ошибки или нет//
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once "config.php";


$vk_config = array(
    'app_id' => $config['app_id'],
    'api_secret' => $config['app_secret'],
    'access_token' => $config['token']

);

try {
    $vk = new \models\API($vk_config);


    $friendsget = $vk->getFriendsRequest();

    for ($i = 0; $i < count($friendsget); $i++) {

        $vk->addFriend($friendsget[$i]);

    }

} catch (Exception $error) {


    echo $error->getMessage();
}
