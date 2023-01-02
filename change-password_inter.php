<?php
include_once("inc_config.php");

if($_SESSION['regid'] == "")
{
	$_SESSION['error_msg_fe'] = "Login to continue!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}

if(isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'])
{
	$old_password = $validation->input_validate(sha1($_POST['old_password']));
	$password = $validation->input_validate(sha1($_POST['password']));
	$confirm_password = $validation->input_validate(sha1($_POST['confirm_password']));

	if($_POST['old_password'] == "" || $_POST['password'] == "")
	{
		$_SESSION['error_msg_fe'] = "Please fill all required fields!";
		header("Location: {$base_url}change-password{$suffix}");
		exit();
	}
	if($password != $confirm_password)
	{
		$_SESSION['error_msg_fe'] = "Password and Confirm Password should be same!";
		header("Location: {$base_url}change-password{$suffix}");
		exit();
	}

	$checkResult = $db->view('regid,membership_id', 'rb_registrations', 'regid', "and password='$old_password' and regid='$regid'");
	if($checkResult['num_rows'] >= 1)
	{
		$passwordResult = $db->update("rb_registrations", array('password'=>$password, 'modifytime'=>$createtime, 'modifydate'=>$createdate), array('regid'=>$regid));
		if(!$passwordResult)
		{
			$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
			header("Location: {$base_url}change-password{$suffix}");
			exit();
		}
		
		$loginRow = $checkResult['result'][0];
		$membership_id = $loginRow['membership_id'];
		
		$mlmresetpasswordResult = $db->update("mlm_registrations", array('password'=>$password, 'modifytime'=>$createtime, 'modifydate'=>$createdate), array('membership_id'=>$membership_id));
		if(!$mlmresetpasswordResult)
		{
			$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
			header("Location: {$base_url}change-password{$suffix}");
			exit();
		}
	}
	else
	{
		$_SESSION['error_msg_fe'] = "Your old password is incorrect! Please insert the correct one!";
		header("Location: {$base_url}change-password{$suffix}");
		exit();
	}

	unset($_SESSION['email']);
	unset($_SESSION['regid']);
	unset($_SESSION['first_name']);
	session_destroy();

	$_SESSION['success_msg_fe'] = "You've successfully updated your password. Please login with the new password now!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}
else
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}change-password{$suffix}");
	exit();
}
?>