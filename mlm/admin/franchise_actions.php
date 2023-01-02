<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "franchise";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$franchiseid = $validation->urlstring_validate($_GET['franchiseid']);
	
	$delresult = $media->filedeletion('mlm_franchise', 'franchiseid', $franchiseid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('mlm_franchise', 'franchiseid', $franchiseid, 'fileName', FILE_LOC);

	$franchiseQueryResult = $db->delete("mlm_franchise", array('franchiseid'=>$franchiseid));
	if(!$franchiseQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: franchise_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$franchiseQueryResult} Record Deleted!";
	header("Location: franchise_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$franchiseids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: franchise_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($franchiseids, "$id");
				
				$delresult = $media->filedeletion('mlm_franchise', 'franchiseid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('mlm_franchise', 'franchiseid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($franchiseids, "$id");
			}
		}
		
		$franchiseids = implode(',', $franchiseids);
		
		if($bulk_actions == "delete")
		{
			$franchiseQueryResult = $db->custom("DELETE from mlm_franchise where FIND_IN_SET(`franchiseid`, '$franchiseids')");
			if(!$franchiseQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: franchise_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: franchise_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$franchiseQueryResult = $db->custom("UPDATE mlm_franchise SET status='$bulk_actions' where FIND_IN_SET(`franchiseid`, '$franchiseids')");
			if(!$franchiseQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: franchise_view.php");
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
	
	header("Location: franchise_view.php?$fields_string");
	exit();
}
?>