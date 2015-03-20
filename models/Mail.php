<?php
/**
 * @package vkbotphp
 * @author Dmitriy Kuts <me@exileed.com>
 * @date 3/20/2015
 * @time 2:23 PM
 * @link http://exileed.com
 */

namespace models;


class Mail
{


    public function send($to, $subject, $message, $filename = null)
    {


        $boundary = "---";
        $headers = "Content-Type: multipart/mixed; boundary=\"$boundary\"";
        $body = "--$boundary\n";
        $body .= "Content-type: text/html; charset='utf-8'\n";
        $body .= "Content-Transfer-Encoding: quoted-printablenn";

        if ($filename != null)
            $body .= "Content-Disposition: attachment; filename==?utf-8?B?" . base64_encode($filename) . "?=\n\n";

        $body .= $message . "\n";
        $body .= "--$boundary\n";

        if ($filename != null) {
            $file = fopen($filename, "r");
            $text = fread($file, filesize($filename));
            fclose($file);
            $body .= "Content-Type: application/octet-stream; name==?utf-8?B?" . base64_encode($filename) . "?=\n";
            $body .= "Content-Transfer-Encoding: base64\n";
            $body .= "Content-Disposition: attachment; filename==?utf-8?B?" . base64_encode($filename) . "?=\n\n";
        }
        $body .= chunk_split(base64_encode($message)) . "\n";
        $body .= "--" . $boundary . "--\n";
        mail($to, $subject, $body, $headers);


    }


}