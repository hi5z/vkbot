<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);


require_once "classes.php";
require_once "config.php";
require_once "antigate.php";
require_once "vk.php";
require_once "vkexception.php";


$vk_config = array(
    'app_id' => '4798482',
    'api_secret' => 'yat6sCVTs6g4D8nCgWSJ',
    'access_token' => $config['token']
);

try {
    $vk = new VK\VK($vk_config['app_id'], $vk_config['api_secret'], $vk_config['access_token']);


    // Получаем список последних 20 сообщений //
    $messages = $vk->api('messages.getDialogs', array(
        'count' => '12',
    ));

    // Получаем сообщения, на которые мы еще не отвечали //

    // Ставим статус Online //
    if (rand(1, 20) == 10) {
    $setonline = $vk->api('account.setOnline');
    }

    // Выводим сообщения //
    // Отвечаем на 10 сообщений //
    echo '<h3>Последние чаты</h3>';
    $i = 0;
    foreach ((array)$messages['response'] as $key => $value) {
        $i++;

        $uid = $value['uid'];
        $message = $value['body'];

        $vkprofileinfo = $vk->api('users.get', array(
            'name_case' => 'nom',
            'fields' => 'sex,photo_50,bdate,city,country',
            'user_ids' => $uid,
        ));

        if (isset($value['uid'])) {
            ?>

            <div class="panel panel-default">
                <div class="panel-heading"><a href="http://vk.com/id<?= $value['uid'] ?>"><img style="padding-right: 5px; padding-bottom: 3px;" src="<?=$vkprofileinfo['response'][0]['photo_50']?>" align="left" /> <?=$vkprofileinfo['response'][0]['first_name']?> <?=$vkprofileinfo['response'][0]['last_name']?></a><br /> <? if ($value['read_state'] == '0') {
                        echo '<span class="label label-danger">Не прочитано</span>';
                    } else {
                        echo '<span class="label label-success">Прочитано</span>';
                    } ?> <? if ($value['out'] == '1') {
                        echo '<span class="label label-primary">Ответ отправлен</span>';
                    } ?></div>
                <div class="panel-body">
                    <a href="https://vk.com/im?msgid=<?= $value['mid'] ?>&sel=<?= $value['uid'] ?>"><?= $value['body'] ?></a><br />
                    <span class="text-muted">Сообщение оставлено <?= gmdate("Y-m-d H:i:s", $value['date']) ?></span>
                </div>
            </div>
        <? usleep(400000);
        }



        if ($message[0] == '/') {
            $reading = $vk->api('messages.markAsRead', array(
                'peer_id' => $uid,
            ));
            $typing = $vk->api('messages.setActivity', array(
                'type' => 'typing',
                'user_id' => $uid,
            ));
            sleep(rand(1,3));
            $send = $vk->api('messages.send', array(
                'message' => cmd(substr($message, 1)),
                'uid' => $value['uid'],
            ));
        } elseif ($value['out'] == '0' AND !in_array($uid, $debug)) {
            // Сделаем выборку из базы //
            $result = $link->query("SELECT * FROM clients WHERE vkid=" . $uid);
            if ($result != false) {
                $row = mysqli_fetch_array($result);
            }

            if ($row['vkid'] == $uid AND $result != false) {

                // Если есть в базе отсылаем сообщение //

                $reading = $vk->api('messages.markAsRead', array(
                    'peer_id' => $uid,
                ));
                sleep(rand(1,3));
                $typing = $vk->api('messages.setActivity', array(
                    'type' => 'typing',
                    'user_id' => $uid,
                ));
                sleep(rand(1,3));

                $repquotes = array ("\"", "\'" ); // фильтруем сторонние символы
                $filtered = addslashes(str_replace( $repquotes , '', $value['body'] ));
                $mes = file_get_contents($config['url'] . '/sp.php?session=' . $row['chatid'] . '&text=' . urlencode($filtered));
                $send = $vk->api('messages.send', array(
                    'message' => strip_tags($mes),
                    'uid' => $value['uid'],
                ));


                if ($send['error']['error_code'] == '14' AND $config['antigate'] !== null) {
                    // Загружаем капчу на сервер //
                    file_put_contents("captcha/captcha.jpg", file_get_contents($send['error']['captcha_img']));
                    // Уникальный ID капчи //
                    $captcha['id'] = $send['error']['captcha_sid'];
                    $captcha['key'] = recognize("captcha/captcha.jpg", $config['antigate'], false, "antigate.com");

                    // Повторяем отправку вместе с разгаданной капчей //
                    $send = $vk->api('messages.send', array(
                        'message' => strip_tags($mes),
                        'uid' => $value['uid'],
                        'captcha_sid' => $captcha['id'],
                        'captcha_key' => $captcha['key'],
                    ));

                } elseif ($send['error']['error_code'] == '14' AND $config['antigate'] == null){
                    // Загружаем капчу на сервер //
                    file_put_contents("captcha/captcha.jpg", file_get_contents($send['error']['captcha_img']));
                    // Уникальный ID капчи //
                    if (isset($_GET['key'])){
                    $captcha['id'] = $send['error']['captcha_sid'];
                    $captcha['key'] = $_GET['key'];

                        // Повторяем отправку вместе с разгаданной капчей //
                        $send = $vk->api('messages.send', array(
                            'message' => strip_tags($mes),
                            'uid' => $value['uid'],
                            'captcha_sid' => $captcha['id'],
                            'captcha_key' => $captcha['key'],
                        )); ?>
                        <script>history.go(-1);</script>
                        <?
                    }?>
                    <form action="/autoupdate.php" method="get">
                        <img src="captcha/captcha.jpg" align="center"><br />
                        Что на картинке? <input type="text" name="key"><br />
                        <input type="submit" value="Отправить">
                    </form>
<?
                    break;
                }

                // Если нет в базе - добавляем его //
            } else {



                $firstname = addslashes($vkprofileinfo['response'][0]['first_name']);
                $secondname = addslashes($vkprofileinfo['response'][0]['last_name']);
                $city = $vkprofileinfo['response'][0]['city'];
                $country = $vkprofileinfo['response'][0]['country'];
                $bdate = $vkprofileinfo['response'][0]['bdate'];
                $sex = $vkprofileinfo['response'][0]['sex'];
                $chatid = file_get_contents($config['url'] . '/showmeid.php?id=' . $uid);


                $insert = $link->query("INSERT INTO clients (firstname, secondname, city, birthdate, country, sex, chatid, vkid) VALUES ('$firstname', '$secondname', '$city', '$bdate', '$country', '$sex', '$chatid', '$uid')") or die("Возникла проблемка..." . mysqli_error($link));

                // Находим новосозданное имя и ID сессии //
                $result2 = $link->query("SELECT firstname, chatid, sex FROM clients WHERE vkid=" . $uid);
                $row2 = mysqli_fetch_array($result2);

                // Устанавливаем имя //
                file_get_contents($config['url'] . '/sp.php?session=' . $row2['chatid'] . '&text=' . urlencode('!botsetname ' . $firstname));

                // Устанавливаем пол //
                if ($row2['sex'] == '2') {
                    file_get_contents($config['url'] . '/sp.php?session=' . $row2['chatid'] . '&text=' . urlencode('я мальчик'));
                } elseif ($row2['sex'] == '1') {
                    file_get_contents($config['url'] . '/sp.php?session=' . $row2['chatid'] . '&text=' . urlencode('я девочка'));
                }

            }

            sleep(rand(1,3));
        }


    }
} catch (VK\VKException $error) {
    echo $error->getMessage();
}