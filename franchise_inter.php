<?php
include_once('inc_config.php');

if(isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'])
{
	$regid = $validation->input_validate($_POST['regid']);
	
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
	$franchiseid = $validation->input_validate($_POST['franchiseid']);

	
	if($franchiseid == "") {
		$_SESSION['error_msg_fe'] = "Error occur! Please try again later.";
		header("Location: {$base_url}franchise{$suffix}");
		exit();
	}

	// Franchise Query
	$frachiseDetailsQuery = $db->view("*",'mlm_franchise','franchiseid'," and franchiseid={$franchiseid}");
	$franchiseDetailsRow = $frachiseDetailsQuery['result'][0];

	$planid = $franchiseDetailsRow['planid'];

	$planResultQuery = $db->view('*','mlm_plans','planid'," and planid={$planid}");
	$planResultRow = $planResultQuery['result'][0];

	$franchiseTitle = $franchiseDetailsRow['title'];
	$franchisePrice = $planResultRow['amount'];
	$tax = $franchiseDetailsRow['tax'];
	$totalPins = $franchiseDetailsRow['total_pins'] + $franchiseDetailsRow['free'];

	$price = $franchisePrice + ($franchisePrice * ($tax/100));
	$totalPrice = $franchiseDetailsRow['total_pins'] * ($price);

	$status = 'unpaid';

	if($membership_id == "" || $sponsor_id == ""){
		$_SESSION['error_msg_fe'] = "You have to register in mlm to buy Franchise.";
		header("Location: {$base_url}franchise{$suffix}");
		exit();
	}
	

	if($billing_first_name == "" || $billing_last_name == "" || $billing_mobile == "" || $billing_address == "") {
		$_SESSION['error_msg_fe'] = "Please Update your Profile First.";
		header("Location: {$base_url}franchise{$suffix}");
		exit();
	}

	


	$refno = "";
	$current_year = date('Y');
	$updated_year = date('y');
	$refResult = $db->view("MAX(refno_value) as max_refno_value", "rb_franchise_purchase", "fpurchaseid", "and YEAR(createdate) = '$current_year'");
	$refRow = $refResult['result'][0];
	$max_refno_value = $refRow['max_refno_value'];
	$refno_value = $max_refno_value+1;
	$refno_value = sprintf("%02d", $refno_value);
	$refno = "FSL-".$updated_year."-".$refno_value;
	
	
	$refno_custom = "";
	$current_year = date('Y');
	$updated_year = date('y');
	$refResult = $db->view("MAX(refno_custom_value) as max_refno_custom_value", "rb_franchise_purchase", "fpurchaseid", "and YEAR(createdate) = '$current_year'");
	$refRow = $refResult['result'][0];
	$max_refno_custom_value = $refRow['max_refno_custom_value'];
	$refno_custom_value = $max_refno_custom_value+1;
	$refno_custom_value = sprintf("%02d", $refno_custom_value);
	$refno_custom = "FSL-".$updated_year."-".$refno_custom_value;

	$order_status = "pending";


	$fields = array('regid'=>$regid, 'refno'=>$refno,'refno_value'=>$refno_value, 'refno_custom'=>$refno_custom, 'refno_custom_value'=> $refno_custom_value, 'membership_id'=>$membership_id, 'sponsor_id'=>$sponsor_id, 'billing_first_name'=>$billing_first_name,'billing_last_name'=>$billing_last_name, 'billing_mobile'=>$billing_mobile, 'billing_mobile_alter'=>$billing_mobile_alter,'billing_address'=>$billing_address,'billing_landmark'=>$billing_landmark, 'billing_city'=>$billing_city,'billing_state'=>$billing_state,'billing_country'=>$billing_country,'billing_pincode'=>$billing_pincode,'franchiseid'=>$franchiseid,'franchisePrice'=>$franchisePrice,'tax'=>$tax,'totalPrice'=>$totalPrice,'franchiseTitle'=>$franchiseTitle, 'totalPins'=>$totalPins,'status'=>$status, 'order_status'=>$order_status, 'user_ip'=>$user_ip);
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;

	$purchaseResult = $db->insert("rb_franchise_purchase", $fields);
}
header("Location: {$base_url}page/success{$suffix}");
exit();
?>