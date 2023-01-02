<?php
include_once("inc_config.php");

if(isset($_SESSION['email']) and isset($_SESSION['regid']))
{
	unset($_SESSION['email']);
	unset($_SESSION['regid']);
	unset($_SESSION['first_name']);
	unset($_SESSION['last_name']);
	unset($_SESSION['mobile']);
	unset($_SESSION['pincode']);
	session_destroy();

	header("Location: /login");
	exit;
}
?>
