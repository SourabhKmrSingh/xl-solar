<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "franchise";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: franchise_purchase_view.php");
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
	if(isset($_GET['fpurchaseid']))
	{
		$fpurchaseid = $validation->urlstring_validate($_GET['fpurchaseid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: franchise_purchase_view.php");
		exit();
	}
}

$configQueryResult2 = $db->view('*', 'mlm_config', 'configid', "", "configid desc");
if(!$configQueryResult2)
{
	echo mysqli_error($connect);
	exit();
}
$configRow2 = $configQueryResult2['result'][0];

$regid = $validation->input_validate($_POST['regid']);
$refno_custom = $validation->input_validate($_POST['refno_custom']);
$membership_id = $validation->input_validate($_POST['membership_id']);
$sponsor_id = $validation->input_validate($_POST['sponsor_id']);
$billing_first_name = $validation->input_validate($_POST['billing_first_name']);
$billing_last_name = $validation->input_validate($_POST['billing_last_name']);
$billing_mobile = $validation->input_validate($_POST['billing_mobile']);
$billing_mobile_alter = $validation->input_validate($_POST['billing_mobile_alter']);
$billing_address = $validation->input_validate($_POST['billing_address']);
$billing_landmark = $validation->input_validate($_POST['billing_landmark']);
$billing_city = $validation->input_validate($_POST['billing_city']);
$billing_state = $validation->input_validate($_POST['billing_state']);
$billing_country = $validation->input_validate($_POST['billing_country']);
$billing_pincode = $validation->input_validate($_POST['billing_pincode']);
$status = $validation->input_validate($_POST['status']);

$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

$fields = array('billing_first_name'=>$billing_first_name, 'billing_last_name'=>$billing_last_name, 'billing_mobile'=>$billing_mobile, 'billing_mobile_alter'=>$billing_mobile_alter, 'billing_address'=>$billing_address, 'billing_landmark'=>$billing_landmark, 'billing_city'=>$billing_city, 'billing_state'=>$billing_state, 'billing_country'=>$billing_country, 'billing_pincode'=>$billing_pincode, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$purchaseQueryResult = $db->insert("rb_franchise_purchase", $fields);
	if(!$purchaseQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: franchise_purchase_view.php");
	exit();
}
else if($mode == "edit")
{
	if($status == "paid")
	{
		$fields['order_status'] = 'fulfilled';
	}
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	if($status == "cancelled")
	{
		$fields['order_status'] = 'cancelled';
	}
	
	$purchaseQueryResult = $db->update("rb_franchise_purchase", $fields, array('fpurchaseid'=>$fpurchaseid));
	if(!$purchaseQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}

	if($status == "paid")
	{
		$franchisePurchaseQuery = $db->view('*','rb_franchise_purchase', 'fpurchaseid', " and fpurchaseid={$fpurchaseid}"); 
		$franchisePurchaseRow = $franchisePurchaseQuery['result'][0];

		$regid = $franchisePurchaseRow['regid'];
	}
	
	
	
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: franchise_purchase_view.php$search_filter");
	exit();
}
?>