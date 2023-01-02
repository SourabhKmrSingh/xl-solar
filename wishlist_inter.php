<?php
include_once("inc_config.php");

if($_SESSION['wishlist_productid'] != "")
{
	$productid = $_SESSION['wishlist_productid'];
}
else
{
	$productid = $validation->input_validate($_GET['id']);
}
$redirect_url = $validation->input_validate($_GET['q']);
$status = "active";

if($regid == "")
{
	$_SESSION['wishlist_productid'] = $productid;
	$_SESSION['error_msg_fe'] = "Please login first to continue!";
	header("Location: {$base_url}login{$suffix}?q=wishlist");
	exit();
}

if($productid == "")
{
	//$_SESSION['error_msg_fe'] = "Please select atleast one product!";
	header("Location: {$base_url}products{$suffix}");
	exit();
}

$checkResult = $db->view('*', 'rb_wishlist', 'wishlistid', "and regid = '$regid' and productid = '$productid' and status = 'active'");
if($checkResult['num_rows'] == 0)
{
	$fields = array('regid'=>$regid, 'productid'=>$productid, 'status'=>$status, 'user_ip'=>$user_ip);
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;

	$wishlistResult = $db->insert("rb_wishlist", $fields);
	if(!$wishlistResult)
	{
		echo mysqli_error($connect);
		exit();
	}
}

$_SESSION['wishlist_productid'] = "";

if($redirect_url != "")
{
	$_SESSION['notify_success_msg_fe'] = "You have successfully added product in the wishlist!";
	header("Location: {$redirect_url}");
}
else
{
	$_SESSION['success_msg_fe'] = "";
	header("Location: {$base_url}wishlist{$suffix}");
}
exit();
?>