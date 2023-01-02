<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "coupon";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$couponid = $validation->urlstring_validate($_GET['couponid']);
	
	$delresult = $media->filedeletion('rb_coupons', 'couponid', $couponid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('rb_coupons', 'couponid', $couponid, 'fileName', FILE_LOC);

	$couponQueryResult = $db->delete("rb_coupons", array('couponid'=>$couponid));
	if(!$couponQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: coupon_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$couponQueryResult} Record Deleted!";
	header("Location: coupon_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$couponids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: coupon_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($couponids, "$id");
				
				$delresult = $media->filedeletion('rb_coupons', 'couponid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('rb_coupons', 'couponid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($couponids, "$id");
			}
		}
		
		$couponids = implode(',', $couponids);
		
		if($bulk_actions == "delete")
		{
			$couponQueryResult = $db->custom("DELETE from rb_coupons where FIND_IN_SET(`couponid`, '$couponids')");
			if(!$couponQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: coupon_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: coupon_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$couponQueryResult = $db->custom("UPDATE rb_coupons SET status='$bulk_actions' where FIND_IN_SET(`couponid`, '$couponids')");
			if(!$couponQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: coupon_view.php");
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
	
	header("Location: coupon_view.php?$fields_string");
	exit();
}
?>