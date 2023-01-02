<?php
include_once("inc_config.php");

if($_SESSION['regid'] != "")
{
	header("Location: {$base_url}home{$suffix}");
	exit();
}

if(isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'])
{

	$first_name = $validation->input_validate($_POST['first_name']);
	$last_name = $validation->input_validate($_POST['last_name']);
	$email = $validation->input_validate($_POST['email']);
	if($email != "")
	{
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$_SESSION['error_msg_fe'] = "Please enter a valid Email-ID!";
			header("Location: {$base_url}register{$suffix}");
			exit();
		}
	}
	$password = $validation->input_validate(sha1($_POST['password']));
	$confirm_password = $validation->input_validate(sha1($_POST['confirm_password']));
	$mobile = $validation->input_validate($_POST['mobile']);
	$address = $validation->input_validate($_POST['address']);
	$city = $validation->input_validate($_POST['city']);
	$state = $validation->input_validate($_POST['state']);
	$country = $validation->input_validate($_POST['country']);
	$pincode = $validation->input_validate($_POST['pincode']);
	if($pincode=='')
	{
		$pincode = 0;
	}
	$status = "active";
	$regid_custom = 'SL'.$random_no;
	
	if($first_name == "" || $password == "" || $confirm_password == "" || $mobile == "")
	{
		$_SESSION['error_msg_fe'] = "Please fill all required fields!";
		header("Location: {$base_url}register{$suffix}");
		exit();
	}
	if($password != $confirm_password)
	{
		$_SESSION['error_msg_fe'] = "Password and Confirm Password should be same!";
		header("Location: {$base_url}register{$suffix}");
		exit();
	}
	
	
	if($email != "")
	{
		$dupresult = $db->check_duplicates('rb_registrations', 'regid', $regid, 'email', strtolower($email), "insert");
		if($dupresult >= 1)
		{
			$_SESSION['error_msg_fe'] = "Email-ID is already in use. Please take another one!";
			header("Location: {$base_url}register{$suffix}");
			exit();
		}
	}
	
	$dupresult2 = $db->check_duplicates('rb_registrations', 'regid', $regid, 'mobile', strtolower($mobile), "insert");
	if($dupresult2 >= 1)
	{
		$_SESSION['error_msg_fe'] = "Mobile No. is already in use. Please take another one!";
		header("Location: {$base_url}register{$suffix}");
		exit();
	}
	
	$fields = array('regid_custom'=>$regid_custom,'first_name'=>$first_name, 'last_name'=>$last_name, 'email'=>$email, 'password'=>$password, 'mobile'=>$mobile, 'address'=>$address, 'landmark'=>$landmark, 'city'=>$city, 'state'=>$state, 'country'=>$country, 'pincode'=>$pincode, 'status'=>$status, 'user_ip'=>$user_ip);
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;

	$registerResult = $db->insert("rb_registrations", $fields);
	if(!$registerResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	
	/*
	if($mobile != "")
	{
		$recipient_no = $mobile;
		$message = "Hi {$first_name}, Thank You for registering with us. You're now a member of SKY Capital and your ID is {$membership_id}. You can now enjoy all the benefits.";
		$send = $api->sendSMS('ARIHAN', $recipient_no, $message);
	}
	*/
	
	// if($email != "")
	// {
	// 	$subject = "Welcome to Sunlief";
	// 	$message = "Dear $first_name,<br><br>
	// 				Your Account is successfully created. You can now enjoy all the benefits, please check out the link of our products.<br>
	// 				<a href='{$base_url}products{$suffix}' style='color: #1AB1D1;'>Click here to check out our latest products</a><br><br>
	// 				Link not working for you? Copy the url below into your browser.<br>
	// 				<a href='{$base_url}products{$suffix}' style='color: #1AB1D1;'>{$base_url}products{$suffix}</a><br><br>
	// 				Thanks and Regards<br>Sky
	// 				<br><br>This is an automated email, please do not reply.";
					
	// 	$mail->sendmail(array($email), $subject, $message);

	// }
	
	if($userid == "")
	{
		$userid = 0;
	}
	
	$_SESSION['success_msg_fe'] = "You're now successfully registred with us. Thank You!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}
else
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}register{$suffix}");
	exit();
}
?>