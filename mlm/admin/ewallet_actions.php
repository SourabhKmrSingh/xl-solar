<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "ewallet";
echo $validation->section($_SESSION['per_ewallet']);

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$ewalletid = $validation->urlstring_validate($_GET['ewalletid']);
	
	$delresult = $media->multiple_filedeletion('mlm_ewallet', 'ewalletid', $ewalletid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('mlm_ewallet', 'ewalletid', $ewalletid, 'fileName', FILE_LOC);

	$ewalletQueryResult = $db->delete("mlm_ewallet", array('ewalletid'=>$ewalletid));
	if(!$ewalletQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: ewallet_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$ewalletQueryResult} Record Deleted!";
	header("Location: ewallet_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$ewalletids = array();
	$refnos = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: ewallet_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($ewalletids, "$id");
				
				$delresult = $media->filedeletion('mlm_ewallet', 'ewalletid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('mlm_ewallet', 'ewalletid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "pending" || $bulk_actions == "approved" || $bulk_actions == "declined" || $bulk_actions == "fulfilled")
			{
				array_push($ewalletids, "$id");
				
				$ewalletResult = $db->view('refno', 'mlm_ewallet', 'ewalletid', "and ewalletid='$id'", 'ewalletid desc');
				$ewalletRow = $ewalletResult['result'][0];
				array_push($refnos, $ewalletRow['refno']);
			}
		}
		
		$ewalletids = implode(',', $ewalletids);
		$refnos = implode(',', $refnos);
		
		if($bulk_actions == "delete")
		{
			$ewalletQueryResult = $db->custom("DELETE from mlm_ewallet where FIND_IN_SET(`ewalletid`, '$ewalletids')");
			if(!$ewalletQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: ewallet_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: ewallet_view.php");
			exit();
		}
		else if($bulk_actions == "pending" || $bulk_actions == "approved" || $bulk_actions == "declined" || $bulk_actions == "fulfilled")
		{
			$ewalletQueryResult = $db->custom("UPDATE mlm_ewallet SET status='$bulk_actions' where FIND_IN_SET(`ewalletid`, '$ewalletids')");
			if(!$ewalletQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			$transactionResult = $db->custom("UPDATE mlm_transactions SET status='$bulk_actions' where FIND_IN_SET(`refno`, '$refnos')");
			if(!$transactionResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: ewallet_view.php");
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
	
	header("Location: ewallet_view.php?$fields_string");
	exit();
}
?>