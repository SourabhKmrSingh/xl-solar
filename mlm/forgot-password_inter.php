<?php
include_once("inc_config.php");

if($_SESSION['mlm_regid'] != '')
{
	$_SESSION['success_msg'] = "You're Logged In!";
	header("Location: {$base_url}home{$suffix}");
	exit();
}

if(isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'])
{
	$email = $validation->input_validate($_POST['email']);
	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		$_SESSION['error_msg'] = "Please enter a valid Email-ID!";
		header("Location: {$base_url}forgot-password{$suffix}");
		exit();
	}

	if($email == "")
	{
		$_SESSION['error_msg'] = "Please fill all required fields!";
		header("Location: {$base_url}forgot-password{$suffix}");
		exit();
	}

	$checkResult = $db->view('*', 'mlm_registrations', 'regid', "and email = '$email' and status = 'active'");
	if(!$checkResult)
	{
		$_SESSION['error_msg'] = "Error Occurred! Please try again!";
		header("Location: {$base_url}forgot-password{$suffix}");
		exit();
	}
	if($checkResult['num_rows'] >= 1)
	{
		$checkRow = $checkResult['result'][0];
		$first_name = $checkRow['first_name'];
		$membership_id = $checkRow['membership_id'];
		$user_ip = $checkRow['user_ip'];
		
		$user_ip_array = ($user_ip!='') ? explode(", ", $validation->input_validate($user_ip)) : array();
		array_push($user_ip_array, $user_ip);
		$user_ip_array = array_unique($user_ip_array);
		$user_ip = implode(", ", $user_ip_array);
		
		$updatetime = time() + 60*60*2;
		$expirydatetime =  date('Y-m-d H:i:s', $updatetime);
		
		$fields = array('user_ip'=>$user_ip, 'expirydatetime'=>$expirydatetime);
		$resetpasswordResult = $db->update("mlm_registrations", $fields, array('email'=>$email, 'membership_id'=>$membership_id));
		if(!$resetpasswordResult)
		{
			$_SESSION['error_msg'] = "Error Occurred! Please try again!";
			header("Location: {$base_url}forgot-password{$suffix}");
			exit();
		}
		
		$subject = "Account Recovery - Sunliefs";
		$message = "<strong>Hi $first_name,</strong><br><br>
					This email is in response to your password request. <a href='{$base_url}forgot-password-complete{$suffix}?email=$email&mode=jUhYg7Hu2hY12HuKiUhY2bhYhY6h&q=$membership_id&mode2=kIjYhY786gThUjvFdXsAe57G' style='color: #1AB1D1;'>Click here to set your new password</a>. This link may be used once and is active for 2 hours. If this email is older than 15 minutes, you may receive a new link by using Forgot Password from the Sign In page.<br><br>
					Link not working for you? Copy the url below into your browser.<br>
					<a href='{$base_url}forgot-password-complete{$suffix}?email=$email&mode=jUhYg7Hu2hY12HuKiUhY2bhYhY6h&q=$membership_id&mode2=kIjYhY786gThUjvFdXsAe57G' style='color: #1AB1D1;'>{$base_url}forgot-password-complete{$suffix}?email=$email&mode=jUhYg7Hu2hY12HuKiUhY2bhYhY6h&q=$membership_id&mode2=kIjYhY786gThUjvFdXsAe57G</a><br><br>
					If you did not request for a new password, please <a href='{$base_url}contact{$suffix}' style='color: #1AB1D1;'>click here</a> to contact us immediately.<br><br>
					Thanks and Regards<br>Sunlief
					<br><br>This is an automated email, please do not reply.";
		$mail->sendmail(array($email), $subject, $message);
		
		$_SESSION['success_msg'] = "An email has been sent to your email address with instructions on how to reset your password. If you don't receive it within a few minutes, please check that you used the e-mail address for your account and try again or contact us for help.";
		header("Location: {$base_url}");
		exit();
	}
	else
	{
		$_SESSION['error_msg'] = "We're not able to recognize your account. Please enter a valid Email-ID to send reset password link!";
		header("Location: {$base_url}forgot-password{$suffix}");
		exit();
	}
}
else
{
	$_SESSION['error_msg'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}forgot-password{$suffix}");
	exit();
}
?>