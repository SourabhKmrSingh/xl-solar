<?php
include_once("inc_config.php");

if($_SESSION['regid'] != "")
{
	header("Location: {$base_url}home{$suffix}");
	exit();
}

if(isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'])
{
	$redirect_url = $_POST['redirect_url'];
	$q = $_POST['q'];
	$membership_id = $validation->input_validate($_POST['membership_id']);
	$password = $validation->input_validate(sha1($_POST['password']));

	if($membership_id == "" || $password == "")
	{
		$_SESSION['error_msg_fe'] = "Please fill all required fields!";
		header("Location: {$base_url}login{$suffix}?url={$full_url}");
		exit();
	}

	
	
	$loginResult = $db->view('*', 'rb_registrations', 'regid', " and membership_id = '$membership_id' and password = '$password'");
	if(!$loginResult)
	{
		$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
		header("Location: {$base_url}login{$suffix}?url={$full_url}");
		exit();
	}
	
	$loginResult2 = $db->view('*', 'rb_registrations', 'regid', " and membership_id = '$membership_id' and password = '$password' and status = 'inactive'");
	if(!$loginResult2)
	{
		$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
		header("Location: {$base_url}login{$suffix}?url={$full_url}");
		exit();
	}
	
	$loginResult3 = $db->view('*', 'rb_registrations', 'regid', " and membership_id = '$membership_id' and password != '$password' and status = 'active'");
	if(!$loginResult3)
	{
		$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
		header("Location: {$base_url}login{$suffix}?url={$full_url}");
		exit();
	}
	
	if($loginResult3['num_rows'] >= 1)
	{
		$_SESSION['error_msg_fe'] = "Please enter correct password!";
		header("Location: {$base_url}login{$suffix}?url={$full_url}");
		exit();
	}
	else if($loginResult['num_rows'] >= 1)
	{
		$loginRow = $loginResult['result'][0];
		
		$_SESSION['email'] = $loginRow['email'];
		$_SESSION['regid'] = $loginRow['regid'];
		$_SESSION['first_name'] = $loginRow['first_name'];
		$_SESSION['last_name'] = $loginRow['last_name'];
		$_SESSION['mobile'] = $loginRow['mobile'];
		$_SESSION['pincode'] = $loginRow['pincode'];
		$_SESSION['membership_id'] = $loginRow['membership_id'];
		
		$mlmResult= $db->view("*", "mlm_registrations", 'regid', " and membership_id = '{$_SESSION['membership_id']}'");
		$_SESSION['mlm_status'] = $mlmResult['result'][0]['status'];
		
		$fields = array('regid'=>$loginRow['regid'], 'email'=>$loginRow['email'], 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
		$logResult = $db->insert("rb_logdetail_frontend", $fields);
		if(!$logResult)
		{
			$_SESSION['error_msg_fe'] = "Error Occurred! Please try again!";
			header("Location: {$base_url}login{$suffix}?url={$full_url}");
			exit();
		}
		$membership_id = $loginRow['membership_id'];
		
		
		if($q == "wishlist")
		{
			$_SESSION['success_msg_fe'] = "";
			header("Location: {$base_url}wishlist_inter.php");
			exit();
		}
		else if($q == "cart")
		{
			$_SESSION['success_msg_fe'] = "";
			header("Location: {$base_url}product-detail_inter.php?q=$q");
			exit();
		}
		else if($q == "buy")
		{
			$_SESSION['success_msg_fe'] = "";
			header("Location: {$base_url}product-detail_inter.php?q=$q");
			exit();
		}
		else if($redirect_url != "")
		{
			$_SESSION['success_msg_fe'] = "";
			header("Location: {$redirect_url}");
			exit();
		}
		else
		{
			if($loginRow['address'] == "")
			{
				$_SESSION['success_msg_fe'] = "Complete your profile before proceeding!";
				header("Location: {$base_url}profile{$suffix}");
				exit();
			}
			else
			{
				header("Location: {$base_url}");
				exit();
			}
		}
	}
	else
	{
		$_SESSION['error_msg_fe'] = "We're not able to recognize your account. Please use right login credentials!";
		header("Location: {$base_url}login{$suffix}?q={$q}&url={$full_url}");
		exit();
	}
}
else
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}login{$suffix}?q={$q}&url={$full_url}");
	exit();
}
?>