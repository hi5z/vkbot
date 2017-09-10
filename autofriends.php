<?php
// Отображать все ошибки или нет//
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once "config.php";
<<<<<<< HEAD


$vk_config = array(
    'app_id' => $config['app_id'],
    'api_secret' => $config['app_secret'],
    'access_token' => $config['token']

);

try {
    $vk = new \models\API($vk_config);


    $friendsget = $vk->getFriendsRequest();
=======
require_once "vk.api.php";


define('VK_TOKEN',$config['token']);
$vk = new VK(VK_TOKEN);




    $friendsget = $vk->request('friends.getRequests');
>>>>>>> master

    for ($i = 0; $i < count($friendsget); $i++) {

<<<<<<< HEAD
        $vk->addFriend($friendsget[$i]);

    }

} catch (Exception $error) {


    echo $error->getMessage();
=======
    $wall = $vk->request('friends.add', array(
        'user_id' => $friendsget['response'][$i],
    ));
    
>>>>>>> master
}
