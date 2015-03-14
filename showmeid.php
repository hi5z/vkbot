<?php
require "config.php";
require "classes.php";


$data = initme($_GET['id'], $config['key'], $config['botid']);
echo $data->result->cuid;