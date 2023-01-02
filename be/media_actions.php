<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "media";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$mediaid = $validation->urlstring_validate($_GET['mediaid']);
	
	$delresult = $media->filedeletion('rb_media', 'mediaid', $mediaid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('rb_media', 'mediaid', $mediaid, 'fileName', FILE_LOC);

	$mediaQueryResult = $db->delete("rb_media", array('mediaid'=>$mediaid));
	if(!$mediaQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: media_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$mediaQueryResult} Record Deleted!";
	header("Location: media_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$mediaids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: media_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($mediaids, "$id");
				
				$delresult = $media->filedeletion('rb_media', 'mediaid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('rb_media', 'mediaid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($mediaids, "$id");
			}
		}
		
		$mediaids = implode(',', $mediaids);
		
		if($bulk_actions == "delete")
		{
			$mediaQueryResult = $db->custom("DELETE from rb_media where FIND_IN_SET(`mediaid`, '$mediaids')");
			if(!$mediaQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: media_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: media_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$mediaQueryResult = $db->custom("UPDATE rb_media SET status='$bulk_actions' where FIND_IN_SET(`mediaid`, '$mediaids')");
			if(!$mediaQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: media_view.php");
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
	
	header("Location: media_view.php?$fields_string");
	exit();
}
?>