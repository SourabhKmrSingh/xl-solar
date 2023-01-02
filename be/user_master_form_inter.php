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
	header("Location: user_master_view.php");
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
	if(isset($_GET['userid']))
	{
		$userid = $validation->urlstring_validate($_GET['userid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: user_master_view.php");
		exit();
	}
}

$type = $validation->input_validate($_POST['type']);
$username = $validation->input_validate($_POST['username']);
$password = $validation->input_validate(sha1($_POST['password']));
$confirm_password = $validation->input_validate(sha1($_POST['confirm_password']));
$old_password = $validation->input_validate($_POST['old_password']);
$display_name = $validation->input_validate($_POST['display_name']);
$email = $validation->input_validate($_POST['email']);
if(isset($_POST['per_read']))
{
	$per_read = 1;
}
else
{
	$per_read = 0;
}
if(isset($_POST['per_write']))
{
	$per_write = 1;
}
else
{
	$per_write = 0;
}
if(isset($_POST['per_update']))
{
	$per_update = 1;
}
else
{
	$per_update = 0;
}
if(isset($_POST['per_delete']))
{
	$per_delete = 1;
}
else
{
	$per_delete = 0;
}
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
		header("Location: user_master_view.php");
		exit();
	}
}

$dupresult = $db->check_duplicates('rb_users', 'userid', $userid, 'username', strtolower($username), $mode);
if($dupresult >= 1)
{
	$_SESSION['error_msg'] = "Username already exists!";
	header("Location: user_master_view.php");
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
		$handle->image_resize = true;
		$handle->image_x = 30;
		$handle->image_y = 30;
		$handle->image_ratio = false;
		
		$handle->process(IMG_MAIN_LOC);
		if($handle->processed)
		{
			$imgName = $handle->file_dst_name;
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: user_master_view.php");
			exit();
		}
		
		$handle-> clean();
	}
	else
	{
		$_SESSION['error_msg'] = $handle->error.'!';
		header("Location: user_master_view.php");
		exit();
    }
	
	if($mode == "edit")
	{
		$delresult = $media->filedeletion('rb_users', 'userid', $userid, 'imgName', IMG_MAIN_LOC);
	}
}

if($_POST['password'] == "")
{
	$password = $old_password;
}
if($imgName == "")
{
	$imgName = $old_imgName;
}

$fields = array('type'=>$type, 'username'=>$username, 'password'=>$password, 'display_name'=>$display_name, 'email'=>$email, 'imgName'=>$imgName, 'per_read'=>$per_read, 'per_write'=>$per_write, 'per_update'=>$per_update, 'per_delete'=>$per_delete, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$userQueryResult = $db->insert("rb_users", $fields);
	if(!$userQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: user_master_view.php");
	exit();
}
else if($mode == "edit")
{
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$userQueryResult = $db->update("rb_users", $fields, array('userid'=>$userid));
	if(!$userQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: user_master_view.php$search_filter");
	exit();
}
?>