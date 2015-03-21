<?php
// Отображать все ошибки или нет//
error_reporting(E_ALL);
ini_set('display_errors', 1);

sleep(6);

require_once "config.php";
require_once 'vendor/autoload.php';


$vk_config = array(
    'app_id' => $config['app_id'],
    'api_secret' => $config['app_secret'],
    'access_token' => $config['token']

);

try {

    $vk = new \models\API($vk_config);
    $iii = new \models\Iii();

    // Получаем список последних 20 сообщений //
    $messages = $vk->getMessage();

    // Получаем сообщения, на которые мы еще не отвечали //

    // Ставим статус Online //
    $vk->setOnline();

    // Выводим сообщения //
    // Отвечаем на 10 сообщений //
    echo '<h3>Последние чаты</h3>';
    $i = 0;
    foreach ((array)$messages as $key => $value) {
        $i++;

        if (isset($value['uid'])) {
            ?>

            <div class="panel panel-default">
                <div class="panel-heading">Отправил <?= $value['uid'] ?>
                    в <?= gmdate("Y-m-d H:i:s", $value['date']) ?> <? if ($value['read_state'] == '0') {
                        echo '<span functions="label label-danger">Не прочитано</span>';
                    } else {
                        echo '<span functions="label label-success">Прочитано</span>';
                    } ?> <? if ($value['out'] == '1') {
                        echo '<span functions="label label-primary">Ответ отправлен</span>';
                    } ?></div>
                <div class="panel-body">
                    <?= $value['body'] ?>
                </div>
            </div>
        <?
        }

        $uid = $value['uid'];
        $message = $value['body'];


        if ($message[0] == '/') {
            $vk->markAsRead($uid);

            $vk->setActivity($uid);

            sleep(1);

            // Не знаю зачем это нужно посылать, но пусть будет
            $send = $vk->sendMessages($value['uid'], cmd(substr($message, 1)));

        } elseif ($value['out'] == '0' AND !in_array($uid, $debug)) {
            // Сделаем выборку из базы //
            $result = $link->query("SELECT * FROM clients WHERE vkid=" . $uid);
            if ($result != false) {
                $row = mysqli_fetch_array($result);
            }

            if ($row['vkid'] == $uid AND $result != false) {

                // Если есть в базе отсылаем сообщение //

                $vk->markAsRead($uid);
                $vk->setActivity($uid);
                sleep(1);

                $repquotes = array ("\"", "\'" );
                $filtered = addslashes(str_replace( $repquotes , '', $value['body'] ));

                $mes = $iii->sendMsg($row['chatid'], urlencode($filtered));

                $send = $vk->sendMessages($value['uid'], strip_tags($mes));


                if ($send['error']['error_code'] == '14' AND $config['antigate'] !== null) {
                    // Загружаем капчу на сервер //
                    file_put_contents("captcha/captcha.jpg", file_get_contents($send['error']['captcha_img']));
                    // Уникальный ID капчи //
                    $captcha['id'] = $send['error']['captcha_sid'];
                    $captcha['key'] = recognize("captcha/captcha.jpg", $config['antigate'], false, "antigate.com");


                    $captcha_array = [
                        'captcha_sid' => $captcha['id'],
                        'captcha_key' => $captcha['key'],
                    ];
// Повторяем отправку вместе с разгаданной капчей //
                    $send = $vk->sendMessages($value['uid'], strip_tags($mes), $captcha_array);



                }

                if ($send['error']['error_code'] == '14' AND $config['antigate'] == null) {

                    file_put_contents("captcha/captcha.jpg", file_get_contents($send['error']['captcha_img']));

                    $captcha['id'] = $send['error']['captcha_sid'];

                    (new \models\Mail())->send($config['email'], 'Капча', 'Капча чувак', "captcha/captcha.jpg");

                }


                // Если нет в базе - добавляем его //
            } else {

                $vkprofileinfo = $vk->getUserInfo($uid);

                $firstname = addslashes($vkprofileinfo['first_name']);
                $secondname = addslashes($vkprofileinfo['last_name']);
                $sex = $vkprofileinfo['sex'];


                $chatid = $iii->initMe($uid, $config['botid']);

                $insert = $link->query("INSERT INTO clients VALUES (null, '$firstname', '$secondname', '$sex', '$chatid', '$uid')") or die("Возникла проблемка..." . mysqli_error($link));

                // Находим новосозданное имя и ID сессии //
                $result2 = $link->query("SELECT firstname, chatid, sex FROM clients WHERE vkid=" . $uid);
                $row2 = mysqli_fetch_array($result2);


                $iii->sendMsg($row2['chatid'], urlencode('!botsetname ' . $row2['firstname']));

                // Устанавливаем пол //
                if ($row2['sex'] == '2') {
                    $iii->sendMsg($row2['chatid'], urlencode('я мальчик'));
                } elseif ($row2['sex'] == '1') {
                    $iii->sendMsg($row2['chatid'], urlencode('я девочка'));
                }

            }

        }


    }
} catch (VK\VKException $error) {
    echo $error->getMessage();
}