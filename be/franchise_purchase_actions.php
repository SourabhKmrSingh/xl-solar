<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "franchise";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$refno_custom = $validation->urlstring_validate($_GET['refno_custom']);
	
	
	
	$purchaseQueryResult = $db->delete("rb_franchise_purchase", array('refno_custom'=>$refno_custom));
	if(!$purchaseQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: franchise_purchase_view.php");
		exit();
	}
	
	$_SESSION['success_msg'] = "{$purchaseQueryResult} Record Deleted!";
	header("Location: franchise_purchase_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$refno_customs = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: franchise_purchase_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		
		
		$refno_customs = implode(',', $del_items);
	
		
		if($bulk_actions == "delete")
		{
			$purchaseQueryResult = $db->custom("DELETE from rb_franchise_purchase where FIND_IN_SET(`refno_custom`, '$refno_customs')");
			if(!$purchaseQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: franchise_purchase_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: franchise_purchase_view.php");
			exit();
		}
		else if($bulk_actions == "paid" || $bulk_actions == "unpaid")
		{
			$purchaseQueryResult = $db->custom("UPDATE rb_franchise_purchase SET status='$bulk_actions' where FIND_IN_SET(`refno_custom`, '$refno_customs')");
			if(!$purchaseQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: franchise_purchase_view.php");
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
	
	header("Location: franchise_purchase_view.php?$fields_string");
	exit();
}
?>