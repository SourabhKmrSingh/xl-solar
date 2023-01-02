<?php
include_once("inc_config.php");

if(isset($_POST['verification_code']))
{
	$mobile = $validation->input_validate($_POST['mobile']);
	$verification_code = $validation->input_validate($_POST['verification_code']);
	
	if($_SESSION['mobile'] == $mobile and $_SESSION['verification_code'] == $verification_code)
	{
		echo "Done";
		$_SESSION['verification_code'] = "";
		$_SESSION['mobile'] = "";
	}
	else
	{
		echo "Failed";
	}
	exit();
}
?>