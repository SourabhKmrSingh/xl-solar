<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "product_reviews";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: product_review_view.php");
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
	if(isset($_GET['reviewid']))
	{
		$reviewid = $validation->urlstring_validate($_GET['reviewid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: product_review_view.php");
		exit();
	}
}

$name = $validation->input_validate($_POST['name']);
$email = $validation->input_validate($_POST['email']);
$ratings = $validation->input_validate($_POST['ratings']);
$message = $validation->input_validate($_POST['message']);
$remarks = $validation->input_validate($_POST['remarks']);
$status = $validation->input_validate($_POST['status']);

$fields = array('userid'=>$userid, 'name'=>$name, 'email'=>$email, 'ratings'=>$ratings, 'message'=>$message, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$reviewQueryResult = $db->insert("rb_products_reviews", $fields);
	if(!$reviewQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: product_review_view.php");
	exit();
}
else if($mode == "edit")
{
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$reviewQueryResult = $db->update("rb_products_reviews", $fields, array('reviewid'=>$reviewid));
	if(!$reviewQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: product_review_view.php$search_filter");
	exit();
}
?>