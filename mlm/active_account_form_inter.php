<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "activateAccount";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: generate_pins_view.php");
	exit();
}

$memberAccount =  $validation->input_validate($_POST['memberAccount']);
$membership_id = $validation->input_validate($_POST['membership_id']);
$username = $validation->input_validate($_POST['username']);
$mobile = $validation->input_validate($_POST['mobile']);
$pin = $validation->input_validate($_POST['pin']);

if($memberAccount =="" || $membership_id == "" || $username == "" || $pin == ""){
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: active_account_view.php");
	exit();
}

$pinQuery = $db->view('*','mlm_activate_pins','pinid'," and membership_id ='{$memberAccount}' and pin ='{$pin}' and status='active'");

if($pinQuery['num_rows'] < 1){
	$_SESSION['error_msg'] = "Pins is already used!";
	header("Location: active_account_view.php");
	exit();
}


$memberQuery = $db->custom("UPDATE `mlm_registrations` SET `status`='active' WHERE membership_id ='{$membership_id}'");
if(!$memberQuery){
	$_SESSION['error_msg'] = "There is some problem during activation! Please try again later.";
	header("Location: active_account_view.php");
	exit();
}
$changeStatusPins = $db->custom("UPDATE `mlm_activate_pins` SET `status`='inactive' WHERE membership_id ='{$memberAccount}' and pin ='{$pin}' and status='active'");



$checkpurchaseResult = $db->view('*', 'mlm_registrations', 'regid', "and membership_id = '$membership_id' and first_purchase='0'");


if($checkpurchaseResult['num_rows'] == 1)
{
	$memberUpdate = $db->custom("update mlm_registrations set first_purchase = '1', status='active' where membership_id='{$membership_id}'");
	$mlmregisterResult = $db->view('*', 'mlm_registrations', 'regid', "and  membership_id='$membership_id' and first_purchase = '1'", 'regid desc', '1');
	$mlmregisterRow = $mlmregisterResult['result'][0];
	$planid = $mlmregisterRow['planid'];
	$regid = $mlmregisterRow['regid'];
	
	$planResult = $db->view('planid,title,amount', 'mlm_plans', 'planid', "and planid='$planid' and status='active'");

	if($planResult['num_rows'] >= 1)
	{
		$planRow = $planResult['result'][0];

		$amount = $planRow['amount'];
		$title = $planRow['title'];
		$refno = substr(md5(rand(1, 99999)),0,10);
		$reason = "Joining";
		$description = "Joining on purchasing of &#8377; {$amount}";
		$status = "fulfilled";
		$fields = array('userid'=>$userid, 'regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'refno'=>$refno, 'amount'=>$amount, 'type'=>'credit','pay_type'=>'1', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
		$transactionResult = $db->insert("mlm_transactions", $fields);
		if(!$transactionResult)
		{
			echo "Transaction History is not updated! Consult Administrator";
			exit();
		}
		
		$fields2 = array('userid'=>$userid, 'regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'refno'=>$refno, 'amount'=>$amount, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
		$ewalletResult = $db->insert("mlm_ewallet", $fields2);
		if(!$ewalletResult)
		{
			echo "E-Wallet History is not updated! Consult Administrator";
			exit();
		}
		
		$registerwalletResult = $db->custom("update mlm_registrations set total_debit = total_debit+{$amount}, activatetime = '$createtime', activatedate='$createdate' where regid='{$regid}'");
		if(!$registerwalletResult)
		{
			echo "Member Wallet is not added! Consult Administrator";
			exit();
		}
	}
	
	$referral_amount = $configRow['referral_amount'];
	
	if($referral_amount != '0' and $referral_amount != '0.00')
	{	
		$sponsor_id = $mlmregisterRow['sponsor_id'];
		$sponsorResult = $db->view('*', 'mlm_registrations', 'regid', " and status='active' and membership_id='{$sponsor_id}'");
		// update direct members
		$db->custom("update mlm_registrations set direct_member = direct_member + 1 where membership_id='{$sponsor_id}'");

		if($sponsorResult['num_rows'] >= 1)
		{
			$sponsorRow = $sponsorResult['result'][0];
			$sponsor_regid = $sponsorRow['regid'];
			$sponsor_username = $sponsorRow['username'];
			$sponsor_mem_id = $sponsorRow['membership_id'];
			$sponsorRewardId = $sponsorRow['rewardid'];
			$sponsor_Sponsor_ID = $sponsorRow['sponsor_id'];

			$amount = $referral_amount;
			$refno = substr(md5(rand(1, 99999)),0,10);
			$reason = "Referral Bonus";
			$description = "Referral Bonus for adding $membership_id.";
			$status = "fullfilled";
			$fields = array('userid'=>$userid, 'regid'=>$sponsor_regid, 'membership_id'=>$sponsor_mem_id, 'username'=>$sponsor_username, 'refno'=>$refno, 'amount'=>$amount, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
			$transactionResult = $db->insert("mlm_transactions", $fields);
			if(!$transactionResult)
			{
				echo "Transaction History is not updated! Consult Administrator";
				exit();
			}
			
			$fields2 = array('userid'=>$userid, 'regid'=>$sponsor_regid, 'membership_id'=>$sponsor_mem_id, 'username'=>$sponsor_username, 'refno'=>$refno, 'amount'=>$amount, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
			$ewalletResult = $db->insert("mlm_ewallet", $fields2);
			if(!$ewalletResult)
			{
				echo "E-Wallet History is not updated! Consult Administrator";
				exit();
			}
			
			$registerwalletResult = $db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount}, wallet_total = wallet_total+{$amount} where regid='{$sponsor_regid}'");
			if(!$registerwalletResult)
			{
				echo "Member Wallet is not added! Consult Administrator";
				exit();
			}

				//Level Income 
				$slr = 1;
			
				function getAllDownlines($parent)
				{
					
					global $db, $slr,$referral_amount,$membership_id, $createdate, $createtime;
					$dataResult = $db->view('*', 'mlm_registrations', 'regid', " and membership_id IN('$parent')", 'regid asc');
					$children = array();
					if($dataResult['num_rows']>=1)
					{
						foreach($dataResult['result'] as $memberRow)
						{
							if($slr == 7){
								return $children;
							}
	
							if($slr == 1){
								$amount = 20;
							}else{
								$amount = 10;
							}
							if($memberRow['status'] == 'active'){
								$regid = $memberRow['regid'];
								$refno = substr(md5(rand(1, 99999)),0,10);
								$reason = "LEVEL INCOME";
								$description = "Level Income for adding new Members ({$membership_id})";
								$status = "fullfilled";
								$fields = array('regid'=>$memberRow['regid'], 'membership_id'=>$memberRow['membership_id'], 'username'=>$memberRow['username'], 'refno'=>$refno, 'amount'=>$amount, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'createtime'=>$createtime, 'createdate'=>$createdate);
								$transactionResult = $db->insert("mlm_transactions", $fields);
								if(!$transactionResult)
								{
									echo "Transaction History is not updated! Consult Administrator";
									exit();
								}
								
								$fields2 = array('regid'=>$memberRow['regid'], 'membership_id'=>$memberRow['membership_id'], 'username'=>$memberRow['username'], 'refno'=>$refno, 'amount'=>$amount, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'createtime'=>$createtime, 'createdate'=>$createdate);
								$ewalletResult = $db->insert("mlm_ewallet", $fields2);
								if(!$ewalletResult)
								{
									echo "E-Wallet History is not updated! Consult Administrator";
									exit();
								}
								
								$registerwalletResult = $db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount}, wallet_total = wallet_total+{$amount} where regid='{$regid}'");
								if(!$registerwalletResult)
								{
									echo "Member Wallet is not added! Consult Administrator";
									exit();
								}
	
							}
							
							$children[$parent][$memberRow['sponsor_id']] = array();
							$new_father_ids[] = $memberRow['sponsor_id'];
						}
						$slr++;
						$children = array_merge($children, getAllDownlines($memberRow['sponsor_id'])); 
					}
					return $children;
				}
	
				$memberCountResult = $db->view('*', 'mlm_registrations', 'regid', " and membership_id='{$sponsor_Sponsor_ID}'");
				if($memberCountResult['num_rows'] >= 1)
				{   
					$memberRow = $memberCountResult['result'][0];
					getAllDownlines($memberRow['membership_id']);
				}
		}
	}	
	
	
	//for Challenges 
	$challengeQuery= $db->view('*','mlm_challenges','challengeid');
	$challengeResultRow = $challengeQuery['result'][0];

	if($challengeQuery['num_rows']>=1){

		$sponsor_id = $mlmregisterRow['sponsor_id'];
		$sponsorResult = $db->view('*', 'mlm_registrations', 'regid', " and status='active' and membership_id='{$sponsor_id}'");
		$sponsorResultRow = $sponsorResult['result'][0];
		
		if($sponsorResultRow['challenge_id'] <= '0'){
			
			$time = $challengeResultRow['time_period'];
			$members = $challengeResultRow['members'];
			$timeperiod = date('Y-m-d', strtotime($createdate . " - {$time} days"));
			
			$checkMemberQuery = $db->view('*','mlm_registrations','regid', "  and status='active' and createdate BETWEEN '{$timeperiod}' and '{$createdate}' and sponsor_id = '{$sponsor_id}' ");
			
			if($checkMemberQuery['num_rows'] >= $members){
				$regid= $sponsorResultRow['regid'];
				$challengeid = $challengeResultRow['challengeid'];
				$username = $sponsorResultRow['username'];
				$refno = substr(md5(rand(1, 99999)),0,10);
				$description = "For adding {$members} in {$time} days";
				$reward = "3% company revenue distribution!"; 

				$fields = array('regid'=>$regid,"challengeid"=>$challengeid,'membership_id'=> $sponsor_id,'username'=>$username,'refno'=>$refno,'time_period'=> $time,'members'=>$members,'reward'=> $reward,'description'=> $description);
				$fields['status'] = 'pending';
				$fields['createtime'] = $createtime;
				$fields['createdate'] = $createdate;
				
				$challengeHistory = $db->insert('mlm_challenges_history', $fields);
				$memberUpdate = $db->update('mlm_registrations',array('challenge_id' => "{$challengeid}"),array('regid'=>$regid));
			}
		}

		// if($sponsorResultRow['challenge_id2'] <= '0'){
		// 	$time = $challengeResultRow['time_period'];
		// 	$members = $challengeResultRow['members'];
		// 	$expirydate = $challengeResultRow['expiry_date'];
			
		// 	$checkMemberQuery = $db->view('*','mlm_registrations','regid', "  and status='active' and createdate < '{$expirydate}' sponsor_id = '{$sponsor_id}' ");
			
		// 	if($checkMemberQuery['num_rows'] >= $members){
		// 		$regid= $sponsorResultRow['regid'];
		// 		$challengeid = $challengeResultRow['challengeid'];
		// 		$username = $sponsorResultRow['username'];
		// 		$refno = substr(md5(rand(1, 99999)),0,10);
		// 		$description = "For adding {$members} before {$expirydate}";
		// 		$reward = $challengeResultRow['reward']; 

		// 		$fields = array('regid'=>$regid,"challengeid"=>$challengeid,'membership_id'=> $sponsor_id,'username'=>$username,'refno'=>$refno,'time_period'=> $time,'members'=>$members,'reward'=> $reward,'description'=> $description);
		// 		$fields['status'] = 'pending';
		// 		$fields['createtime'] = $createtime;
		// 		$fields['createdate'] = $createdate;
				
		// 		$challengeHistory = $db->insert('mlm_challenges_history', $fields);
		// 		$memberUpdate = $db->update('mlm_registrations',array('challenge_id2' => "{$challengeid}"),array('regid'=>$regid));
		// }
	}


	function getAllDownlines1($parent)
	{
		global $db, $referral_amount, $createdate, $createtime;
		$dataResult = $db->view('*', 'mlm_registrations', 'regid', "and status='active' and membership_id IN('$parent') and first_purchase = '1'", 'regid asc');
		$children = array();
		$countUpdateResult = $db->custom("update mlm_registrations set members = members+1, current_member = current_member+1  where membership_id='{$parent}'");


		if (!$countUpdateResult) {
			echo "Member Count is not updated! Consult Administrator";
			exit();
		}
		if($dataResult['num_rows']>=1)
		{
			foreach($dataResult['result'] as $memberRow)
			{
				// $rewardResult = $db->view('*', 'mlm_rewards', 'rewardid', "and status='active' and rewardid > '{$memberRow['rewardid']}'",'','1');
				
				// if ($rewardResult['num_rows'] >= 1) {
				// 	$rewardRow = $rewardResult['result'][0];
				// 	$members = $rewardRow['members'];
				// 	$direct_members = $rewardRow['direct_members'];
				// 	$amount = $rewardRow['amount'];
				// 	$rewardid = $rewardRow['rewardid'];
				// 	$rewardTitle = $rewardRow['title'];
				// 	$rewardTime = $rewardRow['time_period'];

				// 	$registerDate = $memberRow['createdate'];
				// 	$registertime = $memberRow['createtime'];

				// 	$singleLegTeamQuery =  $db->view('*','mlm_registrations','regid'," and concat(createdate,' ',createtime) > '{$registerDate} {$registertime}' and status='active'");

				// 	$singleLegTeam = $singleLegTeamQuery['num_rows'];

					
				
				// 	if ($amount != "") {
				// 		if($singleLegTeam >= $members && $memberRow['direct_member'] >= $direct_members){
				// 			$refno = substr(md5(rand(1, 99999)), 0, 10);
				// 			$reward = "{$amount} Rs. For {$rewardTime} days";
				// 			$description = "Reward for reaching {$rewardTitle}";
				// 			$reward_end_date = date('Y-m-d', strtotime($createdate . " + {$rewardTime} days"));

				// 			$fields4 = array('regid' => $memberRow['regid'],'rewardid'=>$rewardid ,'membership_id' => $memberRow['membership_id'], 'username' => $memberRow['username'], 'refno' => $refno, 'members' => $members,'reward'=>$reward, 'description' => $description, 'createtime' => $createtime, 'createdate' => $createdate);
				// 			$rewardHistory = $db->insert("mlm_rewards_history", $fields4);

				// 			$fields_rewards = array('regid'=>$memberRow['regid'], 'membershipId'=>$memberRow['membership_id'], 'reward_id'=>$rewardid, 'reward_amount'=>$amount, 'reward_end_date'=>$reward_end_date);

				// 			$fields_rewards['createdate'] = $createdate;
				// 			$fields_rewards['createtime'] = $createtime;

				// 			$rewardQueryResult = $db->insert('mlm_reward_details', $fields_rewards);

				// 			$registerRewardResult = $db->custom("update mlm_registrations set rewardid = {$rewardid}, direct_member = '0', current_member = '0' where regid ='{$memberRow['regid']}'");
				// 			if (!$registerRewardResult) {
				// 				echo "Member Wallet is not added! Consult Administrator";
				// 				exit();
				// 			}
				// 		}
				// 	}
				// }

				$children[$parent][$memberRow['sponsor_id']] = array();
				$new_father_ids[] = $memberRow['sponsor_id'];
			}
			$children = array_merge($children, getAllDownlines1($memberRow['sponsor_id'])); 
		}
		return $children;
	}

	$children = array();
	$memberCountResult = $db->view('membership_id,sponsor_id,members,status,regid,createdate,createtime,username,rewardid', 'mlm_registrations', 'regid', "and status='active' and membership_id='$membership_id' and first_purchase = '1'", 'regid asc');
	if($memberCountResult['num_rows'] >= 1)
	{
		$memberCountRow = $memberCountResult['result']['0'];
		$sponsor_id = $memberCountRow['sponsor_id'];
		getAllDownlines1($sponsor_id);
		
	}



}



	
$_SESSION['success_msg'] = "Account activeted successfully!";
header("Location: active_account_view.php");
exit();


?>