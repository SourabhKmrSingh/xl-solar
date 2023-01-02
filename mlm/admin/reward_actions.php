<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "reward";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$rewardid = $validation->urlstring_validate($_GET['rewardid']);
	
	$delresult = $media->filedeletion('mlm_rewards', 'rewardid', $rewardid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('mlm_rewards', 'rewardid', $rewardid, 'fileName', FILE_LOC);

	$rewardQueryResult = $db->delete("mlm_rewards", array('rewardid'=>$rewardid));
	if(!$rewardQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: reward_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$rewardQueryResult} Record Deleted!";
	header("Location: reward_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$rewardids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: reward_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($rewardids, "$id");
				
				$delresult = $media->filedeletion('mlm_rewards', 'rewardid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('mlm_rewards', 'rewardid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($rewardids, "$id");
			}
		}
		
		$rewardids = implode(',', $rewardids);
		
		if($bulk_actions == "delete")
		{
			$rewardQueryResult = $db->custom("DELETE from mlm_rewards where FIND_IN_SET(`rewardid`, '$rewardids')");
			if(!$rewardQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: reward_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: reward_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$rewardQueryResult = $db->custom("UPDATE mlm_rewards SET status='$bulk_actions' where FIND_IN_SET(`rewardid`, '$rewardids')");
			if(!$rewardQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: reward_view.php");
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
	
	header("Location: reward_view.php?$fields_string");
	exit();
}
?>