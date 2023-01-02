<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "repurchase";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$repurchaseid = $validation->urlstring_validate($_GET['repurchaseid']);
	
	$delresult = $media->filedeletion('mlm_repurchase', 'repurchaseid', $repurchaseid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('mlm_repurchase', 'repurchaseid', $repurchaseid, 'fileName', FILE_LOC);

	$levelQueryResult = $db->delete("mlm_repurchase", array('repurchaseid'=>$repurchaseid));
	if(!$levelQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: repurchase_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$levelQueryResult} Record Deleted!";
	header("Location: repurchase_view.php");
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
		header("Location: repurchase_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($levelids, "$id");
				
				$delresult = $media->filedeletion('mlm_repurchase', 'repurchaseid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('mlm_repurchase', 'repurchaseid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($levelids, "$id");
			}
		}
		
		$levelids = implode(',', $levelids);
		
		if($bulk_actions == "delete")
		{
			$levelQueryResult = $db->custom("DELETE from mlm_repurchase where FIND_IN_SET(`repurchaseid`, '$levelids')");
			if(!$levelQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: repurchase_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: repurchase_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$levelQueryResult = $db->custom("UPDATE mlm_repurchase SET status='$bulk_actions' where FIND_IN_SET(`repurchaseid`, '$levelids')");
			if(!$levelQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: repurchase_view.php");
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
	
	header("Location: repurchase_view.php?$fields_string");
	exit();
}
?>