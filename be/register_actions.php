<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "register";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$regid = $validation->urlstring_validate($_GET['regid']);
	
	$delresult = $media->filedeletion('rb_registrations', 'regid', $regid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	
	$registerQueryResult = $db->delete("rb_registrations", array('regid'=>$regid));
	if(!$registerQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: register_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$registerQueryResult} Record Deleted!";
	header("Location: register_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$regids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: register_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($regids, "$id");
				
				$delresult = $media->filedeletion('rb_registrations', 'regid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($regids, "$id");
			}
		}
		
		$regids = implode(',', $regids);
		
		if($bulk_actions == "delete")
		{
			$registerQueryResult = $db->custom("DELETE from rb_registrations where FIND_IN_SET(`regid`, '$regids')");
			if(!$registerQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: register_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: register_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$registerQueryResult = $db->custom("UPDATE rb_registrations SET status='$bulk_actions' where FIND_IN_SET(`regid`, '$regids')");
			if(!$registerQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: register_view.php");
			exit();
		}
	}
}
else
{
	$fields = $_POST;
	
	foreach($fields as $key=>$value)
	{
		$fields_string .= $key.'='.$value.'&';
	}
	rtrim($fields_string, '&');
	$fields_string = str_replace("bulk_actions=&", "", $fields_string);
	$fields_string = substr($fields_string, 0, -1);
	
	header("Location: register_view.php?$fields_string");
	exit();
}
?>