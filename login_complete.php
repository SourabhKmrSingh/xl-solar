<?php
include_once("inc_config.php");

if($_SESSION['regid'] != "")
{
	header("Location: {$base_url}home{$suffix}");
	exit();
}

$regid_custom = $validation->urlstring_validate($_GET['q']);
$email = $validation->input_validate($_GET['email']);

if($regid_custom != "" and $email != "")
{
	$regResult = $db->view("regid", "rb_registrations", "regid", "and regid_custom='{$regid_custom}' and email='{$email}'");
	if($regResult['num_rows'] >= 1)
	{
		$update = $db->update("rb_registrations", array('status'=>'active'), array('regid_custom'=>$regid_custom, 'email'=>$email));
		
		$loginResult = $db->view('*', 'rb_registrations', 'regid', "and regid_custom = '$regid_custom' and status = 'active'");
		if(!$loginResult)
		{
			$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
			header("Location: {$base_url}login{$suffix}");
			exit();
		}
		if($loginResult['num_rows'] >= 1)
		{
			$loginRow = $loginResult['result'][0];
			
			$_SESSION['email'] = $loginRow['email'];
			$_SESSION['regid'] = $loginRow['regid'];
			$_SESSION['first_name'] = $loginRow['first_name'];
			
			$fields = array('regid'=>$loginRow['regid'], 'email'=>$loginRow['email'], 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
			$logResult = $db->insert("rb_logdetail_frontend", $fields);
			if(!$logResult)
			{
				$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
				header("Location: {$base_url}login{$suffix}");
				exit();
			}
			
			$_SESSION['success_msg_fe'] = "Complete your profile before proceeding!";
			header("Location: {$base_url}profile{$suffix}");
			exit();
		}
		else
		{
			$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
			header("Location: {$base_url}login{$suffix}");
			exit();
		}
	}
	else
	{
		$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
		header("Location: {$base_url}login{$suffix}");
		exit();
	}
}
else
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}
?>