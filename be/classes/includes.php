<?php
// server should keep session data for AT LEAST 4 hours
ini_set('session.gc_maxlifetime', 14400);
ini_set('max_execution_time', 123456);
ini_set('memory_limit', '512M');

// each client should remember their session id for EXACTLY 4 hours
session_set_cookie_params(14400);
error_reporting(0);

@ob_start();
@session_start();
include("class.connection.php");
include("class.db.php");
include("class.validation.php");
include("class.media.php");
include("class.pagination.php");
include("class.pagination2.php");
include("class.pagination3.php");
include("class.upload.php");
include("class.mail.php");
include("class.api.php");
$connect->query("SET NAMES 'utf8'");

$configQueryResult = $db->view('*', 'rb_config', 'configid', "", "configid desc");
if(!$configQueryResult)
{
	echo mysqli_error($connect);
	exit();
}
$configRow = $configQueryResult['result'][0];

if($configRow['timezone'] != "")
{
	date_default_timezone_set($validation->db_field_validate($configRow['timezone']));
}

$user_ip = $validation->getuseripaddr();
$random_no = strtoupper(substr(md5(rand(1, 99999)),0,10));
$createtime = date('H:i:s');
$createdate = date('Y-m-d');

//$protocol=strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
$protocol=$_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http';
$domainLink=$protocol.'://'.$_SERVER['HTTP_HOST'];
$full_url = $domainLink.''.$_SERVER['REQUEST_URI'];

header("X-Frame-Options: sameorigin");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: ");
header("Referrer-Policy: no-referrer");
header_remove("X-Powered-By");
?>