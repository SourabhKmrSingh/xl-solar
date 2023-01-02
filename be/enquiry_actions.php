<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "enquiry";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$enquiryid = $validation->urlstring_validate($_GET['enquiryid']);
	
	$enquiryQueryResult = $db->delete("rb_enquiries", array('enquiryid'=>$enquiryid));
	if(!$enquiryQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: enquiry_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$enquiryQueryResult} Record Deleted!";
	header("Location: enquiry_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$enquiryids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: enquiry_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($enquiryids, "$id");
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($enquiryids, "$id");
			}
		}
		
		$enquiryids = implode(',', $enquiryids);
		
		if($bulk_actions == "delete")
		{
			$enquiryQueryResult = $db->custom("DELETE from rb_enquiries where FIND_IN_SET(`enquiryid`, '$enquiryids')");
			if(!$enquiryQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: enquiry_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: enquiry_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$enquiryQueryResult = $db->custom("UPDATE rb_enquiries SET status='$bulk_actions' where FIND_IN_SET(`enquiryid`, '$enquiryids')");
			if(!$enquiryQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: enquiry_view.php");
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
	
	header("Location: enquiry_view.php?$fields_string");
	exit();
}
?>