<?php
include_once("admin/classes/includes.php");

define("CMS_BASE_URL", $validation->db_field_validate($configRow['cms_url']));
define("BASE_URL", $validation->db_field_validate($configRow['site_url']));
define("BASE_URL_WEB", $validation->db_field_validate($configRow['site_url_web']));
define("SUFFIX", $validation->db_field_validate($configRow['site_url_extension']));
define("FILE_LOC", "content/");
define("IMG_MAIN_LOC", "content/");
define("IMG_THUMB_LOC", "content/thumb/");
define("IMG_LOC", "uploads/");

$base_url = BASE_URL;
$suffix = SUFFIX;
$base_url_web = BASE_URL_WEB;
$_SESSION['image_maxsize'] = $validation->db_field_validate($configRow['image_maxsize']);
@$regid = $_SESSION['mlm_regid'];

if($_SESSION['mlm_regid'] != '')
{
	$_SESSION['redirect_url'] = "";
}

$err_redirect_url = BASE_URL."error".SUFFIX;
if(strpos($full_url,'%3C') !== false)
{
	header("Location: $err_redirect_url");
	exit();
}
else if(strpos($full_url,'%3E') !== false)
{
	header("Location: $err_redirect_url");
	exit();
}
else if(strpos($full_url,'[') !== false)
{
	header("Location: $err_redirect_url");
	exit();
}
else if(strpos($full_url,']') !== false)
{
	header("Location: $err_redirect_url");
	exit();
}
else if(strpos($full_url,'%22') !== false)
{
	header("Location: $err_redirect_url");
	exit();
}
else if(strpos($full_url,'javascript') !== false)
{
	header("Location: $err_redirect_url");
	exit();
}
?>