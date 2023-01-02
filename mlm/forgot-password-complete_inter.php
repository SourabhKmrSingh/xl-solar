<?php
include_once("inc_config.php");

if($_SESSION['mlm_regid'] != '')
{
	$_SESSION['success_msg'] = "You're Logged In!";
	header("Location: {$base_url}home{$suffix}");
	exit();
}

$email = $validation->input_validate($_POST['email']);
if(!filter_var($email, FILTER_VALIDATE_EMAIL))
{
	$_SESSION['error_msg'] = "Please enter a valid Email-ID!";
	header("Location: {$base_url}forgot-password{$suffix}");
	exit();
}
$membership_id = $validation->input_validate($_POST['membership_id']);
$password = $validation->input_validate(sha1($_POST['password']));
$confirm_password = $validation->input_validate(sha1($_POST['confirm_password']));

if(isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'])
{
	if($email == "" || $password == "" || $confirm_password == "")
	{
		$_SESSION['error_msg'] = "Please fill all required fields!";
		header("Location: {$base_url}forgot-password-complete{$suffix}?email=$email&q=$membership_id");
		exit();
	}

	if($password != $confirm_password)
	{
		$_SESSION['error_msg'] = "Password and Confirm Password should be same!";
		header("Location: {$base_url}forgot-password-complete{$suffix}?email=$email&q=$membership_id");
		exit();
	}

	$nowdatetime =  date('Y-m-d H:i:s');
	$checkResult = $db->view('*', 'mlm_registrations', 'regid', "and email='$email' and membership_id='$membership_id' and expirydatetime >= '{$nowdatetime}' and status='active'");
	if(!$checkResult)
	{
		$_SESSION['error_msg'] = "Error Occurred! Please try again!";
		header("Location: {$base_url}forgot-password-complete{$suffix}?email=$email&q=$membership_id");
		exit();
	}
	if($checkResult['num_rows'] >= 1)
	{
		$checkRow = $checkResult['result'][0];
		$user_ip = $checkRow['user_ip'];
		
		$user_ip_array = ($user_ip!='') ? explode(", ", $validation->input_validate($user_ip)) : array();
		array_push($user_ip_array, $user_ip);
		$user_ip_array = array_unique($user_ip_array);
		$user_ip = implode(", ", $user_ip_array);

		$fields = array('password'=>$password, 'user_ip'=>$user_ip, 'modifytime'=>$createtime, 'modifydate'=>$createdate);
		
		$resetpasswordResult = $db->update("mlm_registrations", $fields, array('email'=>$email, 'membership_id'=>$membership_id));
		if(!$resetpasswordResult)
		{
			$_SESSION['error_msg'] = "Error Occurred! Please try again!";
			header("Location: {$base_url}forgot-password-complete{$suffix}?email=$email&q=$membership_id");
			exit();
		}
		
		$_SESSION['success_msg'] = "You've successfully reset your account's password. Now you can login with your new password!";
		header("Location: {$base_url}login{$suffix}");
		exit();
	}
	else
	{
		$_SESSION['error_msg'] = "There is some problem. Please try again after some time!";
		header("Location: {$base_url}forgot-password-complete{$suffix}?email=$email&q=$membership_id");
		exit();
	}
}
else
{
	$_SESSION['error_msg'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}forgot-password-complete{$suffix}?email=$email&q=$membership_id");
	exit();
}
?>