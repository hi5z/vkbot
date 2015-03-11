<?php
/*
$filename - file path to captcha. MUST be local file. URLs not working
$apikey   - account's API key
$rtimeout - delay between captcha status checks
$mtimeout - captcha recognition timeout

$is_verbose - false(commenting OFF),  true(commenting ON)

additional custom parameters for each captcha:
$is_phrase - 0 OR 1 - captcha has 2 or more words
$is_regsense - 0 OR 1 - captcha is case sensetive
$is_numeric -  0 OR 1 - captcha has digits only
$min_len    -  0 is no limit, an integer sets minimum text length
$max_len    -  0 is no limit, an integer sets maximum text length
$is_russian -  0 OR 1 - with flag = 1 captcha will be given to a Russian-speaking worker

usage examples:
$text=recognize("/path/to/file/captcha.jpg","YOUR_KEY_HERE",true, "antigate.com");

$text=recognize("/path/to/file/captcha.jpg","YOUR_KEY_HERE",false, "antigate.com");  

$text=recognize("/path/to/file/captcha.jpg","YOUR_KEY_HERE",false, "antigate.com",1,0,0,5);  

*/

function recognize(
    $filename,
    $apikey,
    $is_verbose = true,
    $sendhost = "antigate.com",
    $rtimeout = 5,
    $mtimeout = 120,
    $is_phrase = 0,
    $is_regsense = 0,
    $is_numeric = 0,
    $min_len = 0,
    $max_len = 0,
    $is_russian = 0)
{
    if (!file_exists($filename)) {
        if ($is_verbose) echo "file $filename not found\n";
        return false;
    }
    $fp = fopen($filename, "r");
    if ($fp != false) {
        $body = "";
        while (!feof($fp)) $body .= fgets($fp, 1024);
        fclose($fp);
        $ext = strtolower(substr($filename, strpos($filename, ".") + 1));
    } else {
        if ($is_verbose) echo "could not read file $filename\n";
        return false;
    }

    if ($ext == "jpg") $conttype = "image/pjpeg";
    if ($ext == "gif") $conttype = "image/gif";
    if ($ext == "png") $conttype = "image/png";


    $boundary = "---------FGf4Fh3fdjGQ148fdh";

    $content = "--$boundary\r\n";
    $content .= "Content-Disposition: form-data; name=\"method\"\r\n";
    $content .= "\r\n";
    $content .= "post\r\n";
    $content .= "--$boundary\r\n";
    $content .= "Content-Disposition: form-data; name=\"key\"\r\n";
    $content .= "\r\n";
    $content .= "$apikey\r\n";
    $content .= "--$boundary\r\n";
    $content .= "Content-Disposition: form-data; name=\"phrase\"\r\n";
    $content .= "\r\n";
    $content .= "$is_phrase\r\n";
    $content .= "--$boundary\r\n";
    $content .= "Content-Disposition: form-data; name=\"regsense\"\r\n";
    $content .= "\r\n";
    $content .= "$is_regsense\r\n";
    $content .= "--$boundary\r\n";
    $content .= "Content-Disposition: form-data; name=\"numeric\"\r\n";
    $content .= "\r\n";
    $content .= "$is_numeric\r\n";
    $content .= "--$boundary\r\n";
    $content .= "Content-Disposition: form-data; name=\"min_len\"\r\n";
    $content .= "\r\n";
    $content .= "$min_len\r\n";
    $content .= "--$boundary\r\n";
    $content .= "Content-Disposition: form-data; name=\"max_len\"\r\n";
    $content .= "\r\n";
    $content .= "$max_len\r\n";
    $content .= "--$boundary\r\n";
    $content .= "Content-Disposition: form-data; name=\"is_russian\"\r\n";
    $content .= "\r\n";
    $content .= "$is_russian\r\n";
    $content .= "--$boundary\r\n";
    $content .= "Content-Disposition: form-data; name=\"file\"; filename=\"capcha.$ext\"\r\n";
    $content .= "Content-Type: $conttype\r\n";
    $content .= "\r\n";
    $content .= $body . "\r\n"; //���� �����
    $content .= "--$boundary--";


    $poststr = "POST http://$sendhost/in.php HTTP/1.0\r\n";
    $poststr .= "Content-Type: multipart/form-data; boundary=$boundary\r\n";
    $poststr .= "Host: $sendhost\r\n";
    $poststr .= "Content-Length: " . strlen($content) . "\r\n\r\n";
    $poststr .= $content;

    // echo $poststr;

    if ($is_verbose) echo "connecting $sendhost...";
    $fp = fsockopen($sendhost, 80, $errno, $errstr, 30);
    if ($fp != false) {
        if ($is_verbose) echo "OK\n";
        if ($is_verbose) echo "sending request " . strlen($poststr) . " bytes...";
        fputs($fp, $poststr);
        if ($is_verbose) echo "OK\n";
        if ($is_verbose) echo "getting response...";
        $resp = "";
        while (!feof($fp)) $resp .= fgets($fp, 1024);
        fclose($fp);
        $result = substr($resp, strpos($resp, "\r\n\r\n") + 4);
        if ($is_verbose) echo "OK\n";
    } else {
        if ($is_verbose) echo "could not connect to anti-captcha\n";
        if ($is_verbose) echo "socket error: $errno ( $errstr )\n";
        return false;
    }

    if (strpos($result, "ERROR") !== false or strpos($result, "<HTML>") !== false) {
        if ($is_verbose) echo "server returned error: $result\n";
        return false;
    } else {
        $ex = explode("|", $result);
        $captcha_id = $ex[1];
        if ($is_verbose) echo "captcha sent, got captcha ID $captcha_id\n";
        $waittime = 0;
        if ($is_verbose) echo "waiting for $rtimeout seconds\n";
        sleep($rtimeout);
        while (true) {
            $result = file_get_contents('http://antigate.com/res.php?key=' . $apikey . '&action=get&id=' . $captcha_id);
            if (strpos($result, 'ERROR') !== false) {
                if ($is_verbose) echo "server returned error: $result\n";
                return false;
            }
            if ($result == "CAPCHA_NOT_READY") {
                if ($is_verbose) echo "captcha is not ready yet\n";
                $waittime += $rtimeout;
                if ($waittime > $mtimeout) {
                    if ($is_verbose) echo "timelimit ($mtimeout) hit\n";
                    break;
                }
                if ($is_verbose) echo "waiting for $rtimeout seconds\n";
                sleep($rtimeout);
            } else {
                $ex = explode('|', $result);
                if (trim($ex[0]) == 'OK') return trim($ex[1]);
            }
        }

        return false;
    }
}

?>