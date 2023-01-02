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

$old_regid_custom = $validation->input_validate($_POST['old_regid_custom']);
$first_name = $validation->input_validate($_POST['first_name']);
$last_name = $validation->input_validate($_POST['last_name']);
$username = $validation->input_validate($_POST['username']);
$email = $validation->input_validate($_POST['email']);
$password = $validation->input_validate(sha1($_POST['password']));
$confirm_password = $validation->input_validate(sha1($_POST['confirm_password']));
$old_password = $validation->input_validate($_POST['old_password']);
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
$remarks = $validation->input_validate($_POST['remarks']);
$status = $validation->input_validate($_POST['status']);
$old_imgName = $validation->input_validate($_POST['old_imgName']);

$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

if($_POST['password'] != "")
{
	if($password != $confirm_password)
	{
		$_SESSION['error_msg'] = "Password and Confirm Password should be Same!";
		header("Location: register_view.php");
		exit();
	}
}

$dupresult = $db->check_duplicates('rb_registrations', 'regid', $regid, 'email', strtolower($email), $mode);
if($dupresult >= 1)
{
	$_SESSION['error_msg'] = "Email-ID already exists!";
	header("Location: register_view.php");
	exit();
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
		$delresult = $media->filedeletion('rb_registrations', 'regid', $regid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	}
}

if($old_regid_custom == "")
{
	$regid_custom = 'IN'.$random_no;
}
else
{
	$regid_custom = $old_regid_custom;
}

if($_POST['password'] == "")
{
	$password = $old_password;
}
if($imgName == "")
{
	$imgName = $old_imgName;
}

$fields = array('regid_custom'=>$regid_custom, 'first_name'=>$first_name, 'last_name'=>$last_name, 'username'=>$username, 'email'=>$email, 'password'=>$password, 'mobile'=>$mobile, 'mobile_alter'=>$mobile_alter, 'gender'=>$gender, 'date_of_birth'=>$date_of_birth, 'address'=>$address, 'landmark'=>$landmark, 'city'=>$city, 'state'=>$state, 'country'=>$country, 'pincode'=>$pincode, 'imgName'=>$imgName, 'remarks'=>$remarks, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$registerQueryResult = $db->insert("rb_registrations", $fields);
	if(!$registerQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: register_view.php");
	exit();
}
else if($mode == "edit")
{
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$registerQueryResult = $db->update("rb_registrations", $fields, array('regid'=>$regid));
	if(!$registerQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: register_view.php$search_filter");
	exit();
}
?>