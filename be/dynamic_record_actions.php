<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "dynamic_records";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$recordid = $validation->urlstring_validate($_GET['recordid']);
	
	$delresult = $media->multiple_filedeletion('rb_dynamic_records', 'recordid', $recordid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('rb_dynamic_records', 'recordid', $recordid, 'fileName', FILE_LOC);

	$recordQueryResult = $db->delete("rb_dynamic_records", array('recordid'=>$recordid));
	if(!$recordQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: dynamic_record_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$recordQueryResult} Record Deleted!";
	header("Location: dynamic_record_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$recordids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: dynamic_record_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($recordids, "$id");
				
				$delresult = $media->filedeletion('rb_dynamic_records', 'recordid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('rb_dynamic_records', 'recordid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($recordids, "$id");
			}
		}
		
		$recordids = implode(',', $recordids);
		
		if($bulk_actions == "delete")
		{
			$recordQueryResult = $db->custom("DELETE from rb_dynamic_records where FIND_IN_SET(`recordid`, '$recordids')");
			if(!$recordQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: dynamic_record_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: dynamic_record_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$recordQueryResult = $db->custom("UPDATE rb_dynamic_records SET status='$bulk_actions' where FIND_IN_SET(`recordid`, '$recordids')");
			if(!$recordQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: dynamic_record_view.php");
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
	
	header("Location: dynamic_record_view.php?$fields_string");
	exit();
}
?>