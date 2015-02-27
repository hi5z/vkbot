<?
include_once "classes.php";
include_once "config.php";


$data = initme($_GET['id']."ozoozo222", $key);

echo $data->result->cuid;