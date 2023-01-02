<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "pincode";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$pincodeid = $validation->urlstring_validate($_GET['pincodeid']);
	
	$pincodeQueryResult = $db->delete("rb_pincodes", array('pincodeid'=>$pincodeid));
	if(!$pincodeQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: pincode_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$pincodeQueryResult} Record Deleted!";
	header("Location: pincode_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$pincodeids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: pincode_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			array_push($pincodeids, "$id");
		}
		
		$pincodeids = implode(',', $pincodeids);
		
		if($bulk_actions == "delete")
		{
			$pincodeQueryResult = $db->custom("DELETE from rb_pincodes where FIND_IN_SET(`pincodeid`, '$pincodeids')");
			if(!$pincodeQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: pincode_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: pincode_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$pincodeQueryResult = $db->custom("UPDATE rb_pincodes SET status='$bulk_actions' where FIND_IN_SET(`pincodeid`, '$pincodeids')");
			if(!$pincodeQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: pincode_view.php");
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
	
	header("Location: pincode_view.php?$fields_string");
	exit();
}
?>