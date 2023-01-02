<?php
include_once("classes/includes.php");

define("CMS_BASE_URL", $validation->db_field_validate($configRow['cms_url']));
define("BASE_URL", $validation->db_field_validate($configRow['site_url']));
define("SUFFIX", $validation->db_field_validate($configRow['site_url_extension']));
define("FILE_LOC", "../content/");
define("IMG_MAIN_LOC", "../content/");
define("IMG_THUMB_LOC", "../content/thumb/");
define("IMG_LOC", "../uploads/");

$_SESSION['image_maxsize'] = $validation->db_field_validate($configRow['image_maxsize']);

@$userid = $_SESSION['be_userid'];

if($_SESSION['be_userid'] != '')
{
	$_SESSION['redirect_url'] = "";
}

$tracking_msgs = array(
				"ordered"=>"Your order has been placed.",
				"packed"=>"Seller has processed your order.",
				"shipped"=>"You order has been shipped.",
				"delivered"=>"Package was delivered directly to the customer.",
				"cancelled"=>"Your order has been cancelled.",
				);

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