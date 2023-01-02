<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "badlink";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$badlinkid = $validation->urlstring_validate($_GET['badlinkid']);
	
	$badlinkQueryResult = $db->delete("rb_badlinks", array('badlinkid'=>$badlinkid));
	if(!$badlinkQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: badlink_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$badlinkQueryResult} Record Deleted!";
	header("Location: badlink_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$badlinkids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: badlink_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($badlinkids, "$id");
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($badlinkids, "$id");
			}
		}
		
		$badlinkids = implode(',', $badlinkids);
		
		if($bulk_actions == "delete")
		{
			$badlinkQueryResult = $db->custom("DELETE from rb_badlinks where FIND_IN_SET(`badlinkid`, '$badlinkids')");
			if(!$badlinkQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: badlink_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: badlink_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$badlinkQueryResult = $db->custom("UPDATE rb_badlinks SET status='$bulk_actions' where FIND_IN_SET(`badlinkid`, '$badlinkids')");
			if(!$badlinkQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: badlink_view.php");
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
	
	header("Location: badlink_view.php?$fields_string");
	exit();
}
?>