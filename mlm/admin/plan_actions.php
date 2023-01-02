<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "plan";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$planid = $validation->urlstring_validate($_GET['planid']);
	
	$delresult = $media->filedeletion('mlm_plans', 'planid', $planid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('mlm_plans', 'planid', $planid, 'fileName', FILE_LOC);

	$planQueryResult = $db->delete("mlm_plans", array('planid'=>$planid));
	if(!$planQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: plan_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$planQueryResult} Record Deleted!";
	header("Location: plan_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$planids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: plan_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($planids, "$id");
				
				$delresult = $media->filedeletion('mlm_plans', 'planid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('mlm_plans', 'planid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($planids, "$id");
			}
		}
		
		$planids = implode(',', $planids);
		
		if($bulk_actions == "delete")
		{
			$planQueryResult = $db->custom("DELETE from mlm_plans where FIND_IN_SET(`planid`, '$planids')");
			if(!$planQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: plan_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: plan_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$planQueryResult = $db->custom("UPDATE mlm_plans SET status='$bulk_actions' where FIND_IN_SET(`planid`, '$planids')");
			if(!$planQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: plan_view.php");
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
	
	header("Location: plan_view.php?$fields_string");
	exit();
}
?>