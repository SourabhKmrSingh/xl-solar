<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "level";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$levelid = $validation->urlstring_validate($_GET['levelid']);
	
	$delresult = $media->filedeletion('mlm_levels', 'levelid', $levelid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('mlm_levels', 'levelid', $levelid, 'fileName', FILE_LOC);

	$levelQueryResult = $db->delete("mlm_levels", array('levelid'=>$levelid));
	if(!$levelQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: level_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$levelQueryResult} Record Deleted!";
	header("Location: level_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$levelids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: level_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($levelids, "$id");
				
				$delresult = $media->filedeletion('mlm_levels', 'levelid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('mlm_levels', 'levelid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($levelids, "$id");
			}
		}
		
		$levelids = implode(',', $levelids);
		
		if($bulk_actions == "delete")
		{
			$levelQueryResult = $db->custom("DELETE from mlm_levels where FIND_IN_SET(`levelid`, '$levelids')");
			if(!$levelQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: level_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: level_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$levelQueryResult = $db->custom("UPDATE mlm_levels SET status='$bulk_actions' where FIND_IN_SET(`levelid`, '$levelids')");
			if(!$levelQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: level_view.php");
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
	
	header("Location: level_view.php?$fields_string");
	exit();
}
?>