<?php
if(isset($_SESSION['mlm_regid']) and isset($_SESSION['mlm_membership_id']))
{
	// if($_SESSION['mlm_status'] == 'inactive')
	// {
	// 	$_SESSION['error_msg'] = "Your Account is Inactive. Please contact Administrator!";
	// 	$_SESSION['redirect_url'] = $full_url;
	// 	$_SESSION['mlm_regid'] = "";
	// 	$_SESSION['mlm_membership_id'] = "";
	// 	$_SESSION['mlm_status'] = "";
	// 	header("Location: index.php");
	// 	exit();
	// }
}
else
{
	$_SESSION['error_msg'] = "Please login first!";
	$_SESSION['redirect_url'] = $full_url;
	header("Location: index.php");
	exit();
}
?>