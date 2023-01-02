<?php
include_once("inc_config.php");

if($_SESSION['regid'] != "")
{
	header("Location: {$base_url}home{$suffix}");
	exit();
}

$email = $validation->urlstring_validate($_GET['email']);
if(!filter_var($email, FILTER_VALIDATE_EMAIL))
{
	$_SESSION['error_msg_fe'] = "Please enter a valid Email-ID!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}
$regid_custom = $validation->urlstring_validate($_GET['q']);

if($email == "" || $regid_custom == "")
{
	$_SESSION['error_msg_fe'] = "Please fill all required fields!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}

$checkResult = $db->view('*', 'rb_registrations', 'regid', "and email = '$email' and regid_custom = '$regid_custom'");
if(!$checkResult)
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}
if($checkResult['num_rows'] >= 1)
{
	$checkRow = $checkResult['result'][0];
	$first_name = $checkRow['first_name'];
	
	$subject = "Complete your registration with Bertol Kitchenware";
	$message = "Dear $first_name,<br><br>
				Your Account has almost been created. You are just one step away to log in to your panel. Please click on the given link to confirm your email and activate your account.<br>
				<a href='{$base_url}login_complete{$suffix}?email=$email&mode=jUhYg7Hu2hY12HuKiUhY2bhYhY6h&q=$regid_custom&mode2=kIjYhY786gThUjvFdXsAe57G' style='color: #1AB1D1;'>Click here to confirm your Email</a><br><br>
				Thanks and Regards<br>Bertol Kitchenware
				<br><br>This is an automated email, please do not reply.";
	
	$mail->sendmail(array($email), $subject, $message);
	
	$_SESSION['success_msg_fe'] = "The confirmation email has been resent on your email id. Please check your email and confirm to login. In case if you don't find it in your inbox please check your SPAM FOLDER also. Thankyou!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}
else
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}
?>