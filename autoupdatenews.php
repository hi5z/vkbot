<?php

require_once "config.php";
require_once "vendor/autoload.php";

$vk_config = array(
    'app_id' => $config['app_id'],
    'api_secret' => $config['app_secret'],
    'access_token' => $config['token']

);

try {
    $vk = new VK\VK($vk_config['app_id'], $vk_config['api_secret'], $vk_config['access_token']);


    // Получаем список последних 20 новостей //
    $wall = $vk->api('newsfeed.get', array(
        'count' => '30',
        'return_banned' => '0',
    ));

    $repost = $vk->api('wall.repost', array(
        'object' => 'wall' . $wall['response']['items'][0]['source_id'] .'_'. $wall['response']['items'][0]['post_id'],
    ));

    // Выводим ленту //
    echo '<h3>Новости</h3>';
    foreach ((array)$wall['response']['items'] as $key => $value) {

        if ($value['post_id'] != null ){
            ?>

            <div class="panel panel-default">
                <div class="panel-heading">Новость</div>
                <div class="panel-body">
                    <a href="http://vk.com/wall<?= $value['source_id'] ?>_<?= $value['post_id'] ?>">Ссылка на новость.</a>
                </div>
            </div>
        <?
        }
    }
} catch (VK\VKException $error) {
    echo $error->getMessage();
}