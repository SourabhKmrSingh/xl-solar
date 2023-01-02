<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "enquiry";

$q = $validation->urlstring_validate($_GET['q']);
$enquiryid = $validation->urlstring_validate($_GET['enquiryid']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$replyid = $validation->urlstring_validate($_GET['replyid']);
	
	$replyQueryResult = $db->delete("mlm_enquiries_replies", array('replyid'=>$replyid));
	if(!$replyQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: enquiry_reply_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$replyQueryResult} Record Deleted!";
	header("Location: enquiry_reply_view.php?enquiryid={$enquiryid}");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$enquiryid = $validation->urlstring_validate($_POST['enquiryid']);
	$del_items = $_POST['del_items'];
	$replyids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: enquiry_reply_view.php?enquiryid={$enquiryid}");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($replyids, "$id");
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($replyids, "$id");
			}
		}
		
		$replyids = implode(',', $replyids);
		
		if($bulk_actions == "delete")
		{
			$replyQueryResult = $db->custom("DELETE from mlm_enquiries_replies where FIND_IN_SET(`replyid`, '$replyids')");
			if(!$replyQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: enquiry_reply_view.php?enquiryid={$enquiryid}");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: enquiry_reply_view.php?enquiryid={$enquiryid}");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$replyQueryResult = $db->custom("UPDATE mlm_enquiries_replies SET status='$bulk_actions' where FIND_IN_SET(`replyid`, '$replyids')");
			if(!$replyQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: enquiry_reply_view.php?enquiryid={$enquiryid}");
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
	
	header("Location: enquiry_reply_view.php?$fields_string");
	exit();
}
?>