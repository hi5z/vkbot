<?
include_once "classes.php";
include_once "config.php";


/** @noinspection PhpVoidFunctionResultUsedInspection */
$data = initme($_GET['id']."ozoozo222", $config['key']);

/** @noinspection PhpUndefinedFieldInspection */
echo $data->result->cuid;