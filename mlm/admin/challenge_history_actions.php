<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "challengehistory";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$historyid = $validation->urlstring_validate($_GET['historyid']);
	
	$delresult = $media->multiple_filedeletion('mlm_challenges_history', 'historyid', $historyid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('mlm_challenges_history', 'historyid', $historyid, 'fileName', FILE_LOC);

	$challengehistoryQueryResult = $db->delete("mlm_challenges_history", array('historyid'=>$historyid));
	if(!$challengehistoryQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: challenge_history_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$challengehistoryQueryResult} Record Deleted!";
	header("Location: challenge_history_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$historyids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: challenge_history_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($historyids, "$id");
				
				$delresult = $media->filedeletion('mlm_challenges_history', 'historyid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('mlm_challenges_history', 'historyid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "pending" || $bulk_actions == "claimed" || $bulk_actions == "declined" || $bulk_actions == "fulfilled" || $bulk_actions == "achieved")
			{
				array_push($historyids, "$id");
			}
		}
		
		$historyids = implode(',', $historyids);
		
		if($bulk_actions == "delete")
		{
			$challengehistoryQueryResult = $db->custom("DELETE from mlm_challenges_history where FIND_IN_SET(`historyid`, '$historyids')");
			if(!$challengehistoryQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: challenge_history_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: challenge_history_view.php");
			exit();
		}
		else if($bulk_actions == "pending" || $bulk_actions == "claimed" || $bulk_actions == "declined" || $bulk_actions == "fulfilled" || $bulk_actions == "achieved")
		{
			$challengehistoryQueryResult = $db->custom("UPDATE mlm_challenges_history SET status='$bulk_actions' where FIND_IN_SET(`historyid`, '$historyids')");
			if(!$challengehistoryQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: challenge_history_view.php");
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
	
	header("Location: challenge_history_view.php?$fields_string");
	exit();
}
?>