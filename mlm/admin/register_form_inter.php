<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "register";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: register_view.php");
	exit();
}

if($mode == "edit")
{
	echo $validation->update_permission();
}
else
{
	echo $validation->write_permission();
}

if($mode == "edit")
{
	if(isset($_GET['regid']))
	{
		$regid = $validation->urlstring_validate($_GET['regid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: register_view.php");
		exit();
	}
}



$old_membership_id = $validation->input_validate($_POST['old_membership_id']);
$old_membership_id_value = $validation->input_validate($_POST['old_membership_id_value']);
$member_check = $validation->input_validate($_POST['member_check']);
$sponsor_id = $validation->input_validate($_POST['sponsor_id']);
$planid = $validation->input_validate($_POST['planid']);
if($planid=='')
{
	$planid = 0;
}
$first_name = $validation->input_validate($_POST['first_name']);
$last_name = $validation->input_validate($_POST['last_name']);
$username = $validation->input_validate($_POST['username']);
$email = $validation->input_validate($_POST['email']);
$password = $validation->input_validate(sha1($_POST['password']));
$confirm_password = $validation->input_validate(sha1($_POST['confirm_password']));
$old_password = $validation->input_validate($_POST['old_password']);
$mobile = $validation->input_validate($_POST['mobile']);
$mobile_alter = $validation->input_validate($_POST['mobile_alter']);
$pincode = $validation->input_validate($_POST['pincode']);
$father_name = $validation->input_validate($_POST['father_name']);
$mother_name = $validation->input_validate($_POST['mother_name']);
$date_of_birth = $validation->input_validate($_POST['date_of_birth']);
$address = $validation->input_validate($_POST['address']);

$pan_card_number = $validation->input_validate($_POST['pan_card_number']);
$old_panImage  = $validation->input_validate($_POST['old_panImage']);


if($pincode=='')
{
	$pincode = 0;
}
$bank_name = $validation->input_validate($_POST['bank_name']);
$account_number = $validation->input_validate($_POST['account_number']);
$ifsc_code = $validation->input_validate($_POST['ifsc_code']);
$account_name = $validation->input_validate($_POST['account_name']);
$document = $validation->input_validate($_POST['document']);
$document_number = $validation->input_validate($_POST['document_number']);
$remarks = $validation->input_validate($_POST['remarks']);
$status = $validation->input_validate($_POST['status']);
$old_imgName = $validation->input_validate($_POST['old_imgName']);
$old_kycdoc = $validation->input_validate($_POST['old_kycdoc']);
$old_bankdoc = $validation->input_validate($_POST['old_bankdoc']);

$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

$dupresult = $db->check_duplicates('mlm_registrations', 'regid', $regid, 'pan_card_number', $pan_card_number, "edit");
if($dupresult >= 1)
{
	$_SESSION['error_msg'] = "Pan Card Number already exists!";
	header("Location: register_view.php");
	exit();
}

if($_POST['password'] != "")
{
	if($password != $confirm_password)
	{
		$_SESSION['error_msg'] = "Password and Confirm Password should be Same!";
		header("Location: register_view.php");
		exit();
	}
}


$imgTName = $_FILES['imgName']['name'];
if($imgTName != "")
{
	$handle = new Upload($_FILES['imgName']);
    if($handle->uploaded)
	{
		$handle->file_force_extension = true;
		$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
		$handle->allowed = array('image/*');
		if($configRow['large_width'] != "0" and $configRow['large_height'] != "0")
		{
			$handle->image_resize = true;
			$handle->image_x = $validation->db_field_validate($configRow['large_width']);
			$handle->image_y = $validation->db_field_validate($configRow['large_height']);
			$handle->image_no_enlarging = ($configRow['large_ratio'] === "false") ? false : true;
			$handle->image_ratio = ($configRow['large_ratio'] === "false") ? false : true;
		}
		
		$handle->process(IMG_MAIN_LOC);
		if($handle->processed)
		{
			$imgName = $handle->file_dst_name;
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: register_view.php");
			exit();
		}
		
		// Thumbnail Image
		$handle->file_force_extension = true;
		$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
		$handle->allowed = array('image/*');
		if($configRow['thumb_width'] != "0" and $configRow['thumb_height'] != "0")
		{
			$handle->image_resize = true;
			$handle->image_x = $validation->db_field_validate($configRow['thumb_width']);
			$handle->image_y = $validation->db_field_validate($configRow['thumb_height']);
			$handle->image_no_enlarging = ($configRow['thumb_ratio'] === "false") ? false : true;
			$handle->image_ratio = ($configRow['thumb_ratio'] === "false") ? false : true;
		}
		
		$handle->process(IMG_THUMB_LOC);
		if($handle->processed)
		{
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: register_view.php");
			exit();
		}
		
		$handle-> clean();
	}
	else
	{
		$_SESSION['error_msg'] = $handle->error.'!';
		header("Location: register_view.php");
		exit();
    }
	
	if($mode == "edit")
	{
		$delresult = $media->filedeletion('mlm_registrations', 'regid', $regid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	}
}


// BANK DOCUMENT
$bankdocTName = $_FILES['bankdoc']['name'][0];
if($bankdocTName != "")
{
	$files = array();
	foreach($_FILES['bankdoc'] as $k => $l)
	{
		foreach ($l as $i => $v)
		{
			if(!array_key_exists($i, $files))
			$files[$i] = array();
			$files[$i][$k] = $v;
		}
	}
	
	$bankdoc = array();
	
	foreach ($files as $file)
	{
		$handle = new Upload($file);
		if($handle->uploaded)
		{
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if($configRow['large_width'] != "0" and $configRow['large_height'] != "0")
			{
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['large_width']);
				$handle->image_y = $validation->db_field_validate($configRow['large_height']);
				$handle->image_no_enlarging = ($configRow['large_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['large_ratio'] === "false") ? false : true;
			}
			
			$handle->process(IMG_MAIN_LOC);
			if($handle->processed)
			{
				array_push($bankdoc, $handle->file_dst_name);
			}
			else
			{
				$_SESSION['error_msg'] = $handle->error.'!';
				header("Location: product_view.php");
				exit();
			}
			
			// Thumbnail Image
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if($configRow['thumb_width'] != "0" and $configRow['thumb_height'] != "0")
			{
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['thumb_width']);
				$handle->image_y = $validation->db_field_validate($configRow['thumb_height']);
				$handle->image_no_enlarging = ($configRow['thumb_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['thumb_ratio'] === "false") ? false : true;
			}
			
			$handle->process(IMG_THUMB_LOC);
			if($handle->processed)
			{
			}
			else
			{
				$_SESSION['error_msg'] = $handle->error.'!';
				header("Location: profile.php");
				exit();
			}
			
			$handle-> clean();
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: profile.php");
			exit();
		}
	}
}

$bankdoc = implode(" | ", $bankdoc);
if($old_bankdoc != "")
{
	if($bankdoc != "")
	{
		$bankdoc = $bankdoc.' | '.$old_bankdoc;
	}
	else
	{
		$bankdoc = $old_bankdoc;
	}
}


// KYC DOCUMENT
$kycdocTName = $_FILES['kycdoc']['name'][0];
if($kycdocTName != "")
{
	$files = array();
	foreach($_FILES['kycdoc'] as $k => $l)
	{
		foreach ($l as $i => $v)
		{
			if(!array_key_exists($i, $files))
			$files[$i] = array();
			$files[$i][$k] = $v;
		}
	}
	
	$kycdoc = array();
	
	foreach ($files as $file)
	{
		$handle = new Upload($file);
		if($handle->uploaded)
		{
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if($configRow['large_width'] != "0" and $configRow['large_height'] != "0")
			{
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['large_width']);
				$handle->image_y = $validation->db_field_validate($configRow['large_height']);
				$handle->image_no_enlarging = ($configRow['large_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['large_ratio'] === "false") ? false : true;
			}
			
			$handle->process(IMG_MAIN_LOC);
			if($handle->processed)
			{
				array_push($kycdoc, $handle->file_dst_name);
			}
			else
			{
				$_SESSION['error_msg'] = $handle->error.'!';
				header("Location: product_view.php");
				exit();
			}
			
			// Thumbnail Image
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if($configRow['thumb_width'] != "0" and $configRow['thumb_height'] != "0")
			{
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['thumb_width']);
				$handle->image_y = $validation->db_field_validate($configRow['thumb_height']);
				$handle->image_no_enlarging = ($configRow['thumb_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['thumb_ratio'] === "false") ? false : true;
			}
			
			$handle->process(IMG_THUMB_LOC);
			if($handle->processed)
			{
			}
			else
			{
				$_SESSION['error_msg'] = $handle->error.'!';
				header("Location: profile.php");
				exit();
			}
			
			$handle-> clean();
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: profile.php");
			exit();
		}
	}
}

$kycdoc = implode(" | ", $kycdoc);
if($old_kycdoc != "")
{
	if($kycdoc != "")
	{
		$kycdoc = $kycdoc.' | '.$old_kycdoc;
	}
	else
	{
		$kycdoc = $old_kycdoc;
	}
}

// PAN DOCUMENT
$panImageTName = $_FILES['panImage']['name'][0];
if($panImageTName != "")
{
	$files = array();
	foreach($_FILES['panImage'] as $k => $l)
	{
		foreach ($l as $i => $v)
		{
			if(!array_key_exists($i, $files))
			$files[$i] = array();
			$files[$i][$k] = $v;
		}
	}
	
	$panImage = array();
	
	foreach ($files as $file)
	{
		$handle = new Upload($file);
		if($handle->uploaded)
		{
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if($configRow['large_width'] != "0" and $configRow['large_height'] != "0")
			{
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['large_width']);
				$handle->image_y = $validation->db_field_validate($configRow['large_height']);
				$handle->image_no_enlarging = ($configRow['large_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['large_ratio'] === "false") ? false : true;
			}
			
			$handle->process(IMG_MAIN_LOC);
			if($handle->processed)
			{
				array_push($panImage, $handle->file_dst_name);
			}
			else
			{
				$_SESSION['error_msg'] = $handle->error.'!';
				header("Location: product_view.php");
				exit();
			}
			
			// Thumbnail Image
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if($configRow['thumb_width'] != "0" and $configRow['thumb_height'] != "0")
			{
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['thumb_width']);
				$handle->image_y = $validation->db_field_validate($configRow['thumb_height']);
				$handle->image_no_enlarging = ($configRow['thumb_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['thumb_ratio'] === "false") ? false : true;
			}
			
			$handle->process(IMG_THUMB_LOC);
			if($handle->processed)
			{
			}
			else
			{
				$_SESSION['error_msg'] = $handle->error.'!';
				header("Location: profile.php");
				exit();
			}
			
			$handle-> clean();
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: profile.php");
			exit();
		}
	}
}

$panImage = implode(" | ", $panImage);
if($old_panImage != "")
{
	if($panImage != "")
	{
		$panImage = $panImage.' | '.$old_panImage;
	}
	else
	{
		$panImage = $old_panImage;
	}
}

if($old_membership_id == "")
{
	$membership_id = "";
	$current_year = date('Y');
	$current_month = date('m');
	$refResult = $db->view("MAX(membership_id_value) as membership_id_value", "mlm_registrations", "regid", "");
	$refRow = $refResult['result'][0];
	$membership_id_value = $refRow['membership_id_value'];
	$membership_id_value = $membership_id_value+1;
	$membership_id = sprintf("%03d", $membership_id_value);
	//$membership_id = "BT".$current_year."".$current_month."".$membership_id;
	$membership_id = "SL".$membership_id;
}
else
{
	$membership_id = $old_membership_id;
	$membership_id_value = $old_membership_id_value;
}

if($_POST['password'] == "")
{
	$password = $old_password;
}
if($imgName == "")
{
	$imgName = $old_imgName;
}
if ($signature == "") {
	$signature = $old_signature;
}


$fields = array('membership_id'=>$membership_id, 'membership_id_value'=>$membership_id_value, 'sponsor_id'=>$sponsor_id, 'first_name'=>$first_name, 'last_name'=>$last_name,'father_name'=>$father_name,'mother_name'=>$mother_name,'date_of_birth'=>$date_of_birth,'address'=>$address, 'signature'=>$signature, 'username'=>$username, 'email'=>$email, 'password'=>$password, 'mobile'=>$mobile, 'mobile_alter'=>$mobile_alter, 'pincode'=>$pincode, 'bank_name'=>$bank_name, 'pan_card_number' => $pan_card_number,'panImage'=>$panImage, 'account_number'=>$account_number, 'ifsc_code'=>$ifsc_code, 'account_name'=>$account_name, 'document'=>$document, 'document_number'=>$document_number, 'imgName'=>$imgName,'kycdoc'=>$kycdoc, 'bankdoc'=>$bankdoc, 'remarks'=>$remarks, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$registerQueryResult = $db->insert("mlm_registrations", $fields);
	if(!$registerQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
}
else if($mode == "edit")
{
	if($member_check == "0" and $status == "active")
	{
		$fields['member_check'] = "1";
	}
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$registerQueryResult = $db->update("mlm_registrations", $fields, array('regid'=>$regid));
	
	// mlm link
	// if($status == 'active'){
	// 	if ($registerQueryResult) {
	// 		$fields_user = array('membership_id' => $membership_id, 'sponsor_id' => $sponsor_id);
	// 		$userMembershipidUpdate = $db->update('rb_registrations', $fields_user, array('mobile' => $mobile));
	// 		if (!$userMembershipidUpdate) {
	// 			echo mysqli_error($connect);
	// 			exit();
	// 		}

	// 	}
	// }else{
	// 	if ($registerQueryResult) {
	// 		$fields_user = array('membership_id' => "", 'sponsor_id' => "");
	// 		$userMembershipidUpdate = $db->update('rb_registrations', $fields_user, array('mobile' => $mobile));

	// 		if (!$userMembershipidUpdate) {
	// 			echo mysqli_error($connect);
	// 			exit();
	// 		}
			
	// 	}
	// }
	
	if(!$registerQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: register_view.php$search_filter");
}

$purchaseQueryResult = $db->view('tracking_status,final_price,price,shipping,coupon_discount,taxamount', 'rb_purchases', 'purchaseid', "and membership_id = '$membership_id'", "purchaseid desc");
$purchaseRow = $purchaseQueryResult['result'][0];
//and $purchaseRow['tracking_status'] == "delivered"

$total_amount = $validation->db_field_validate($purchaseRow['price']+$purchaseRow['shipping']-$purchaseRow['coupon_discount']+$purchaseRow['taxamount']);
$discounted_amount = $validation->calculate_discounted_price('1', $total_amount);

header("Location: register_view.php$search_filter");
exit();
?>