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
	$sponsor_id = $validation->input_validate($_POST['sponsor_id']);
	$sponsor_name = $validation->input_validate($_POST['sponsor_name']);
	$first_name = $validation->input_validate($_POST['first_name']);
	$last_name = $validation->input_validate($_POST['last_name']);
	$username = $validation->input_validate($_POST['username']);
	$email = $validation->input_validate($_POST['email']);
	$password = $validation->input_validate(sha1($_POST['password']));
	$confirm_password = $validation->input_validate(sha1($_POST['confirm_password']));
	$mobile = $validation->input_validate($_POST['mobile']);
	$pincode = $validation->input_validate($_POST['pincode']);
	$father_name = $validation->input_validate($_POST['father_name']);
	$mother_name = $validation->input_validate($_POST['mother_name']);
	$date_of_birth = $validation->input_validate($_POST['date_of_birth']);
	$address = $validation->input_validate($_POST['address']);

	if($pincode=='')
	{
		$pincode = 0;
	}
	// $bank_name = $validation->input_validate($_POST['bank_name']);
	// $account_number = $validation->input_validate($_POST['account_number']);
	// $ifsc_code = $validation->input_validate($_POST['ifsc_code']);
	// $account_name = $validation->input_validate($_POST['account_name']);
	// $document = $validation->input_validate($_POST['document']);
	// $document_number = $validation->input_validate($_POST['document_number']);
	$rewardid = "0";
	$status = "active";
	
	$registerplanResult = $db->view('regid,planid', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id'", 'regid desc');
	if($registerplanResult['num_rows'] >= 1)
	{
		$registerplanRow = $registerplanResult['result'][0];
		$planid = $registerplanRow['planid'];
	}
	
	if($sponsor_id == "" || $sponsor_name == "" || $first_name == "" || $username == "" || $email == "" || $password == "" || $confirm_password == "" || $mobile == "")
	{
		$_SESSION['error_msg'] = "Please fill all required fields!";
		header("Location: {$base_url}register{$suffix}");
		exit();
	}
	if($password != $confirm_password)
	{
		$_SESSION['error_msg'] = "Password and Confirm Password should be same!";
		header("Location: {$base_url}register{$suffix}");
		exit();
	}
	
	// $userlimitResult = $db->check_duplicates('mlm_registrations', 'regid', $regid, 'sponsor_id', strtolower($sponsor_id), "insert");
	// if($userlimitResult >= 3)
	// {
		// $_SESSION['error_msg'] = "You can only add only 3 members in your downline. Please motivate your team members so that you'll get their benefits";
		// header("Location: {$base_url}register{$suffix}");
		// exit();
	// }
	
	// if($account_number != "")
	// {
	// 	$dupresult2 = $db->check_duplicates('mlm_registrations', 'regid', $regid, 'account_number', strtolower($account_number), "insert");
	// 	if($dupresult2 >= 2)
	// 	{
	// 		$_SESSION['error_msg'] = "Bank Account Number is already in use. Please take another one!";
	// 		header("Location: {$base_url}register{$suffix}");
	// 		exit();
	// 	}
	// }
	
	// if($document != "" and $document_number != "")
	// {
	// 	$dupresult4 = $db->view('regid', 'mlm_registrations', 'regid', "and document='$document' and document_number='$document_number'");
	// 	if($dupresult4['num_rows'] >= 1)
	// 	{
	// 		$_SESSION['error_msg'] = "KYC Document is already in use. Please take another one!";
	// 		header("Location: {$base_url}register{$suffix}");
	// 		exit();
	// 	}
	// }

	

	// Signature upload 

	// $signatureTName = $_FILES['signature']['name'];
	// if ($signatureTName != "") {
	// 	$handle = new Upload($_FILES['signature']);
	// 	if ($handle->uploaded) {
	// 		$handle->file_force_extension = true;
	// 		$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
	// 		$handle->allowed = array('image/*');
	// 		if ($configRow['large_width'] != "0" and $configRow['large_height'] != "0") {
	// 			$handle->image_resize = true;
	// 			$handle->image_x = $validation->db_field_validate($configRow['large_width']);
	// 			$handle->image_y = $validation->db_field_validate($configRow['large_height']);
	// 			$handle->image_no_enlarging = ($configRow['large_ratio'] === "false") ? false : true;
	// 			$handle->image_ratio = ($configRow['large_ratio'] === "false") ? false : true;
	// 		}

	// 		$handle->process(IMG_MAIN_LOC);
	// 		if ($handle->processed) {
	// 			$signature = $handle->file_dst_name;
	// 		} else {
	// 			$_SESSION['error_msg'] = $handle->error . '!';
	// 			header("Location: register.php");
	// 			exit();
	// 		}

	// 		// Thumbnail Image
	// 		$handle->file_force_extension = true;
	// 		$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
	// 		$handle->allowed = array('image/*');
	// 		if ($configRow['thumb_width'] != "0" and $configRow['thumb_height'] != "0") {
	// 			$handle->image_resize = true;
	// 			$handle->image_x = $validation->db_field_validate($configRow['thumb_width']);
	// 			$handle->image_y = $validation->db_field_validate($configRow['thumb_height']);
	// 			$handle->image_no_enlarging = ($configRow['thumb_ratio'] === "false") ? false : true;
	// 			$handle->image_ratio = ($configRow['thumb_ratio'] === "false") ? false : true;
	// 		}

	// 		$handle->process(IMG_THUMB_LOC);
	// 		if ($handle->processed) {
	// 		} else {
	// 			$_SESSION['error_msg'] = $handle->error . '!';
	// 			header("Location: register.php");
	// 			exit();
	// 		}

	// 		$handle->clean();
	// 	} else {
	// 		$_SESSION['error_msg'] = $handle->error . '!';
	// 		header("Location: register.php");
	// 		exit();
	// 	}
	// }

	// profile pic upload 
	$imgTName = $_FILES['imgName']['name'];
	if ($imgTName != "") {
		$handle = new Upload($_FILES['imgName']);
		if ($handle->uploaded) {
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if ($configRow['large_width'] != "0" and $configRow['large_height'] != "0") {
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['large_width']);
				$handle->image_y = $validation->db_field_validate($configRow['large_height']);
				$handle->image_no_enlarging = ($configRow['large_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['large_ratio'] === "false") ? false : true;
			}

			$handle->process(IMG_MAIN_LOC);
			if ($handle->processed) {
				$imgName = $handle->file_dst_name;
			} else {
				$_SESSION['error_msg'] = $handle->error . '!';
				header("Location: register.php");
				exit();
			}

			// Thumbnail Image
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if ($configRow['thumb_width'] != "0" and $configRow['thumb_height'] != "0") {
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['thumb_width']);
				$handle->image_y = $validation->db_field_validate($configRow['thumb_height']);
				$handle->image_no_enlarging = ($configRow['thumb_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['thumb_ratio'] === "false") ? false : true;
			}

			$handle->process(IMG_THUMB_LOC);
			if ($handle->processed) {
			} else {
				$_SESSION['error_msg'] = $handle->error . '!';
				header("Location: register.php");
				exit();
			}

			$handle->clean();
		} else {
			$_SESSION['error_msg'] = $handle->error . '!';
			header("Location: register.php");
			exit();
		}
	}


	$signature ="";
	$membership_id = "";
	$current_year = date('Y');
	$current_month = date('m');
	$refResult = $db->view("MAX(membership_id_value) as membership_id_value", "mlm_registrations", "regid", "");
	$refRow = $refResult['result'][0];
	$membership_id_value = $refRow['membership_id_value'];
	$membership_id_value = $membership_id_value+1;
	$membership_id = $membership_id_value;
	//$membership_id = "BT".$current_year."".$current_month."".$membership_id;
	$membership_id = "XLS".$membership_id . rand(1, 99999); 

	$membership_id = substr($membership_id,0,5);


	// if($membership_id != "")
	// {
	// 	$dupresult2 = $db->check_duplicates('regid', 'mlm_registrations', 'regid', " and membership_id='$membership_id'");
	// 	if($dupresult2 >= 1)
	// 	{
	// 		$_SESSION['error_msg'] = "There is some problem. Please try again later!";
	// 		header("Location: {$base_url}register{$suffix}");
	// 		exit();
	// 	}
	// }
	
	$fields = array('membership_id'=>$membership_id, 'membership_id_value'=>$membership_id_value, 'rewardid'=>$rewardid, 'sponsor_id'=>$sponsor_id, 'sponsor_name'=>$sponsor_name, 'planid'=>$planid, 'first_name'=>$first_name, 'last_name'=>$last_name,'father_name'=> $father_name,'mother_name'=>$mother_name,'date_of_birth'=>$date_of_birth,'address'=>$address,'signature'=>$signature, 'username'=>$username, 'email'=>$email, 'password'=>$password, 'mobile'=>$mobile, 'mobile_alter'=>$mobile_alter, 'pincode'=>$pincode,  'imgName' => $imgName, 'status'=>$status, 'user_ip'=>$user_ip);
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;

	$registerResult = $db->insert("mlm_registrations", $fields);

	$regid_custom = 'XLS'.$random_no;

	$fields = array('regid_custom'=>$regid_custom,'first_name'=>$first_name,'membership_id'=>$membership_id,'sponsor_id'=>$sponsor_id, 'last_name'=>$last_name, 'email'=>$email, 'password'=>$password, 'mobile'=>$mobile, 'address'=>$address, 'pincode'=>$pincode, 'status'=>$status, 'user_ip'=>$user_ip);
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;

	$registerResult = $db->insert("rb_registrations", $fields);

	if(!$registerResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	$passwordShow = $_POST['password'];

	// $message = "Hi {$first_name} You are successfully registered with SUNLIEF. Your Membership ID is {$membership_id} and Password is {$passwordShow}. Please do not share this information with anyone. Thank You, SUNLIEF E-COMMERCE AND SERVICES PRIVATE LIMITED";



	// $send = $api->sendSMS($mobile,$message,'','1707162019393314443');
	
	// if($email != "")
	// {
	// 	$subject = "Welcome to Sunlief";
	// 	$message = "Dear $first_name,<br><br>
	// 				Your Account has been created. You membership ID is {$membership_id} and password is {$passwordShow}. Please click on the given link to login into your account.<br>
	// 				<a href='{$base_url}login{$suffix}' style='color: #1AB1D1;'>Click here to login into your account</a><br><br>
					
	// 				Thanks and Regards<br>Sunlief
	// 				<br><br>This is an automated email, please do not reply.";
		
	// 	$mail->sendmail(array($email), $subject, $message);
	// }
	
	
	
	$_SESSION['register_msg'] = "Your account has been successfully created. Your membership ID is <b>{$membership_id}</b> and password is <b>{$passwordShow}</b>. Thank You!";
	header("Location: {$base_url}");
	exit();
}
else
{
	$_SESSION['error_msg'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}register{$suffix}");
	exit();
}
?>