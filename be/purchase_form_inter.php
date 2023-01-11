<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "purchase";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "Error! Please Try Again.";
	header("Location: purchase_view.php");
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
	if(isset($_GET['purchaseid']))
	{
		$purchaseid = $validation->urlstring_validate($_GET['purchaseid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "Error! Please Try Again.";
		header("Location: purchase_view.php");
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
$shipping_first_name = $validation->input_validate($_POST['shipping_first_name']);
$shipping_last_name = $validation->input_validate($_POST['shipping_last_name']);
$shipping_mobile = $validation->input_validate($_POST['shipping_mobile']);
$shipping_mobile_alter = $validation->input_validate($_POST['shipping_mobile_alter']);
$shipping_address = $validation->input_validate($_POST['shipping_address']);
$shipping_landmark = $validation->input_validate($_POST['shipping_landmark']);
$shipping_city = $validation->input_validate($_POST['shipping_city']);
$shipping_state = $validation->input_validate($_POST['shipping_state']);
$shipping_country = $validation->input_validate($_POST['shipping_country']);
$shipping_pincode = $validation->input_validate($_POST['shipping_pincode']);
$note = $validation->input_validate($_POST['note']);
$tracking_status = $validation->input_validate($_POST['tracking_status']);
$old_invoicedate = $validation->input_validate($_POST['old_invoicedate']);
$status = $validation->input_validate($_POST['status']);
$record_check = $validation->input_validate($_POST['record_check']);


$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

$fields = array('billing_first_name'=>$billing_first_name, 'billing_last_name'=>$billing_last_name, 'billing_mobile'=>$billing_mobile, 'billing_mobile_alter'=>$billing_mobile_alter, 'billing_address'=>$billing_address, 'billing_landmark'=>$billing_landmark, 'billing_city'=>$billing_city, 'billing_state'=>$billing_state, 'billing_country'=>$billing_country, 'billing_pincode'=>$billing_pincode, 'shipping_first_name'=>$shipping_first_name, 'shipping_last_name'=>$shipping_last_name, 'shipping_mobile'=>$shipping_mobile, 'shipping_mobile_alter'=>$shipping_mobile_alter, 'shipping_address'=>$shipping_address, 'shipping_landmark'=>$shipping_landmark, 'shipping_city'=>$shipping_city, 'shipping_state'=>$shipping_state, 'shipping_country'=>$shipping_country, 'shipping_pincode'=>$shipping_pincode, 'note'=>$note, 'tracking_status'=>$tracking_status, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$purchaseQueryResult = $db->insert("rb_purchases", $fields);
	if(!$purchaseQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: purchase_view.php");
	exit();
}
else if($mode == "edit")
{
	if($old_invoicedate == "" and $tracking_status == "delivered")
	{
		$fields['invoicedate'] = $createdate;
		$fields['record_check'] = 1;
		

		$message = "Hi {$billing_first_name} {$billing_last_name}, \n\nYour Order with Order ID : {$refno_custom} has been successfully delivered. \n\nThankyou for using our services.\nXL Solar";
		$send = WhatsApp::sendMSG("+91{$billing_mobile}", $message);
	}
	
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$purchaseQueryResult = $db->update("rb_purchases", $fields, array('purchaseid'=>$purchaseid));
	if(!$purchaseQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	if($tracking_status == "cancelled")
	{
		$checkPurchaseResult = $db->view("refno_custom,wallet_money", "rb_purchases", "purchaseid", "and purchaseid='{$purchaseid}'", 'purchaseid desc');
		$checkPurchaseRow = $checkPurchaseResult['result'][0];
		$refno_custom = $checkPurchaseRow['refno_custom'];
		$wallet_money = $checkPurchaseRow['wallet_money'];

	
		if(!$purchaseResult)
		{
			echo "Order is not placed! Consult Administrator";
			exit();
		}

		$message = "Hi {$billing_first_name} {$billing_last_name}, \n\nYour Order with Order ID : {$refno_custom} has been cancelled.\n\nThank you\nXL Solar";
		$send = WhatsApp::sendMSG("+91{$billing_mobile}", $message);
		
		$productPurchaseResult = $db->view("productid,variantid,quantity", "rb_purchases", "purchaseid", "and refno_custom='{$refno_custom}'", 'purchaseid desc');
		
		if($productPurchaseResult['num_rows'] >= 1)
		{
			foreach($productPurchaseResult['result'] as $productPurchaseRow)
			{
				$productid = $productPurchaseRow['productid'];
				$variantid = $productPurchaseRow['variantid'];
				$quantity = $productPurchaseRow['quantity'];
				
				$stockResult = $db->custom("update rb_products set stock_quantity = stock_quantity+{$quantity} where productid='{$productid}'");
				if(!$stockResult)
				{
					echo "Stock is not updated! Consult Administrator";
					exit();
				}
				$stockResult2 = $db->custom("update rb_products_variants set stock_quantity = stock_quantity+{$quantity} where productid='{$productid}' and variantid='{$variantid}'");
				if(!$stockResult2)
				{
					echo "Stock is not updated! Consult Administrator";
					exit();
				}
			}
		}
		
		if($wallet_money != "" and $wallet_money != "0" and $wallet_money != "0.00")
		{
			$fields3 = array('status'=>"declined", 'remarks'=>'Product Cancelled');
			$fields3['modifytime'] = $createtime;
			$fields3['modifydate'] = $createdate;
			
			$ewalletrequestResult = $db->update("mlm_ewallet_requests", $fields3, array('purchaseid'=>$purchaseid));
			if(!$ewalletrequestResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			
			$registerwalletResult = $db->custom("update mlm_registrations set wallet_money = wallet_money+{$wallet_money} where regid='{$regid}'");
			if(!$registerwalletResult)
			{
				echo "Member Wallet is not added! Consult Administrator";
				exit();
			}
		}
	}
	
	
	if($old_invoicedate == "" and $tracking_status == "delivered" and $sponsor_id != "" && $record_check == "0")
	{

		
		$purchaseQueryResult = $db->view('tracking_status, business_volume, final_price, price, shipping, coupon_discount, taxamount', 'rb_purchases', 'purchaseid', "and refno_custom = '$refno_custom' and income_type  = 'level'");

		// Update First Purchase 
		$db->update("mlm_registrations", array('first_purchase'=> '1', "status" => 'active'), array('membership_id' =>$membership_id));

		$total_amount = 0;



		// Check Remaining Distribution


		$remainDistribution = $db->view("*", "mlm_distribution_level", "distributionId", " and status ='unpaid'");

		if($remainDistribution['num_rows'] >= 1){

			foreach($remainDistribution['result'] as $remainDistributionRow){

				$remianMemberShipid = $remainDistributionRow['membership_id'];
				$curMemberResult = $db->view("*", "mlm_registrations", 'regid', " and membership_id ='$remianMemberShipid'");

				if($curMemberResult['result'][0]['first_purchase'] == '1'){
					
					$checkMembersCondition = $db->view("*", "mlm_registrations", "regid", " and first_purchase ='1' and sponsor_id = '$remianMemberShipid'");

					if($checkMembersCondition['num_rows'] >= $remainDistributionRow['levelid']){

						$amount = $remainDistributionRow['amount'];
						$remcMemberid = $remainDistributionRow['ownmemberid'];
						$regid = $remainDistributionRow['regid'];
						$refno = substr(md5(rand(1, 99999)),0,10);
						$reason = $remainDistributionRow['reason'];
						$description = $remainDistributionRow['discription'];
						$status = "fullfilled";

						$fields = array('regid' => $remainDistributionRow['regid'], "level" => $remainDistributionRow['levelid'], "purchaseid" => $remainDistributionRow['purchaseid'],'membership_id' => $remainDistributionRow['membership_id'], 'username' => $remainDistributionRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'debit', 'reason' => $reason, 'description' => $description, 'status' => $status,  'createtime' => $createtime, 'createdate' => $createdate);

						$db->insert("mlm_transactions", $fields);

						$fields2 = array('regid' => $remainDistributionRow['regid'], "level" => $remainDistributionRow['levelid'], "purchaseid" => $remainDistributionRow['purchaseid'], 'membership_id' => $remainDistributionRow['membership_id'], 'username' => $remainDistributionRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'credit', 'reason' => $reason, 'description' => $description, 'status' => $status, 'createtime' => $createtime, 'createdate' => $createdate);
						
						$db->insert("mlm_ewallet", $fields2);
						
						$db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount}, wallet_total = wallet_total+{$amount} where regid='{$regid}'");

						$distributionId = $remainDistributionRow['distributionId'];
						$db->custom("update mlm_distribution_level set status ='paid' where distributionId='{$distributionId}'");

					}
				}




			}
		}


		// Level Income Distribution - Total Amount

		if($purchaseQueryResult['num_rows'] >= 1){

			foreach($purchaseQueryResult['result'] as $purchaseRow)
			{
				if($purchaseRow['business_volume'] != "" && $purchaseRow['business_volume'] != 0){
					$total_amount +=  $purchaseRow['business_volume'];
				}
			}
		
		}

				
		$slr = 1; 
		$cMemberid = $membership_id;
		
		//Level Income distribution
		function getAllDownlines($parent)
		{
			
			global $db, $slr, $total_amount ,$cMemberid, $purchaseid, $createdate, $createtime;
			$dataResult = $db->view('*', 'mlm_registrations', 'regid', "and membership_id IN('$parent')", 'regid asc');
			$children = array();
			if($dataResult['num_rows'] >= 1)
			{

				foreach($dataResult['result'] as $memberRow)
				{

					$levelResult = $db->view("*", "mlm_levels", "levelid", " and status = 'active' and order_custom = '$slr'");

					if($levelResult['num_rows'] >= 1)
					{
						
						$levelRow = $levelResult['result'][0];
						
						$levelPercentage = $levelRow['percentage'];

						if($levelPercentage != "0.00" && $levelPercentage != "" && $total_amount != "0.00" && $total_amount != ""){

							$amount = round($total_amount * ($levelPercentage / 100));
							$regid = $memberRow['regid'];
							$refno = substr(md5(rand(1, 99999)),0,10);
							$reason = "LEVEL INCOME";
							$description = "Level {$slr} Income for Purchase By {$cMemberid}";
							$status = "fullfilled";
							$checkMembershipId = $memberRow['membership_id'];
							

							// Condition - Check Members are completed or not

							$checkMembersCondition = $db->view("*", "mlm_registrations", "regid", " and first_purchase ='1' and sponsor_id = '$checkMembershipId'");

							if($memberRow['first_purchase'] == "1" && $checkMembersCondition['num_rows'] >= $slr){

								$fields = array('regid' => $memberRow['regid'], "level" => $levelRow['levelid'], "purchaseid" => $purchaseid,'membership_id' => $memberRow['membership_id'], 'username' => $memberRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'debit', 'reason' => $reason, 'description' => $description, 'status' => $status,  'createtime' => $createtime, 'createdate' => $createdate);

								$db->insert("mlm_transactions", $fields);

								$fields2 = array('regid' => $memberRow['regid'], "level" => $levelRow['levelid'], "purchaseid" => $purchaseid, 'membership_id' => $memberRow['membership_id'], 'username' => $memberRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'credit', 'reason' => $reason, 'description' => $description, 'status' => $status, 'createtime' => $createtime, 'createdate' => $createdate);
								
								$db->insert("mlm_ewallet", $fields2);
								
								$db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount}, wallet_total = wallet_total+{$amount} where regid='{$regid}'");

								$distributionArray = array('refno' => $refno, 'regid' => $memberRow['regid'], 'membership_id' =>  $memberRow['membership_id'], 'username' => $memberRow['username'], 'purchaseid' => $purchaseid, 'ownmemberid' => $cMemberid, 'amount' => $amount, 'levelid' => $levelRow['levelid'], 'reason' => $reason, 'discription' => $description, 'status' => 'paid', 'createdate' => $createdate, 'createtime' => $createtime);

								$db->insert("mlm_distribution_level", $distributionArray);
								
							}else{
								
								$distributionArray = array('refno' => $refno, 'regid' => $memberRow['regid'], 'membership_id' =>  $memberRow['membership_id'], 'username' => $memberRow['username'], 'purchaseid' => $purchaseid, 'ownmemberid' => $cMemberid, 'amount' => $amount, 'levelid' => $levelRow['levelid'], 'reason' => $reason, 'discription' => $description, 'status' => 'unpaid', 'createdate' => $createdate, 'createtime' => $createtime);

								$db->insert("mlm_distribution_level", $distributionArray);


							}
							

						}
					
					}
					else
					{
						return "";
					}
							
				}
				$slr++;
				getAllDownlines($memberRow['sponsor_id']); 
			}
			return $children;
		}

		$memberCountResult = $db->view('*', 'mlm_registrations', 'regid', " and membership_id='{$sponsor_id}'");
		if($memberCountResult['num_rows'] >= 1 && $sponsor_id != "")
		{   
			$memberRow = $memberCountResult['result'][0];
			getAllDownlines($memberRow['membership_id']);
		}

	}
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: purchase_view.php$search_filter");
	exit();
}
?>