<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "newsletter";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: newsletter_view.php");
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
	if(isset($_GET['newsletterid']))
	{
		$newsletterid = $validation->urlstring_validate($_GET['newsletterid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: newsletter_view.php");
		exit();
	}
}

$first_name = $validation->input_validate($_POST['first_name']);
$last_name = $validation->input_validate($_POST['last_name']);
$email = $validation->input_validate($_POST['email']);
$remarks = $validation->input_validate($_POST['remarks']);
$status = $validation->input_validate($_POST['status']);

$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

$fields = array('first_name'=>$first_name, 'last_name'=>$last_name, 'email'=>$email, 'remarks'=>$remarks, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$newsletterQueryResult = $db->insert("rb_newsletters", $fields);
	if(!$newsletterQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: newsletter_view.php");
	exit();
}
else if($mode == "edit")
{
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$newsletterQueryResult = $db->update("rb_newsletters", $fields, array('newsletterid'=>$newsletterid));
	if(!$newsletterQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: newsletter_view.php$search_filter");
	exit();
}
?>