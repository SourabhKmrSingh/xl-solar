<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "product_reviews";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$reviewid = $validation->urlstring_validate($_GET['reviewid']);
	
	$reviewQueryResult = $db->delete("rb_products_reviews", array('reviewid'=>$reviewid));
	if(!$reviewQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: product_review_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$reviewQueryResult} Record Deleted!";
	header("Location: product_review_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$reviewids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: product_review_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($reviewids, "$id");
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($reviewids, "$id");
			}
		}
		
		$reviewids = implode(',', $reviewids);
		
		if($bulk_actions == "delete")
		{
			$reviewQueryResult = $db->custom("DELETE from rb_products_reviews where FIND_IN_SET(`reviewid`, '$reviewids')");
			if(!$reviewQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: product_review_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: product_review_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$reviewQueryResult = $db->custom("UPDATE rb_products_reviews SET status='$bulk_actions' where FIND_IN_SET(`reviewid`, '$reviewids')");
			if(!$reviewQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: product_review_view.php");
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
	
	header("Location: product_review_view.php?$fields_string");
	exit();
}
?>