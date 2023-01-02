<?php
if(isset($_SESSION['be_userid']) and isset($_SESSION['be_username']))
{
	if($_SESSION['be_status'] == 'inactive')
	{
		$_SESSION['error_msg'] = "Your Account is Inactive. Please contact Administrator!";
		$_SESSION['redirect_url'] = $full_url;
		$_SESSION['be_userid'] = "";
		$_SESSION['be_username'] = "";
		$_SESSION['be_status'] = "";
		header("Location: index.php");
		exit();
	}
}
else
{
	$_SESSION['error_msg'] = "Please login first!";
	$_SESSION['redirect_url'] = $full_url;
	header("Location: index.php");
	exit();
}
?>