<?

require_once "classes.php";
require_once "config.php";

// Получаем диалоги //
$input = json_decode(file_get_contents("https://api.vk.com/method/messages.getDialogs?count=10&access_token=" . $token));

// Профиль онлайн, когда скрипт работает //
file_get_contents("https://api.vk.com/method/account.setOnline?access_token=" . $token);


$i = 0;
for ($i = 1; $i <= 10; $i++) {
    // Выводим список последних сообщений // ?>
<div class="panel panel-default">
    <div class="panel-heading">Отправил <?=$input->response[$i]->uid?> в <?=gmdate("Y-m-d\TH:i:s\Z", $input->response[$i]->date)?> <? if ($input->response[$i]->read_state == '0')  {echo '<span class="label label-danger">Не прочитано</span>';} else {echo '<span class="label label-success">Прочитано</span>';} ?> <? if ($input->response[$i]->out == '1') {echo '<span class="label label-primary">Ответ отправлен</span>';}?></div>
    <div class="panel-body">
        <?=$input->response[$i]->body?>
    </div>
</div>
<?

    if ($input->response[$i]->out == '0') {
        // VK USER ID CONST //
        $uid = $input->response[$i]->uid;

        // Проверяем ID в базе //
        $result = $link->query("SELECT * FROM clients WHERE vkid=" . $uid);
        $row = mysqli_fetch_array($result);

        $vkmessage = $input->response[$i]->body;

        /* if ($vkmessage[0] == '/'){
            // Эмулируем прочтение сообщения + набор текста + пауза до отправки //
            file_get_contents("https://api.vk.com/method/messages.markAsRead?access_token=" . $token . "&peer_id=" . $uid);
            sleep(4);
            file_get_contents("https://api.vk.com/method/messages.setActivity?access_token=" . $token . "&user_id=" . $uid . "&type=typing");
            sleep(5);

            $mes = cmd(substr($vkmessage, 1));
            $res = file_get_contents("https://api.vk.com/method/messages.send?access_token=" . $token . "&message=" . urlencode($mes) . "&uid=" . $uid);
        }
        else*/if ($row['vkid'] == $uid) {
            // Эмулируем прочтение сообщения + набор текста + пауза до отправки //
            file_get_contents("https://api.vk.com/method/messages.markAsRead?access_token=" . $token . "&peer_id=" . $uid);
            sleep(4);
            file_get_contents("https://api.vk.com/method/messages.setActivity?access_token=" . $token . "&user_id=" . $uid . "&type=typing");
            sleep(5);

            // Получаем ответ на сообщение //
            $mes = file_get_contents('http://'.$URL.'/sp.php?session=' . $row['chatid'] . '&text=' . urlencode($vkmessage));

            // Отсылаем сообщение //
            $res = file_get_contents("https://api.vk.com/method/messages.send?access_token=" . $token . "&message=" . urlencode($mes) . "&uid=" . $uid);
            sleep(1);
        } else {

            // Если мы еще не общались с этим профилем - добавляем его в базу и отсылаем боту имя //
            $vkprofileinfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?name_case=nom&fields=sex&user_ids=" . $uid), true);
            $firstname = $vkprofileinfo->response[0]->first_name;
            $secondname = $vkprofileinfo->response[0]->last_name;
            $sex = $vkprofileinfo->response[0]->sex;
            $chatid = file_get_contents('http://'.$URL.'/showmeid.php?id=' . $uid);




            $insert = $link->query("INSERT INTO clients VALUES (NULL, '$firstname', '$secondname', '$sex', '$chatid', '$uid')") or die("Возникла проблемка..." . mysqli_error($link));

            // Находим новосозданное имя и ID сессии //
            $result2 = $link->query("SELECT firstname,chatid,sex FROM clients WHERE vkid=" . $uid);
            $row2 = mysqli_fetch_array($result2);

            // Устанавливаем имя //
            file_get_contents('http://'.$URL.'/sp.php?session=' . $row2['chatid'] . '&text=' . urlencode('!botsetname ' . $row2['firstname']));

            // Устанавливаем пол //
            if ($row2['sex'] == '2'){
                file_get_contents('http://'.$URL.'/sp.php?session=' . $row2['chatid'] . '&text=' . urlencode('я мальчик'));
            } elseif ($row2['sex'] == '1') {
                file_get_contents('http://'.$URL.'/sp.php?session=' . $row2['chatid'] . '&text=' . urlencode('я девочка'));
            }


            // Эмулируем прочтение сообщения + набор текста + пауза до отправки //
            file_get_contents("https://api.vk.com/method/messages.markAsRead?access_token=" . $token . "&peer_id=" . $uid);
            sleep(4);
            file_get_contents("https://api.vk.com/method/messages.setActivity?access_token=" . $token . "&user_id=" . $uid . "&type=typing");
            sleep(5);

            // Получаем ответ на сообщение //
            $mes = file_get_contents('http://'.$URL.'/sp.php?session=' . $row['chatid'] . '&text=' . urlencode($vkmessage));

            // Отсылаем сообщение //
            $res = file_get_contents("https://api.vk.com/method/messages.send?access_token=" . $token . "&message=" . urlencode($mes) . "&uid=" . $uid);
            sleep(1);
        }
    }

    // Еще немного timeout для того, чтобы не было капчи //
    sleep(3);
}
