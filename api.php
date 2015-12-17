<?php
ini_set('max_execution_time', 600);
error_reporting(E_ALL);
require_once('classes/db.class.php');
require_once('classes/ajax.requests.php');
$ajaxrequest = new ajax();

echo $ajaxrequest->logData();
if ($ajaxrequest->returnData() != '"no"') {
    echo $ajaxrequest->returnData();
}
