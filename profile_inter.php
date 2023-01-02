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
	$membership_id = $validation->input_validate($_POST['membership_id']);
	$sponsor_id = $validation->input_validate($_POST['sponsor_id']);
	$first_name = $validation->input_validate($_POST['first_name']);
	$last_name = $validation->input_validate($_POST['last_name']);
	$mobile = $validation->input_validate($_POST['mobile']);
	$mobile_alter = $validation->input_validate($_POST['mobile_alter']);
	$gender = $validation->input_validate($_POST['gender']);
	$date_of_birth = $validation->input_validate($_POST['date_of_birth']);
	$address = $validation->input_validate($_POST['address']);
	$landmark = $validation->input_validate($_POST['landmark']);
	$city = $validation->input_validate($_POST['city']);
	$state = $validation->input_validate($_POST['state']);
	$country = $validation->input_validate($_POST['country']);
	$pincode = $validation->input_validate($_POST['pincode']);
	if($pincode=='')
	{
		$pincode = 0;
	}
	
	if($first_name == "" || $mobile == "" || $gender == "" || $date_of_birth == "" || $address == "" || $country == "" || $pincode == "")
	{
		$_SESSION['error_msg_fe'] = "Please fill all required fields!";
		header("Location: {$base_url}profile{$suffix}");
		exit();
	}

	$fields = array('membership_id'=>$membership_id, 'sponsor_id'=>$sponsor_id, 'first_name'=>$first_name, 'last_name'=>$last_name, 'mobile'=>$mobile, 'mobile_alter'=>$mobile_alter, 'gender'=>$gender, 'date_of_birth'=>$date_of_birth, 'address'=>$address, 'landmark'=>$landmark, 'city'=>$city, 'state'=>$state, 'country'=>$country, 'pincode'=>$pincode, 'user_ip'=>$user_ip);
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;

	$profileResult = $db->update("rb_registrations", $fields, array('regid'=>$regid));
	if(!$profileResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	$_SESSION['pincode'] = $pincode;

	$_SESSION['success_msg_fe'] = "You've successfully updated your profile.";
	header("Location: {$base_url}profile{$suffix}");
	exit();
}
else
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}profile{$suffix}");
	exit();
}
?>