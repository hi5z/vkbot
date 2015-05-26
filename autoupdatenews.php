<?php
// Отображать все ошибки или нет//
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once "config.php";
require_once "vk.api.php";


define('VK_TOKEN',$config['token']);
$vk = new VK(VK_TOKEN);


    // Получаем список последних 20 новостей //
    $wall = $vk->request('newsfeed.get', array(
        'count' => '10',
        'return_banned' => '0',
    ));

    $repost = $vk->request('wall.repost', array(
        'object' => 'wall' . $wall['response']['items'][0]['source_id'] .'_'. $wall['response']['items'][0]['post_id'],
    ));

    // Выводим ленту //
    echo '<h3>Новости</h3>';
    $i = 99;
    foreach ((array)$wall['response']['items'] as $key => $value) {

        if ($value['post_id'] != null ){
            ?>

            <div class="panel panel-default">
                <div class="panel-heading"><a href="http://vk.com/wall<?= $value['source_id'] ?>_<?= $value['post_id'] ?>" target="_blank">http://vk.com/wall<?= $value['source_id'] ?>_<?= $value['post_id'] ?></a> <br />Вероятность репоста: <span class="badge"><?=$i?>%</span></div>
                <div class="panel-body">
                    <?= $value['text'] ?><hr />
                    <span class="text-muted">ВНИМАНИЕ! Если нет текста, то скорее всего в посте присутствует картинка или другое прикрепление.</span>
                </div>
            </div>
        <?  $i= $i/2;
        }
    }