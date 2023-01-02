<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "";

echo $validation->admin_permission();

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: config.php");
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
	if(isset($_GET['configid']))
	{
		$configid = $validation->urlstring_validate($_GET['configid']);
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: config.php");
		exit();
	}
}

$cms_title = $validation->input_validate($_POST['cms_title']);
$cms_url = $validation->input_validate($_POST['cms_url']);
$meta_title = $validation->input_validate($_POST['meta_title']);
$meta_keywords = $validation->input_validate($_POST['meta_keywords']);
$meta_description = $validation->input_validate($_POST['meta_description']);
$site_url = $validation->input_validate($_POST['site_url']);
$site_url_extension = $validation->input_validate($_POST['site_url_extension']);
$site_url_web = $validation->input_validate($_POST['site_url_web']);
$script = $validation->input_validate($_POST['script']);
$style = $validation->input_validate($_POST['style']);
$old_logo = $validation->input_validate($_POST['old_logo']);
$old_favicon = $validation->input_validate($_POST['old_favicon']);
$timezone = $validation->input_validate($_POST['timezone']);
$date_format = $validation->input_validate($_POST['date_format']);
$time_format = $validation->input_validate($_POST['time_format']);
$records_perpage = $validation->input_validate($_POST['records_perpage']);

$admin_rate = $validation->input_validate($_POST['admin_rate']);
$tax_rate = $validation->input_validate($_POST['tax_rate']);

if($admin_rate == ''){
	$admin_rate = 0.00;

}


if($tax_rate == ''){
	$tax_rate = 0.00;

}
if(isset($_POST['google_indexing']))
{
	$google_indexing = "1";
}
else
{
	$google_indexing = "0";
}
$referral_amount = $validation->input_validate($_POST['referral_amount']);
if($referral_amount == "")
{
	$referral_amount = 0;
}
$min_wallet_amount = $validation->input_validate($_POST['min_wallet_amount']);
if($min_wallet_amount == "")
{
	$min_wallet_amount = 0;
}
$redeemable_amount = $validation->input_validate($_POST['redeemable_amount']);
if($redeemable_amount == "")
{
	$redeemable_amount = 0;
}
$mail_server = $validation->input_validate($_POST['mail_server']);
$mail_port = $validation->input_validate($_POST['mail_port']);
$mail_encryption = $validation->input_validate($_POST['mail_encryption']);
$mail_name = $validation->input_validate($_POST['mail_name']);
$mail_email = $validation->input_validate($_POST['mail_email']);
$mail_password = $validation->input_validate($_POST['mail_password']);
$thumb_width = $validation->input_validate($_POST['thumb_width']);
$thumb_height = $validation->input_validate($_POST['thumb_height']);
if(isset($_POST['thumb_ratio']))
{
	$thumb_ratio = "false";
}
else
{
	$thumb_ratio = "true";
}
$large_width = $validation->input_validate($_POST['large_width']);
$large_height = $validation->input_validate($_POST['large_height']);
if(isset($_POST['large_ratio']))
{
	$large_ratio = "false";
}
else
{
	$large_ratio = "true";
}
$image_maxsize = $validation->input_validate($_POST['image_maxsize']);
$file_maxsize = $validation->input_validate($_POST['file_maxsize']);

$imgTName = $_FILES['logo']['name'];
if($imgTName != "")
{
	$handle = new Upload($_FILES['logo']);
    if($handle->uploaded)
	{
		$handle->file_force_extension = true;
		$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
		$handle->allowed = array('image/*');
		$handle->image_resize = true;
		$handle->image_x = 600;
		$handle->image_y = 200;
		$handle->image_ratio = true;
		
		$handle->process(IMG_MAIN_LOC);
		if($handle->processed)
		{
			$logo = $handle->file_dst_name;
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: config.php");
			exit();
		}
		
		$handle-> clean();
	}
	else
	{
		$_SESSION['error_msg'] = $handle->error.'!';
		header("Location: config.php");
		exit();
    }
	
	if($mode == "edit")
	{
		$delresult = $media->filedeletion('mlm_config', 'configid', $configid, 'logo', IMG_MAIN_LOC);
	}
}

$imgTName2 = $_FILES['favicon']['name'];
if($imgTName2 != "")
{
	if($mode == "edit")
	{
		$delresult = $media->filedeletion('mlm_config', 'configid', $configid, 'favicon', IMG_MAIN_LOC);
	}
	
	$handle = new Upload($_FILES['favicon']);
    if($handle->uploaded)
	{
		$handle->file_force_extension = true;
		$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
		$handle->allowed = array('image/*');
		$handle->image_resize = true;
		$handle->image_x = 25;
		$handle->image_y = 25;
		$handle->image_ratio = false;
		$handle->file_overwrite = true;
		
		$handle->process(IMG_MAIN_LOC);
		if($handle->processed)
		{
			$favicon = $handle->file_dst_name;
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: config.php");
			exit();
		}
		
		$handle-> clean();
	}
	else
	{
		$_SESSION['error_msg'] = $handle->error.'!';
		header("Location: config.php");
		exit();
    }
}

if($logo == "")
{
	$logo = $old_logo;
}
if($favicon == "")
{
	$favicon = $old_favicon;
}

$fields = array('cms_title'=>$cms_title, 'cms_url'=>$cms_url, 'meta_title'=>$meta_title, 'meta_keywords'=>$meta_keywords, 'meta_description'=>$meta_description, 'site_url'=>$site_url, 'site_url_extension'=>$site_url_extension, 'site_url_web'=>$site_url_web, 'script'=>$script, 'style'=>$style, 'logo'=>$logo, 'favicon'=>$favicon, 'timezone'=>$timezone, 'date_format'=>$date_format, 'time_format'=>$time_format, 'records_perpage'=>$records_perpage,'admin_rate'=>$admin_rate,'tax_rate'=>$tax_rate, 'google_indexing'=>$google_indexing, 'referral_amount'=>$referral_amount, 'min_wallet_amount'=>$min_wallet_amount, 'redeemable_amount'=>$redeemable_amount, 'mail_server'=>$mail_server, 'mail_port'=>$mail_port, 'mail_encryption'=>$mail_encryption, 'mail_name'=>$mail_name, 'mail_email'=>$mail_email, 'mail_password'=>$mail_password, 'thumb_width'=>$thumb_width, 'thumb_height'=>$thumb_height, 'thumb_ratio'=>$thumb_ratio, 'large_width'=>$large_width, 'large_height'=>$large_height, 'large_ratio'=>$large_ratio, 'image_maxsize'=>$image_maxsize, 'file_maxsize'=>$file_maxsize, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$configQueryResult = $db->insert("mlm_config", $fields);
	if(!$configQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Configuration Added!";
	header("Location: config.php");
	exit();
}
else if($mode == "edit")
{
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$configQueryResult = $db->update("mlm_config", $fields, array('configid'=>$configid));
	if(!$configQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Configuration Updated!";
	header("Location: config.php");
	exit();
}
?>