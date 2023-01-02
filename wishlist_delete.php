<?php
include_once("inc_config.php");

$productid = $validation->input_validate($_GET['id']);

if($regid == "")
{
	$_SESSION['error_msg_fe'] = "Please login first to continue!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}

if($productid == "")
{
	$_SESSION['error_msg_fe'] = "Please select atleast one product!";
	header("Location: {$base_url}wishlist{$suffix}");
	exit();
}

$checkResult = $db->view('*', 'rb_wishlist', 'wishlistid', "and regid = '$regid' and productid = '$productid' and status = 'active'");
if($checkResult['num_rows'] >= 1)
{
	$deleteResult = $db->delete("rb_wishlist", array('regid'=>$regid, 'productid'=>$productid));
	if($deleteResult){
		$_SESSION['wishlist_remove_alert'] = 'Item is removed from the Wishlist!';
	}
}


$_SESSION['wishlist_productid'] = "";

//$_SESSION['success_msg_fe'] = "You have successfully added the product in the wishlist!";
header("Location: {$base_url}wishlist{$suffix}");
exit();
?>