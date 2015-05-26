<?php
// Отображать все ошибки или нет//
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once "config.php";
require_once "vk.api.php";


define('VK_TOKEN',$config['token']);
$vk = new VK(VK_TOKEN);




    $friendsget = $vk->request('friends.getRequests');

for ($i = 0; $i < count($friendsget['response']); $i++) {

    $wall = $vk->request('friends.add', array(
        'user_id' => $friendsget['response'][$i],
    ));
    
}
