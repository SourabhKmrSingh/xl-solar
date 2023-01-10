<?php 
include_once("inc_config.php");

$repurchaseResult = $db->view("repurchaseid, title, percentage, order_custom as level, status", "mlm_repurchase", "repurchaseid", " and status = 'active' ", "order_custom asc");

if($repurchaseResult['num_rows'] >= 1){

	// Total Amount 
	// Run a Query to find all the entry of delivered in rb_purchase at that particular day - Sum of That Amount

	$curSalesResult = $db->view("sum(business_volume) as total_sales", "rb_purchases", "purchaseid", " and tracking_status = 'delivered' and invoicedate = '$createdate' and repurchase_check = '0' and income_type = 'repurchase'"); // Current Date While using in Prod

	$total_sales = $curSalesResult['result'][0]['total_sales'];

	// Levels That has been Clear
	// For loop through all the registered members

	function getAllDownlines($parent)

	{

		global $db, $slr, $check, $ht, $checkedMembers;	

		$memberResult = $db->view('membership_id,sponsor_id,imgName,username,status,mobile', 'mlm_registrations', 'regid', "and sponsor_id='$parent' and first_purchase = '1'", 'regid asc');

		if($memberResult['num_rows'] >= 1)

		{

			foreach($memberResult['result'] as $memberRow)

			{
				$membership_id = $memberRow['membership_id'];
				$checkedMembers["{$membership_id}"] = $slr;

				$slr++;
				getAllDownlines($memberRow['membership_id']);
				$sponsor_id = $memberRow['sponsor_id'];
				$data =  $checkedMembers["{$sponsor_id}"] + 1;
				if($ht < $slr){
					$ht = $slr - $check;
				}
				if($data != "" && ($data + 1) > 0){
					$check = 1;
					$slr = $checkedMembers["{$sponsor_id}"] + 1;
				}else{
					$slr = 0;
				}

			}
		}

	}

	$htMemeberData = [];

	$registerMemberResult = $db->view("regid, first_purchase,membership_id, first_name, last_name, sponsor_id, status", "mlm_registrations", "regid", " and status = 'active'");

	if($registerMemberResult['num_rows'] >= 1){
		foreach($registerMemberResult['result'] as $registerMemberRow){
			if($registerMemberRow['first_purchase'] == 1){
				$ht = 0;
				$slr = 0;
				$check = 0;
				$checkedMembers = [];
				$cMember = $registerMemberRow['membership_id'];
				getAllDownlines($registerMemberRow['membership_id']);
				$htMemeberData["{$cMember}"] = $ht;
			}
		}		
	}

	

	foreach($repurchaseResult['result'] as $repurchaseRow)
	{

		$level = $repurchaseRow['level'];

		if($level != 0 && $level != ""){

			$clearedMembers = array_filter($htMemeberData, function ($var){ global $level; return $var > $level - 1; });

			if(count($clearedMembers) > 0){

				$percentage = $repurchaseRow['percentage'];

				if($percentage != "0.00" && $percentage != "" && $total_sales != "0.00" && $total_sales != ""){
						
					$amount = round(($total_sales * ($percentage / 100)) / count($clearedMembers));

					foreach($clearedMembers as $curMemberid => $val){

						$curMemberResult = $db->view("*", "mlm_registrations", "regid", " and status = 'active' and membership_id = '$curMemberid'");
						
						if($curMemberResult['num_rows'] >= 1){

							$curMemberRow = $curMemberResult['result'][0];

							$regid = $curMemberRow['regid'];
							$refno = substr(md5(rand(1, 99999)),0,10); 
							$reason = "REPURCHASE INCOME";
							$description = "Repurchase Income for Level {$level}";
							$status = "fullfilled";
			
							$fields = array('regid' => $curMemberRow['regid'], "level" => $repurchaseRow['repurchaseid'],'membership_id' => $curMemberRow['membership_id'], 'username' => $curMemberRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'debit', 'reason' => $reason, 'description' => $description, 'status' => $status,  'createtime' => $createtime, 'createdate' => $createdate);
			
			
							$db->insert("mlm_transactions", $fields);
			
							$fields2 = array('regid' => $curMemberRow['regid'], "level" => $levelRow['levelid'], 'membership_id' => $curMemberRow['membership_id'], 'username' => $curMemberRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'credit', 'reason' => $reason, 'description' => $description, 'status' => $status, 'createtime' => $createtime, 'createdate' => $createdate);
							
							$db->insert("mlm_ewallet", $fields2);
							
							$db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount}, wallet_total = wallet_total+{$amount} where regid='{$regid}'");

						}

					}

				}
			}else{
				break;
			}
		}

	}

}



// Update Repurchase 


$db->custom("UPDATE rb_purchases SET repurchase_check = 1 WHERE repurchase_check = 0 and tracking_status = 'delivered'");


// wrapper array 
	// Level clear function 
		// getdownlinemember - run 
		// if Lower Level user doesn't have any children assign the $slr value to the $hl point and also reset the $slr value
		// return value as an array ["membership_id" => "LevelID"] value in wrapper --
		

// Total No and IDs of Members that have clered Levels
    // Convert 


// Calculate Amount and Distribute that amount to every Member in That Particular Level
// --------------------------------------------------------------------------------------------------------------------



?>