<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "newsletter";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$newsletterid = $validation->urlstring_validate($_GET['newsletterid']);
	
	$newsletterQueryResult = $db->delete("rb_newsletters", array('newsletterid'=>$newsletterid));
	if(!$newsletterQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: newsletter_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$newsletterQueryResult} Record Deleted!";
	header("Location: newsletter_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$newsletterids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: newsletter_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($newsletterids, "$id");
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($newsletterids, "$id");
			}
		}
		
		$newsletterids = implode(',', $newsletterids);
		
		if($bulk_actions == "delete")
		{
			$newsletterQueryResult = $db->custom("DELETE from rb_newsletters where FIND_IN_SET(`newsletterid`, '$newsletterids')");
			if(!$newsletterQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: newsletter_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: newsletter_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$newsletterQueryResult = $db->custom("UPDATE rb_newsletters SET status='$bulk_actions' where FIND_IN_SET(`newsletterid`, '$newsletterids')");
			if(!$newsletterQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: newsletter_view.php");
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
	
	header("Location: newsletter_view.php?$fields_string");
	exit();
}
?>