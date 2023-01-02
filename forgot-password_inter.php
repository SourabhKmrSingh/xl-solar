<?php
include_once("inc_config.php");

if(isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'])
{
	$email = $validation->input_validate($_POST['email']);

	if($email == "")
	{
		$_SESSION['error_msg_fe'] = "Please fill all required fields!";
		header("Location: {$base_url}forgot-password{$suffix}");
		exit();
	}
	
	$checkResult = $db->view('*', 'rb_registrations', 'regid', "and email = '$email' and status = 'active'");
	
	if(!$checkResult)
	{
		$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
		header("Location: {$base_url}forgot-password{$suffix}");
		exit();
	}
	if($checkResult['num_rows'] >= 1)
	{
		$checkRow = $checkResult['result'][0];
		$first_name = $checkRow['first_name'];
		$email = $checkRow['email'];
		$regid_custom = $checkRow['regid_custom'];
		$user_ip = $checkRow['user_ip'];
		
		$user_ip_array = ($user_ip!='') ? explode(", ", $validation->input_validate($user_ip)) : array();
		array_push($user_ip_array, $user_ip);
		$user_ip_array = array_unique($user_ip_array);
		$user_ip = implode(", ", $user_ip_array);
		
		$updatetime = time() + 60*60*2;
		$expirydatetime =  date('Y-m-d H:i:s', $updatetime);
		
		$fields = array('user_ip'=>$user_ip, 'expirydatetime'=>$expirydatetime);
		$resetpasswordResult = $db->update("rb_registrations", $fields, array('email'=>$email, 'regid_custom'=>$regid_custom));
		if(!$resetpasswordResult)
		{
			$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
			header("Location: {$base_url}forgot-password{$suffix}");
			exit();
		}
		
		if($email != "")
		{
			$subject = "Account Recovery - Sunlief";
			$message = "<strong>Hi $first_name,</strong><br><br>
						This email is in response to your password request. <a href='{$base_url}forgot-password-complete{$suffix}?mobile=$mobile&mode=jUhYg7Hu2hY12HuKiUhY2bhYhY6h&q=$regid_custom&mode2=kIjYhY786gThUjvFdXsAe57G' style='color: #1AB1D1;'>Click here to set your new password</a>. This link may be used once and is active for 2 hours. If this email is older than 2 hours, you may receive a new link by using Forgot Password from the Sign In page.<br><br>
						Link not working for you? Copy the url below into your browser.<br>
						<a href='{$base_url}forgot-password-complete{$suffix}?mobile=$mobile&mode=jUhYg7Hu2hY12HuKiUhY2bhYhY6h&q=$regid_custom&mode2=kIjYhY786gThUjvFdXsAe57G' style='color: #1AB1D1;'>{$base_url}forgot-password-complete{$suffix}?mobile=$mobile&mode=jUhYg7Hu2hY12HuKiUhY2bhYhY6h&q=$regid_custom&mode2=kIjYhY786gThUjvFdXsAe57G</a><br><br>
						If you did not request for a new password, please <a href='{$base_url}contact{$suffix}' style='color: #1AB1D1;'>click here</a> to contact us immediately.<br><br>
						Thanks and Regards<br>Sunlief
						<br><br>This is an automated email, please do not reply.";
			
			$mail->sendmail(array($email), $subject, $message);
		}
		
		// $recipient_no = $mobile;
		// $message = "Hi $first_name,". PHP_EOL ."". PHP_EOL ."This message is in response to your password request. Here is the link {$base_url}forgot-password-complete{$suffix}?mobile=$mobile&mode=jUhYg7Hu2hY12HuKiUhY2bhYhY6h&q=$regid_custom and the link may be used once and is active for 2 hours. If this SMS is older than 2 hours, you may receive a new link by using Forgot Password from the Sign In page.". PHP_EOL ."". PHP_EOL ."Thank You,". PHP_EOL ."Happy Life Line.";
		// $send = $api->sendSMS('ARIHAN', $recipient_no, $message);
		
		// $_SESSION['success_msg_fe'] = "An SMS has been sent to your mobile no. with instructions on how to reset your password. If you don't receive it within a few minutes, please check that you used the mobile no. for your account and try again or contact us for help.";
		$_SESSION['success_msg_fe'] = "A Mail has been sent to your registered email address with instructions on how to reset your password. If you don't receive it within a few minutes, Please check that you used the right email address for your account and try again or contact us for help."; 
		header("Location: {$base_url}forgot-password{$suffix}");
		exit();
	}
	else
	{
		$_SESSION['error_msg_fe'] = "We're not able to recognize your account. Please enter a valid Mobile No. to send reset password link!";
		header("Location: {$base_url}forgot-password{$suffix}");
		exit();
	}
}
else
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}forgot-password{$suffix}");
	exit();
}
?>