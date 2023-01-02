<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "";

echo $validation->admin_permission();

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$userid = $validation->urlstring_validate($_GET['userid']);
	
	$delresult = $media->filedeletion('rb_users', 'userid', $userid, 'imgName', IMG_MAIN_LOC);

	$userQueryResult = $db->delete("rb_users", array('userid'=>$userid));
	if(!$userQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: user_master_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$userQueryResult} Record Deleted!";
	header("Location: user_master_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$userids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: user_master_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($userids, "$id");
				
				$delresult = $media->filedeletion('rb_users', 'userid', $id, 'imgName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($userids, "$id");
			}
		}
		
		$userids = implode(',', $userids);
		
		if($bulk_actions == "delete")
		{
			$userQueryResult = $db->custom("DELETE from rb_users where FIND_IN_SET(`userid`, '$userids')");
			if(!$userQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: user_master_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: user_master_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$userQueryResult = $db->custom("UPDATE rb_users SET status='$bulk_actions' where FIND_IN_SET(`userid`, '$userids')");
			if(!$userQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: user_master_view.php");
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
	
	header("Location: user_master_view.php?$fields_string");
	exit();
}
?>