<?php
include_once("inc_config.php");

$mobile = $validation->input_validate($_POST['mobile']);
$regid_custom = $validation->input_validate($_POST['regid_custom']);
$password = $validation->input_validate(sha1($_POST['password']));
$confirm_password = $validation->input_validate(sha1($_POST['confirm_password']));

if(isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'])
{
	if($mobile == "" || $password == "" || $confirm_password == "")
	{
		$_SESSION['error_msg_fe'] = "Please fill all required fields!";
		header("Location: {$base_url}forgot-password-complete{$suffix}?mobile=$mobile&q=$regid_custom");
		exit();
	}

	if($password != $confirm_password)
	{
		$_SESSION['error_msg_fe'] = "Password and Confirm Password should be same!";
		header("Location: {$base_url}forgot-password-complete{$suffix}?mobile=$mobile&q=$regid_custom");
		exit();
	}

	$nowdatetime =  date('Y-m-d H:i:s');
	$checkResult = $db->view('*', 'rb_registrations', 'regid', "and mobile='$mobile' and regid_custom='$regid_custom' and expirydatetime >= '{$nowdatetime}' and status='active'");
	if(!$checkResult)
	{
		$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
		header("Location: {$base_url}forgot-password-complete{$suffix}?mobile=$mobile&q=$regid_custom");
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
		
		$resetpasswordResult = $db->update("rb_registrations", $fields, array('mobile'=>$mobile, 'regid_custom'=>$regid_custom));
		if(!$resetpasswordResult)
		{
			$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
			header("Location: {$base_url}forgot-password-complete{$suffix}?mobile=$mobile&q=$regid_custom");
			exit();
		}
		
		$_SESSION['success_msg_fe'] = "You've successfully reset your account's password. Now you can login with your new password!";
		header("Location: {$base_url}login{$suffix}");
		exit();
	}
	else
	{
		$_SESSION['error_msg_fe'] = "There is some problem. Please try again after some time!";
		header("Location: {$base_url}forgot-password-complete{$suffix}?mobile=$mobile&q=$regid_custom");
		exit();
	}
}
else
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}forgot-password-complete{$suffix}?mobile=$mobile&q=$regid_custom");
	exit();
}
?>