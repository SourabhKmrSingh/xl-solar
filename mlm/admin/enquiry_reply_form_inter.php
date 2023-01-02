<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "enquiry";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: enquiry_reply_view.php");
	exit();
}

if($mode == "edit")
{
	echo $validation->update_permission();
}
else
{
	echo $validation->write_permission();
}

if($mode == "edit")
{
	if(isset($_GET['replyid']))
	{
		$replyid = $validation->urlstring_validate($_GET['replyid']);
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: enquiry_reply_view.php");
		exit();
	}
}

if($_SESSION['search_filter'] != "")
{
	$search_filter = "?".$_SESSION['search_filter'];
}

$enquiryid = $validation->input_validate($_POST['enquiryid']);
$regid = $validation->input_validate($_POST['regid']);
$posted_by = $validation->input_validate($_POST['posted_by']);
$message = $validation->input_validate($_POST['message']);
$status = $validation->input_validate($_POST['status']);

$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

$fields = array('enquiryid'=>$enquiryid, 'regid'=>$regid, 'posted_by'=>$posted_by, 'message'=>$message, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$replyQueryResult = $db->insert("mlm_enquiries_replies", $fields);
	if(!$replyQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: enquiry_reply_view.php{$search_filter}");
	exit();
}
else if($mode == "edit")
{
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$replyQueryResult = $db->update("mlm_enquiries_replies", $fields, array('replyid'=>$replyid));
	if(!$replyQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: enquiry_reply_view.php{$search_filter}");
	exit();
}
?>