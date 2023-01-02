<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "franchise";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: franchise_view.php");
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
	if(isset($_GET['franchiseid']))
	{
		$franchiseid = $validation->urlstring_validate($_GET['franchiseid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: franchise_view.php");
		exit();
	}
}

$title = $validation->input_validate($_POST['title']);
$title_id = $validation->input_validate($_POST['title_id']);
if($title_id == "")
{
	$title_id = $title;
}
$title_id = $validation->friendlyURL($title_id);
$total_pins = $validation->input_validate($_POST['pins']);
if($total_pins=='')
{
	$total_pins = 0;
}
$free = $validation->input_validate($_POST['free']);
if($free=='')
{
	$free = 0;
}
$amount = $validation->input_validate($_POST['amount']);
if($amount=='')
{
	$amount = 0;
}

$description = mysqli_real_escape_string($connect, $_POST['description']);
if(isset($_POST['priority']))
{
	$priority = 1;
}
else
{
	$priority = 0;
}
$status = $validation->input_validate($_POST['status']);
$old_imgName = $validation->input_validate($_POST['old_imgName']);
$old_fileName = $validation->input_validate($_POST['old_fileName']);

$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

// $dupresult = $db->check_duplicates('mlm_franchise', 'franchiseid', $franchiseid, 'title_id', strtolower($title_id), $mode);
// if($dupresult >= 1)
// {
	// $_SESSION['error_msg'] = "Title ID already exists!";
	// header("Location: franchise_view.php");
	// exit();
// }

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
			header("Location: franchise_view.php");
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
			header("Location: franchise_view.php");
			exit();
		}
		
		$handle-> clean();
	}
	else
	{
		$_SESSION['error_msg'] = $handle->error.'!';
		header("Location: franchise_view.php");
		exit();
    }
	
	if($mode == "edit")
	{
		$delresult = $media->filedeletion('mlm_franchise', 'franchiseid', $franchiseid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	}
}

$fileTName = $_FILES['fileName']['name'];
if($fileTName != "")
{	
	$handle = new Upload($_FILES['fileName']);
    if($handle->uploaded)
	{
		$handle->file_force_extension = true;
		$handle->file_max_size = $validation->db_field_validate($configRow['file_maxsize']);
		$handle->allowed = array('application/*', 'text/csv', 'application/zip');
		
		$handle->process(FILE_LOC);
		if($handle->processed)
		{
			$fileName = $handle->file_dst_name;
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: franchise_view.php");
			exit();
		}
		
		$handle-> clean();
	}
	else
	{
		$_SESSION['error_msg'] = $handle->error.'!';
		header("Location: franchise_view.php");
		exit();
    }
	
	if($mode == "edit")
	{
		$delresult = $media->filedeletion('mlm_franchise', 'franchiseid', $franchiseid, 'fileName', FILE_LOC);
	}
}

$planid = $validation->input_validate($_POST['planid']);
if($planid=='')
{
	$planid = 0;
}

$tax = $validation->input_validate($_POST['tax']);
if($tax=='')
{
	$tax = 0;
}

if($imgName == "")
{
	$imgName = $old_imgName;
}
if($fileName == "")
{
	$fileName = $old_fileName;
}

$fields = array('planid'=>$planid, 'title'=>$title, 'title_id'=>$title_id, 'tax'=>$tax, 'total_pins'=>$total_pins, 'free'=>$free,'description'=>$description, 'imgName'=>$imgName, 'fileName'=>$fileName, 'priority'=>$priority, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$franchiseQueryResult = $db->insert("mlm_franchise", $fields);
	if(!$franchiseQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: franchise_view.php");
	exit();
}
else if($mode == "edit")
{
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$franchiseQueryResult = $db->update("mlm_franchise", $fields, array('franchiseid'=>$franchiseid));
	if(!$franchiseQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: franchise_view.php$search_filter");
	exit();
}
?>