<?php
/**
 * @package vkbotphp
 * @author Dmitriy Kuts <me@exileed.com>
 * @date 3/20/2015
 * @time 3:55 PM
 * @link http://exileed.com
 */

require_once "config.php";
require_once "vendor/autoload.php";

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
