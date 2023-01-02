<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "register";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: register_view.php");
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
	if(isset($_GET['regid']))
	{
		$regid = $validation->urlstring_validate($_GET['regid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: register_view.php");
		exit();
	}
}

$old_membership_id = $validation->input_validate($_POST['old_membership_id']);
$old_membership_id_value = $validation->input_validate($_POST['old_membership_id_value']);
$member_check = $validation->input_validate($_POST['member_check']);
$sponsor_id = $validation->input_validate($_POST['sponsor_id']);
$planid = $validation->input_validate($_POST['planid']);
if($planid=='')
{
	$planid = 0;
}
$first_name = $validation->input_validate($_POST['first_name']);
$last_name = $validation->input_validate($_POST['last_name']);
$username = $validation->input_validate($_POST['username']);
$email = $validation->input_validate($_POST['email']);
$password = $validation->input_validate(sha1($_POST['password']));
$confirm_password = $validation->input_validate(sha1($_POST['confirm_password']));
$old_password = $validation->input_validate($_POST['old_password']);
$mobile = $validation->input_validate($_POST['mobile']);
$mobile_alter = $validation->input_validate($_POST['mobile_alter']);
$pin_code = $validation->input_validate($_POST['pin_code']);
if($pin_code=='')
{
	$pin_code = 0;
}
$bank_name = $validation->input_validate($_POST['bank_name']);
$account_number = $validation->input_validate($_POST['account_number']);
$ifsc_code = $validation->input_validate($_POST['ifsc_code']);
$account_name = $validation->input_validate($_POST['account_name']);
$pan_card = $validation->input_validate($_POST['pan_card']);
$aadhar_card = $validation->input_validate($_POST['aadhar_card']);
$remarks = $validation->input_validate($_POST['remarks']);
$status = $validation->input_validate($_POST['status']);
$old_imgName = $validation->input_validate($_POST['old_imgName']);

$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

if($_POST['password'] != "")
{
	if($password != $confirm_password)
	{
		$_SESSION['error_msg'] = "Password and Confirm Password should be Same!";
		header("Location: register_view.php");
		exit();
	}
}

$dupresult = $db->check_duplicates('mlm_registrations', 'regid', $regid, 'email', strtolower($email), $mode);
if($dupresult >= 1)
{
	$_SESSION['error_msg'] = "Email-ID already exists!";
	header("Location: register_view.php");
	exit();
}

$imgTName = $_FILES['imgName']['name'];
if($imgTName != "")
{
	$handle = new Upload($_FILES['imgName']);
    if($handle->uploaded)
	{
		$handle->file_force_extension = true;
		$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
		$handle->allowed = array('image/*');
		if($configRow['large_width'] != "0" and $configRow['large_height'] != "0")
		{
			$handle->image_resize = true;
			$handle->image_x = $validation->db_field_validate($configRow['large_width']);
			$handle->image_y = $validation->db_field_validate($configRow['large_height']);
			$handle->image_no_enlarging = ($configRow['large_ratio'] === "false") ? false : true;
			$handle->image_ratio = ($configRow['large_ratio'] === "false") ? false : true;
		}
		
		$handle->process(IMG_MAIN_LOC);
		if($handle->processed)
		{
			$imgName = $handle->file_dst_name;
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: register_view.php");
			exit();
		}
		
		// Thumbnail Image
		$handle->file_force_extension = true;
		$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
		$handle->allowed = array('image/*');
		if($configRow['thumb_width'] != "0" and $configRow['thumb_height'] != "0")
		{
			$handle->image_resize = true;
			$handle->image_x = $validation->db_field_validate($configRow['thumb_width']);
			$handle->image_y = $validation->db_field_validate($configRow['thumb_height']);
			$handle->image_no_enlarging = ($configRow['thumb_ratio'] === "false") ? false : true;
			$handle->image_ratio = ($configRow['thumb_ratio'] === "false") ? false : true;
		}
		
		$handle->process(IMG_THUMB_LOC);
		if($handle->processed)
		{
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: register_view.php");
			exit();
		}
		
		$handle-> clean();
	}
	else
	{
		$_SESSION['error_msg'] = $handle->error.'!';
		header("Location: register_view.php");
		exit();
    }
	
	if($mode == "edit")
	{
		$delresult = $media->filedeletion('mlm_registrations', 'regid', $regid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	}
}

if($old_membership_id == "")
{
	$membership_id = "";
	$current_year = date('Y');
	$current_month = date('m');
	$refResult = $db->view("MAX(membership_id_value) as membership_id_value", "mlm_registrations", "regid", "");
	$refRow = $refResult['result'][0];
	$membership_id_value = $refRow['membership_id_value'];
	$membership_id_value = $membership_id_value+1;
	$membership_id = sprintf("%03d", $membership_id_value);
	//$membership_id = "BT".$current_year."".$current_month."".$membership_id;
	$membership_id = "BT".$membership_id;
}
else
{
	$membership_id = $old_membership_id;
	$membership_id_value = $old_membership_id_value;
}

if($_POST['password'] == "")
{
	$password = $old_password;
}
if($imgName == "")
{
	$imgName = $old_imgName;
}

$fields = array('membership_id'=>$membership_id, 'membership_id_value'=>$membership_id_value, 'sponsor_id'=>$sponsor_id, 'planid'=>$planid, 'first_name'=>$first_name, 'last_name'=>$last_name, 'username'=>$username, 'email'=>$email, 'password'=>$password, 'mobile'=>$mobile, 'mobile_alter'=>$mobile_alter, 'pin_code'=>$pin_code, 'bank_name'=>$bank_name, 'account_number'=>$account_number, 'ifsc_code'=>$ifsc_code, 'account_name'=>$account_name, 'pan_card'=>$pan_card, 'aadhar_card'=>$aadhar_card, 'imgName'=>$imgName, 'remarks'=>$remarks, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$registerQueryResult = $db->insert("mlm_registrations", $fields);
	if(!$registerQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
}
else if($mode == "edit")
{
	if($member_check == "0" and $status == "active")
	{
		$fields['member_check'] = "1";
	}
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$registerQueryResult = $db->update("mlm_registrations", $fields, array('regid'=>$regid));
	if(!$registerQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Updated!";
}

if($member_check == "0" and $status == "active")
{
	$planResult = $db->view('planid,title,amount', 'mlm_plans', 'planid', "and planid='$planid'");
	if($planResult['num_rows'] >= 1)
	{
		$planRow = $planResult['result'][0];
		$amount = $planRow['amount'];
		$title = $planRow['title'];
		$refno = substr(md5(rand(1, 99999)),0,22);
		$reason = "Joining";
		$description = "Joining for {$title}";
		$status = "fulfilled";
		$fields = array('userid'=>$userid, 'regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'refno'=>$refno, 'amount'=>$amount, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
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
	}
	
	$memberCountResult1 = $db->view('membership_id,sponsor_id,members,status,regid,createdate,createtime,username', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id'", 'regid asc', '3');
	if($memberCountResult1['num_rows'] >= 1)
	{
		foreach($memberCountResult1['result'] as $memberCountRow1)
		{
			$updated_members1 = $memberCountRow1['members']+1;
			$createtime_member1 = $memberCountRow1['createtime'];
			$createdate_member1 = $memberCountRow1['createdate'];
			$regid1 = $memberCountRow1['regid'];
			$membership_id1 = $memberCountRow1['membership_id'];
			$username1 = $memberCountRow1['username'];
			$memberRewardResult1 = $db->view('members,rewardid,amount', 'mlm_rewards', 'rewardid', "and planid='$planid' and members='$updated_members1'", 'rewardid asc', '1');
			if($memberRewardResult1['num_rows'] >= 1)
			{
				$memberRewardRow1 = $memberRewardResult1['result'][0];
				$rewardid1 = $memberRewardRow1['rewardid'];
				$amount1 = $memberRewardRow1['amount'];
				$members1 = $memberRewardRow1['members'];
				$query1 = ", rewardid='{$rewardid1}'";
				
				$refno1 = substr(md5(rand(1, 99999)),0,22);
				$reason = "Reward";
				$description = "Reward for completing {$members1} members";
				$status = "pending";
				$fields = array('userid'=>$userid, 'regid'=>$regid1, 'membership_id'=>$membership_id1, 'username'=>$username1, 'refno'=>$refno1, 'amount'=>$amount1, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
				$transactionResult = $db->insert("mlm_transactions", $fields);
				
				$fields2 = array('userid'=>$userid, 'regid'=>$regid1, 'membership_id'=>$membership_id1, 'username'=>$username1, 'refno'=>$refno1, 'amount'=>$amount1, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
				$ewalletResult = $db->insert("mlm_ewallet", $fields2);
			}
			$challengeResult1 = $db->view('challengeid,time_period,members,reward', 'mlm_challenges', 'challengeid', "and timestampdiff(DAY, '{$createdate_member1} {$createtime_member1}', now()) <= time_period and planid='$planid' and members='$updated_members1'", "time_period desc", "1");
			if($challengeResult1['num_rows'] >= 1)
			{
				$challengeRow1 = $challengeResult1['result'][0];
				$challengeid = $challengeRow1['challengeid'];
				$time_period = $challengeRow1['time_period'];
				$members1 = $challengeRow1['members'];
				$reward = $challengeRow1['reward'];
				$description = "Reward for completing challenge of {$members1} members in {$time_period} days";
				$status = "achieved";
				$refno1 = substr(md5(rand(1, 99999)),0,22);
				$fields3 = array('userid'=>$userid, 'regid'=>$regid1, 'challengeid'=>$challengeid, 'membership_id'=>$membership_id1, 'username'=>$username1, 'refno'=>$refno1, 'time_period'=>$time_period, 'members'=>$members1, 'reward'=>$reward, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
				$challengeHistoryResult = $db->insert("mlm_challenges_history", $fields3);
			}
			$membership_id_history1 = $memberCountRow1['membership_id'];
			$sponsor_id_history1 = $memberCountRow1['sponsor_id'];
			$fields4 = array('userid'=>$userid, 'regid'=>$regid1, 'membership_id'=>$membership_id_history1, 'sponsor_id'=>$sponsor_id_history1, 'member'=>'1', 'total_members'=>$updated_members1, 'description'=>'', 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
			$registrationHistoryResult1 = $db->insert("mlm_registrations_history", $fields4);
			if(!$registrationHistoryResult1)
			{
				echo "Members History is not updated! Consult Administrator";
				exit();
			}
			$countUpdateResult1 = $db->custom("update mlm_registrations set members = members+1 {$query1} where membership_id='{$sponsor_id}'");
			if(!$countUpdateResult1)
			{
				echo "Member Count is not updated! Consult Administrator";
				exit();
			}
			// end of loop
			
			$sponsor_id2 = $memberCountRow1['sponsor_id'];
			$memberCountResult2 = $db->view('membership_id,sponsor_id,members,status,regid,createdate,createtime,username', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id2'", 'regid asc', '3');
			if($memberCountResult2['num_rows'] >= 1)
			{
				foreach($memberCountResult2['result'] as $memberCountRow2)
				{
					$updated_members2 = $memberCountRow2['members']+1;
					$createtime_member2 = $memberCountRow2['createtime'];
					$createdate_member2 = $memberCountRow2['createdate'];
					$regid2 = $memberCountRow2['regid'];
					$membership_id2 = $memberCountRow2['membership_id'];
					$username2 = $memberCountRow2['username'];
					$memberRewardResult2 = $db->view('members,rewardid,amount', 'mlm_rewards', 'rewardid', "and planid='$planid' and members='$updated_members2'", 'rewardid asc', '1');
					if($memberRewardResult2['num_rows'] >= 1)
					{
						$memberRewardRow2 = $memberRewardResult2['result'][0];
						$rewardid2 = $memberRewardRow2['rewardid'];
						$amount2 = $memberRewardRow2['amount'];
						$members2 = $memberRewardRow2['members'];
						$query2 = ", rewardid='{$rewardid2}'";
						
						$refno2 = substr(md5(rand(1, 99999)),0,22);
						$reason = "Reward";
						$description = "Reward for completing {$members2} members";
						$status = "pending";
						$fields = array('userid'=>$userid, 'regid'=>$regid2, 'membership_id'=>$membership_id2, 'username'=>$username2, 'refno'=>$refno2, 'amount'=>$amount2, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
						$transactionResult = $db->insert("mlm_transactions", $fields);
						
						$fields2 = array('userid'=>$userid, 'regid'=>$regid2, 'membership_id'=>$membership_id2, 'username'=>$username2, 'refno'=>$refno2, 'amount'=>$amount2, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
						$ewalletResult = $db->insert("mlm_ewallet", $fields2);
					}
					$challengeResult2 = $db->view('challengeid,time_period,members,reward', 'mlm_challenges', 'challengeid', "and timestampdiff(DAY, '{$createdate_member2} {$createtime_member2}', now()) <= time_period and planid='$planid' and members='$updated_members2'", "time_period desc", "1");
					if($challengeResult2['num_rows'] >= 1)
					{
						$challengeRow2 = $challengeResult2['result'][0];
						$challengeid = $challengeRow2['challengeid'];
						$time_period = $challengeRow2['time_period'];
						$members2 = $challengeRow2['members'];
						$reward = $challengeRow2['reward'];
						$description = "Reward for completing challenge of {$members2} members in {$time_period} days";
						$status = "achieved";
						$refno2 = substr(md5(rand(1, 99999)),0,22);
						$fields3 = array('userid'=>$userid, 'regid'=>$regid2, 'challengeid'=>$challengeid, 'membership_id'=>$membership_id2, 'username'=>$username2, 'refno'=>$refno2, 'time_period'=>$time_period, 'members'=>$members2, 'reward'=>$reward, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
						$challengeHistoryResult = $db->insert("mlm_challenges_history", $fields3);
					}
					$membership_id_history2 = $memberCountRow2['membership_id'];
					$sponsor_id_history2 = $memberCountRow2['sponsor_id'];
					$fields4 = array('userid'=>$userid, 'regid'=>$regid2, 'membership_id'=>$membership_id_history2, 'sponsor_id'=>$sponsor_id_history2, 'member'=>'1', 'total_members'=>$updated_members2, 'description'=>'', 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
					$registrationHistoryResult2 = $db->insert("mlm_registrations_history", $fields4);
					if(!$registrationHistoryResult2)
					{
						echo "Members History is not updated! Consult Administrator";
						exit();
					}
					$countUpdateResult2 = $db->custom("update mlm_registrations set members = members+1 {$query2} where membership_id='{$sponsor_id2}'");
					if(!$countUpdateResult2)
					{
						echo "Member Count is not updated! Consult Administrator";
						exit();
					}
					// end of loop
					
					$sponsor_id3 = $memberCountRow2['sponsor_id'];
					$memberCountResult3 = $db->view('membership_id,sponsor_id,members,status,regid,createdate,createtime,username', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id3'", 'regid asc', '3');
					if($memberCountResult3['num_rows'] >= 1)
					{
						foreach($memberCountResult3['result'] as $memberCountRow3)
						{
							$updated_members3 = $memberCountRow3['members']+1;
							$createtime_member3 = $memberCountRow3['createtime'];
							$createdate_member3 = $memberCountRow3['createdate'];
							$regid3 = $memberCountRow3['regid'];
							$membership_id3 = $memberCountRow3['membership_id'];
							$username3 = $memberCountRow3['username'];
							$memberRewardResult3 = $db->view('members,rewardid,amount', 'mlm_rewards', 'rewardid', "and planid='$planid' and members='$updated_members3'", 'rewardid asc', '1');
							if($memberRewardResult3['num_rows'] >= 1)
							{
								$memberRewardRow3 = $memberRewardResult3['result'][0];
								$rewardid3 = $memberRewardRow3['rewardid'];
								$amount3 = $memberRewardRow3['amount'];
								$members3 = $memberRewardRow3['members'];
								$query3 = ", rewardid='{$rewardid3}'";
								
								$refno3 = substr(md5(rand(1, 99999)),0,22);
								$reason = "Reward";
								$description = "Reward for completing {$members3} members";
								$status = "pending";
								$fields = array('userid'=>$userid, 'regid'=>$regid3, 'membership_id'=>$membership_id3, 'username'=>$username3, 'refno'=>$refno3, 'amount'=>$amount3, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
								$transactionResult = $db->insert("mlm_transactions", $fields);
								
								$fields2 = array('userid'=>$userid, 'regid'=>$regid3, 'membership_id'=>$membership_id3, 'username'=>$username3, 'refno'=>$refno3, 'amount'=>$amount3, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
								$ewalletResult = $db->insert("mlm_ewallet", $fields2);
							}
							$challengeResult3 = $db->view('challengeid,time_period,members,reward', 'mlm_challenges', 'challengeid', "and timestampdiff(DAY, '{$createdate_member3} {$createtime_member3}', now()) <= time_period and planid='$planid' and members='$updated_members3'", "time_period desc", "1");
							if($challengeResult3['num_rows'] >= 1)
							{
								$challengeRow3 = $challengeResult3['result'][0];
								$challengeid = $challengeRow3['challengeid'];
								$time_period = $challengeRow3['time_period'];
								$members3 = $challengeRow3['members'];
								$reward = $challengeRow3['reward'];
								$description = "Reward for completing challenge of {$members3} members in {$time_period} days";
								$status = "achieved";
								$refno3 = substr(md5(rand(1, 99999)),0,22);
								$fields3 = array('userid'=>$userid, 'regid'=>$regid3, 'challengeid'=>$challengeid, 'membership_id'=>$membership_id3, 'username'=>$username3, 'refno'=>$refno3, 'time_period'=>$time_period, 'members'=>$members3, 'reward'=>$reward, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
								$challengeHistoryResult = $db->insert("mlm_challenges_history", $fields3);
							}
							$membership_id_history3 = $memberCountRow3['membership_id'];
							$sponsor_id_history3 = $memberCountRow3['sponsor_id'];
							$fields4 = array('userid'=>$userid, 'regid'=>$regid3, 'membership_id'=>$membership_id_history3, 'sponsor_id'=>$sponsor_id_history3, 'member'=>'1', 'total_members'=>$updated_members3, 'description'=>'', 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
							$registrationHistoryResult3 = $db->insert("mlm_registrations_history", $fields4);
							if(!$registrationHistoryResult3)
							{
								echo "Members History is not updated! Consult Administrator";
								exit();
							}
							$countUpdateResult3 = $db->custom("update mlm_registrations set members = members+1 {$query3} where membership_id='{$sponsor_id3}'");
							if(!$countUpdateResult3)
							{
								echo "Member Count is not updated! Consult Administrator";
								exit();
							}
							// end of loop
							
							$sponsor_id4 = $memberCountRow3['sponsor_id'];
							$memberCountResult4 = $db->view('membership_id,sponsor_id,members,status,regid,createdate,createtime,username', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id4'", 'regid asc', '3');
							if($memberCountResult4['num_rows'] >= 1)
							{
								foreach($memberCountResult4['result'] as $memberCountRow4)
								{
									$updated_members4 = $memberCountRow4['members']+1;
									$createtime_member4 = $memberCountRow4['createtime'];
									$createdate_member4 = $memberCountRow4['createdate'];
									$regid4 = $memberCountRow4['regid'];
									$membership_id4 = $memberCountRow4['membership_id'];
									$username4 = $memberCountRow4['username'];
									$memberRewardResult4 = $db->view('members,rewardid,amount', 'mlm_rewards', 'rewardid', "and planid='$planid' and members='$updated_members4'", 'rewardid asc', '1');
									if($memberRewardResult4['num_rows'] >= 1)
									{
										$memberRewardRow4 = $memberRewardResult4['result'][0];
										$rewardid4 = $memberRewardRow4['rewardid'];
										$amount4 = $memberRewardRow4['amount'];
										$members4 = $memberRewardRow4['members'];
										$query4 = ", rewardid='{$rewardid4}'";
										
										$refno4 = substr(md5(rand(1, 99999)),0,22);
										$reason = "Reward";
										$description = "Reward for completing {$members4} members";
										$status = "pending";
										$fields = array('userid'=>$userid, 'regid'=>$regid4, 'membership_id'=>$membership_id4, 'username'=>$username4, 'refno'=>$refno4, 'amount'=>$amount4, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
										$transactionResult = $db->insert("mlm_transactions", $fields);
										
										$fields2 = array('userid'=>$userid, 'regid'=>$regid4, 'membership_id'=>$membership_id4, 'username'=>$username4, 'refno'=>$refno4, 'amount'=>$amount4, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
										$ewalletResult = $db->insert("mlm_ewallet", $fields2);
									}
									$challengeResult4 = $db->view('challengeid,time_period,members,reward', 'mlm_challenges', 'challengeid', "and timestampdiff(DAY, '{$createdate_member4} {$createtime_member4}', now()) <= time_period and planid='$planid' and members='$updated_members4'", "time_period desc", "1");
									if($challengeResult4['num_rows'] >= 1)
									{
										$challengeRow4 = $challengeResult4['result'][0];
										$challengeid = $challengeRow4['challengeid'];
										$time_period = $challengeRow4['time_period'];
										$members4 = $challengeRow4['members'];
										$reward = $challengeRow4['reward'];
										$description = "Reward for completing challenge of {$members4} members in {$time_period} days";
										$status = "achieved";
										$refno4 = substr(md5(rand(1, 99999)),0,22);
										$fields3 = array('userid'=>$userid, 'regid'=>$regid4, 'challengeid'=>$challengeid, 'membership_id'=>$membership_id4, 'username'=>$username4, 'refno'=>$refno4, 'time_period'=>$time_period, 'members'=>$members4, 'reward'=>$reward, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
										$challengeHistoryResult = $db->insert("mlm_challenges_history", $fields3);
									}
									$membership_id_history4 = $memberCountRow4['membership_id'];
									$sponsor_id_history4 = $memberCountRow4['sponsor_id'];
									$fields4 = array('userid'=>$userid, 'regid'=>$regid4, 'membership_id'=>$membership_id_history4, 'sponsor_id'=>$sponsor_id_history4, 'member'=>'1', 'total_members'=>$updated_members4, 'description'=>'', 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
									$registrationHistoryResult4 = $db->insert("mlm_registrations_history", $fields4);
									if(!$registrationHistoryResult4)
									{
										echo "Members History is not updated! Consult Administrator";
										exit();
									}
									$countUpdateResult4 = $db->custom("update mlm_registrations set members = members+1 {$query4} where membership_id='{$sponsor_id4}'");
									if(!$countUpdateResult4)
									{
										echo "Member Count is not updated! Consult Administrator";
										exit();
									}
									// end of loop
									
									$sponsor_id5 = $memberCountRow4['sponsor_id'];
									$memberCountResult5 = $db->view('membership_id,sponsor_id,members,status,regid,createdate,createtime,username', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id5'", 'regid asc', '3');
									if($memberCountResult5['num_rows'] >= 1)
									{
										foreach($memberCountResult5['result'] as $memberCountRow5)
										{
											$updated_members5 = $memberCountRow5['members']+1;
											$createtime_member5 = $memberCountRow5['createtime'];
											$createdate_member5 = $memberCountRow5['createdate'];
											$regid5 = $memberCountRow5['regid'];
											$membership_id5 = $memberCountRow5['membership_id'];
											$username5 = $memberCountRow5['username'];
											$memberRewardResult5 = $db->view('members,rewardid,amount', 'mlm_rewards', 'rewardid', "and planid='$planid' and members='$updated_members5'", 'rewardid asc', '1');
											if($memberRewardResult5['num_rows'] >= 1)
											{
												$memberRewardRow5 = $memberRewardResult5['result'][0];
												$rewardid5 = $memberRewardRow5['rewardid'];
												$amount5 = $memberRewardRow5['amount'];
												$members5 = $memberRewardRow5['members'];
												$query5 = ", rewardid='{$rewardid5}'";
												
												$refno5 = substr(md5(rand(1, 99999)),0,22);
												$reason = "Reward";
												$description = "Reward for completing {$members5} members";
												$status = "pending";
												$fields = array('userid'=>$userid, 'regid'=>$regid5, 'membership_id'=>$membership_id5, 'username'=>$username5, 'refno'=>$refno5, 'amount'=>$amount5, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
												$transactionResult = $db->insert("mlm_transactions", $fields);
												
												$fields2 = array('userid'=>$userid, 'regid'=>$regid5, 'membership_id'=>$membership_id5, 'username'=>$username5, 'refno'=>$refno5, 'amount'=>$amount5, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
												$ewalletResult = $db->insert("mlm_ewallet", $fields2);
											}
											$challengeResult5 = $db->view('challengeid,time_period,members,reward', 'mlm_challenges', 'challengeid', "and timestampdiff(DAY, '{$createdate_member5} {$createtime_member5}', now()) <= time_period and planid='$planid' and members='$updated_members5'", "time_period desc", "1");
											if($challengeResult5['num_rows'] >= 1)
											{
												$challengeRow5 = $challengeResult5['result'][0];
												$challengeid = $challengeRow5['challengeid'];
												$time_period = $challengeRow5['time_period'];
												$members5 = $challengeRow5['members'];
												$reward = $challengeRow5['reward'];
												$description = "Reward for completing challenge of {$members5} members in {$time_period} days";
												$status = "achieved";
												$refno5 = substr(md5(rand(1, 99999)),0,22);
												$fields3 = array('userid'=>$userid, 'regid'=>$regid5, 'challengeid'=>$challengeid, 'membership_id'=>$membership_id5, 'username'=>$username5, 'refno'=>$refno5, 'time_period'=>$time_period, 'members'=>$members5, 'reward'=>$reward, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
												$challengeHistoryResult = $db->insert("mlm_challenges_history", $fields3);
											}
											$membership_id_history5 = $memberCountRow5['membership_id'];
											$sponsor_id_history5 = $memberCountRow5['sponsor_id'];
											$fields4 = array('userid'=>$userid, 'regid'=>$regid5, 'membership_id'=>$membership_id_history5, 'sponsor_id'=>$sponsor_id_history5, 'member'=>'1', 'total_members'=>$updated_members5, 'description'=>'', 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
											$registrationHistoryResult5 = $db->insert("mlm_registrations_history", $fields4);
											if(!$registrationHistoryResult5)
											{
												echo "Members History is not updated! Consult Administrator";
												exit();
											}
											$countUpdateResult5 = $db->custom("update mlm_registrations set members = members+1 {$query5} where membership_id='{$sponsor_id5}'");
											if(!$countUpdateResult5)
											{
												echo "Member Count is not updated! Consult Administrator";
												exit();
											}
											// end of loop
											
											$sponsor_id6 = $memberCountRow5['sponsor_id'];
											$memberCountResult6 = $db->view('membership_id,sponsor_id,members,status,regid,createdate,createtime,username', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id6'", 'regid asc', '3');
											if($memberCountResult6['num_rows'] >= 1)
											{
												foreach($memberCountResult6['result'] as $memberCountRow6)
												{
													$updated_members6 = $memberCountRow6['members']+1;
													$createtime_member6 = $memberCountRow6['createtime'];
													$createdate_member6 = $memberCountRow6['createdate'];
													$regid6 = $memberCountRow6['regid'];
													$membership_id6 = $memberCountRow6['membership_id'];
													$username6 = $memberCountRow6['username'];
													$memberRewardResult6 = $db->view('members,rewardid,amount', 'mlm_rewards', 'rewardid', "and planid='$planid' and members='$updated_members6'", 'rewardid asc', '1');
													if($memberRewardResult6['num_rows'] >= 1)
													{
														$memberRewardRow6 = $memberRewardResult6['result'][0];
														$rewardid6 = $memberRewardRow6['rewardid'];
														$amount6 = $memberRewardRow6['amount'];
														$members6 = $memberRewardRow6['members'];
														$query6 = ", rewardid='{$rewardid6}'";
														
														$refno6 = substr(md5(rand(1, 99999)),0,22);
														$reason = "Reward";
														$description = "Reward for completing {$members6} members";
														$status = "pending";
														$fields = array('userid'=>$userid, 'regid'=>$regid6, 'membership_id'=>$membership_id6, 'username'=>$username6, 'refno'=>$refno6, 'amount'=>$amount6, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
														$transactionResult = $db->insert("mlm_transactions", $fields);
														
														$fields2 = array('userid'=>$userid, 'regid'=>$regid6, 'membership_id'=>$membership_id6, 'username'=>$username6, 'refno'=>$refno6, 'amount'=>$amount6, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
														$ewalletResult = $db->insert("mlm_ewallet", $fields2);
													}
													$challengeResult6 = $db->view('challengeid,time_period,members,reward', 'mlm_challenges', 'challengeid', "and timestampdiff(DAY, '{$createdate_member6} {$createtime_member6}', now()) <= time_period and planid='$planid' and members='$updated_members6'", "time_period desc", "1");
													if($challengeResult6['num_rows'] >= 1)
													{
														$challengeRow6 = $challengeResult6['result'][0];
														$challengeid = $challengeRow6['challengeid'];
														$time_period = $challengeRow6['time_period'];
														$members6 = $challengeRow6['members'];
														$reward = $challengeRow6['reward'];
														$description = "Reward for completing challenge of {$members6} members in {$time_period} days";
														$status = "achieved";
														$refno6 = substr(md5(rand(1, 99999)),0,22);
														$fields3 = array('userid'=>$userid, 'regid'=>$regid6, 'challengeid'=>$challengeid, 'membership_id'=>$membership_id6, 'username'=>$username6, 'refno'=>$refno6, 'time_period'=>$time_period, 'members'=>$members6, 'reward'=>$reward, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
														$challengeHistoryResult = $db->insert("mlm_challenges_history", $fields3);
													}
													$membership_id_history6 = $memberCountRow6['membership_id'];
													$sponsor_id_history6 = $memberCountRow6['sponsor_id'];
													$fields4 = array('userid'=>$userid, 'regid'=>$regid6, 'membership_id'=>$membership_id_history6, 'sponsor_id'=>$sponsor_id_history6, 'member'=>'1', 'total_members'=>$updated_members6, 'description'=>'', 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
													$registrationHistoryResult6 = $db->insert("mlm_registrations_history", $fields4);
													if(!$registrationHistoryResult6)
													{
														echo "Members History is not updated! Consult Administrator";
														exit();
													}
													$countUpdateResult6 = $db->custom("update mlm_registrations set members = members+1 {$query6} where membership_id='{$sponsor_id6}'");
													if(!$countUpdateResult6)
													{
														echo "Member Count is not updated! Consult Administrator";
														exit();
													}
													// end of loop
													
													$sponsor_id7 = $memberCountRow6['sponsor_id'];
													$memberCountResult7 = $db->view('membership_id,sponsor_id,members,status,regid,createdate,createtime,username', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id7'", 'regid asc', '3');
													if($memberCountResult7['num_rows'] >= 1)
													{
														foreach($memberCountResult7['result'] as $memberCountRow7)
														{
															$updated_members7 = $memberCountRow7['members']+1;
															$createtime_member7 = $memberCountRow7['createtime'];
															$createdate_member7 = $memberCountRow7['createdate'];
															$regid7 = $memberCountRow7['regid'];
															$membership_id7 = $memberCountRow7['membership_id'];
															$username7 = $memberCountRow7['username'];
															$memberRewardResult7 = $db->view('members,rewardid,amount', 'mlm_rewards', 'rewardid', "and planid='$planid' and members='$updated_members7'", 'rewardid asc', '1');
															if($memberRewardResult7['num_rows'] >= 1)
															{
																$memberRewardRow7 = $memberRewardResult7['result'][0];
																$rewardid7 = $memberRewardRow7['rewardid'];
																$amount7 = $memberRewardRow7['amount'];
																$members7 = $memberRewardRow7['members'];
																$query7 = ", rewardid='{$rewardid7}'";
																
																$refno7 = substr(md5(rand(1, 99999)),0,22);
																$reason = "Reward";
																$description = "Reward for completing {$members7} members";
																$status = "pending";
																$fields = array('userid'=>$userid, 'regid'=>$regid7, 'membership_id'=>$membership_id7, 'username'=>$username7, 'refno'=>$refno7, 'amount'=>$amount7, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
																$transactionResult = $db->insert("mlm_transactions", $fields);
																
																$fields2 = array('userid'=>$userid, 'regid'=>$regid7, 'membership_id'=>$membership_id7, 'username'=>$username7, 'refno'=>$refno7, 'amount'=>$amount7, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
																$ewalletResult = $db->insert("mlm_ewallet", $fields2);
															}
															$challengeResult7 = $db->view('challengeid,time_period,members,reward', 'mlm_challenges', 'challengeid', "and timestampdiff(DAY, '{$createdate_member7} {$createtime_member7}', now()) <= time_period and planid='$planid' and members='$updated_members7'", "time_period desc", "1");
															if($challengeResult7['num_rows'] >= 1)
															{
																$challengeRow7 = $challengeResult7['result'][0];
																$challengeid = $challengeRow7['challengeid'];
																$time_period = $challengeRow7['time_period'];
																$members7 = $challengeRow7['members'];
																$reward = $challengeRow7['reward'];
																$description = "Reward for completing challenge of {$members7} members in {$time_period} days";
																$status = "achieved";
																$refno7 = substr(md5(rand(1, 99999)),0,22);
																$fields3 = array('userid'=>$userid, 'regid'=>$regid7, 'challengeid'=>$challengeid, 'membership_id'=>$membership_id7, 'username'=>$username7, 'refno'=>$refno7, 'time_period'=>$time_period, 'members'=>$members7, 'reward'=>$reward, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
																$challengeHistoryResult = $db->insert("mlm_challenges_history", $fields3);
															}
															$membership_id_history7 = $memberCountRow7['membership_id'];
															$sponsor_id_history7 = $memberCountRow7['sponsor_id'];
															$fields4 = array('userid'=>$userid, 'regid'=>$regid7, 'membership_id'=>$membership_id_history7, 'sponsor_id'=>$sponsor_id_history7, 'member'=>'1', 'total_members'=>$updated_members7, 'description'=>'', 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
															$registrationHistoryResult7 = $db->insert("mlm_registrations_history", $fields4);
															if(!$registrationHistoryResult7)
															{
																echo "Members History is not updated! Consult Administrator";
																exit();
															}
															$countUpdateResult7 = $db->custom("update mlm_registrations set members = members+1 {$query7} where membership_id='{$sponsor_id7}'");
															if(!$countUpdateResult7)
															{
																echo "Member Count is not updated! Consult Administrator";
																exit();
															}
															// end of loop
															
															$sponsor_id8 = $memberCountRow7['sponsor_id'];
															$memberCountResult8 = $db->view('membership_id,sponsor_id,members,status,regid,createdate,createtime,username', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id8'", 'regid asc', '3');
															if($memberCountResult8['num_rows'] >= 1)
															{
																foreach($memberCountResult8['result'] as $memberCountRow8)
																{
																	$updated_members8 = $memberCountRow8['members']+1;
																	$createtime_member8 = $memberCountRow8['createtime'];
																	$createdate_member8 = $memberCountRow8['createdate'];
																	$regid8 = $memberCountRow8['regid'];
																	$membership_id8 = $memberCountRow8['membership_id'];
																	$username8 = $memberCountRow8['username'];
																	$memberRewardResult8 = $db->view('members,rewardid,amount', 'mlm_rewards', 'rewardid', "and planid='$planid' and members='$updated_members8'", 'rewardid asc', '1');
																	if($memberRewardResult8['num_rows'] >= 1)
																	{
																		$memberRewardRow8 = $memberRewardResult8['result'][0];
																		$rewardid8 = $memberRewardRow8['rewardid'];
																		$amount8 = $memberRewardRow8['amount'];
																		$members8 = $memberRewardRow8['members'];
																		$query8 = ", rewardid='{$rewardid8}'";
																		
																		$refno8 = substr(md5(rand(1, 99999)),0,22);
																		$reason = "Reward";
																		$description = "Reward for completing {$members8} members";
																		$status = "pending";
																		$fields = array('userid'=>$userid, 'regid'=>$regid8, 'membership_id'=>$membership_id8, 'username'=>$username8, 'refno'=>$refno8, 'amount'=>$amount8, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
																		$transactionResult = $db->insert("mlm_transactions", $fields);
																		
																		$fields2 = array('userid'=>$userid, 'regid'=>$regid8, 'membership_id'=>$membership_id8, 'username'=>$username8, 'refno'=>$refno8, 'amount'=>$amount8, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
																		$ewalletResult = $db->insert("mlm_ewallet", $fields2);
																	}
																	$challengeResult8 = $db->view('challengeid,time_period,members,reward', 'mlm_challenges', 'challengeid', "and timestampdiff(DAY, '{$createdate_member8} {$createtime_member8}', now()) <= time_period and planid='$planid' and members='$updated_members8'", "time_period desc", "1");
																	if($challengeResult8['num_rows'] >= 1)
																	{
																		$challengeRow8 = $challengeResult8['result'][0];
																		$challengeid = $challengeRow8['challengeid'];
																		$time_period = $challengeRow8['time_period'];
																		$members8 = $challengeRow8['members'];
																		$reward = $challengeRow8['reward'];
																		$description = "Reward for completing challenge of {$members8} members in {$time_period} days";
																		$status = "achieved";
																		$refno8 = substr(md5(rand(1, 99999)),0,22);
																		$fields3 = array('userid'=>$userid, 'regid'=>$regid8, 'challengeid'=>$challengeid, 'membership_id'=>$membership_id8, 'username'=>$username8, 'refno'=>$refno8, 'time_period'=>$time_period, 'members'=>$members8, 'reward'=>$reward, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
																		$challengeHistoryResult = $db->insert("mlm_challenges_history", $fields3);
																	}
																	$membership_id_history8 = $memberCountRow8['membership_id'];
																	$sponsor_id_history8 = $memberCountRow8['sponsor_id'];
																	$fields4 = array('userid'=>$userid, 'regid'=>$regid8, 'membership_id'=>$membership_id_history8, 'sponsor_id'=>$sponsor_id_history8, 'member'=>'1', 'total_members'=>$updated_members8, 'description'=>'', 'status'=>'active', 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
																	$registrationHistoryResult8 = $db->insert("mlm_registrations_history", $fields4);
																	if(!$registrationHistoryResult8)
																	{
																		echo "Members History is not updated! Consult Administrator";
																		exit();
																	}
																	$countUpdateResult8 = $db->custom("update mlm_registrations set members = members+1 {$query8} where membership_id='{$sponsor_id8}'");
																	if(!$countUpdateResult8)
																	{
																		echo "Member Count is not updated! Consult Administrator";
																		exit();
																	}
																	// end of loop
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

header("Location: register_view.php$search_filter");
exit();
?>