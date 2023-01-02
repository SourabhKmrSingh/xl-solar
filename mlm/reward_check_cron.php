<?php 
include_once("inc_config.php");


function getAllDownlines($parent)
{
    global $db, $createdate, $createtime;
    $treeResult = $db->view('*', 'mlm_registrations', 'regid', " and sponsor_id='$parent'", 'regid asc');

	if($treeResult['num_rows'] >= 1)
	{
		foreach($treeResult['result'] as $memberRow)
		{
            if($memberRow['status'] == 'active'){
                $rewardResult = $db->view('*', 'mlm_rewards', 'rewardid', "and status='active' and rewardid > '{$memberRow['rewardid']}'",'','1');
				
				if ($rewardResult['num_rows'] >= 1) {
					$rewardRow = $rewardResult['result'][0];
					$members = $rewardRow['members'];
					$amount = $rewardRow['amount'];
					$rewardid = $rewardRow['rewardid'];
					$rewardTitle = $rewardRow['title'];
					$rewardTime = $rewardRow['time_period'];
					$direct_members = $rewardRow['direct_members'];
					$registerDate = $memberRow['createdate'];
					$registertime = $memberRow['createtime'];

					$singleLegTeamQuery =  $db->view('*','mlm_registrations','regid'," and concat(createdate,' ',createtime) > '{$registerDate} {$registertime}' and status='active'");

					$singleLegTeam = $singleLegTeamQuery['num_rows'];
					$membership_id =$memberRow['membership_id'];

					$direct_members_user = $db->view('*','mlm_registrations','regid'," and sponsor_id ='{$membership_id}' and status='active'");
					if ($amount != "") {
						if($singleLegTeam >= $members && $direct_members_user['num_rows'] >= $direct_members){
							$refno = substr(md5(rand(1, 99999)), 0, 10);
							$reward = "{$amount} Rs. For {$rewardTime} days";
							$description = "Reward for reaching {$rewardTitle}";
							$reward_end_date = date('Y-m-d', strtotime($createdate . " + {$rewardTime} days"));

							$fields4 = array('regid' => $memberRow['regid'],'rewardid'=>$rewardid ,'membership_id' => $memberRow['membership_id'], 'username' => $memberRow['username'], 'refno' => $refno, 'members' => $members,'reward'=>$reward, 'description' => $description, 'createtime' => $createtime, 'createdate' => $createdate);
							$rewardHistory = $db->insert("mlm_rewards_history", $fields4);

							$fields_rewards = array('regid'=>$memberRow['regid'], 'membershipId'=>$memberRow['membership_id'], 'reward_id'=>$rewardid, 'reward_amount'=>$amount, 'reward_end_date'=>$reward_end_date);

							$fields_rewards['createdate'] = $createdate;
							$fields_rewards['createtime'] = $createtime;

							$rewardQueryResult = $db->insert('mlm_reward_details', $fields_rewards);

							$registerRewardResult = $db->custom("update mlm_registrations set rewardid = {$rewardid}, direct_member = '0', current_member = '0' where regid ='{$memberRow['regid']}'");
							if (!$registerRewardResult) {
								echo "Member Wallet is not added! Consult Administrator";
								exit();
							}
						}
					}
				}
            }
			getAllDownlines($memberRow['membership_id']);
		}
	}
	return;
}

$membership_id = 'SL001';

$firstMemberResult = $db->view('*','mlm_registrations','regid'," and membership_id ='{$membership_id}'");
$firstMemberRow = $firstMemberResult['result'][0];

if($firstMemberRow['status'] == 'active'){
	$rewardResult = $db->view('*', 'mlm_rewards', 'rewardid', "and status='active' and rewardid > '{$firstMemberRow['rewardid']}'",'','1');
	
	if ($rewardResult['num_rows'] >= 1) {
		$rewardRow = $rewardResult['result'][0];
		$members = $rewardRow['members'];
		$amount = $rewardRow['amount'];
		$rewardid = $rewardRow['rewardid'];
		$rewardTitle = $rewardRow['title'];
		$rewardTime = $rewardRow['time_period'];
		$direct_members = $rewardRow['direct_members'];
		$registerDate = $firstMemberRow['createdate'];
		$registertime = $firstMemberRow['createtime'];

		$singleLegTeamQuery =  $db->view('*','mlm_registrations','regid'," and concat(createdate,' ',createtime) > '{$registerDate} {$registertime}' and status='active'");

		$singleLegTeam = $singleLegTeamQuery['num_rows'];
		$membership_id1 = $firstMemberRow['membership_id'];

		$direct_members_user = $db->view('*','mlm_registrations','regid'," and sponsor_id ='{$membership_id1}' and status='active'");
		if ($amount != "") {
			if($singleLegTeam >= $members && $direct_members_user['num_rows'] >= $direct_members){
				$refno = substr(md5(rand(1, 99999)), 0, 10);
				$reward = "{$amount} Rs. For {$rewardTime} days";
				$description = "Reward for reaching {$rewardTitle}";
				$reward_end_date = date('Y-m-d', strtotime($createdate . " + {$rewardTime} days"));

				$fields4 = array('regid' => $firstMemberRow['regid'],'rewardid'=>$rewardid ,'membership_id' => $firstMemberRow['membership_id'], 'username' => $firstMemberRow['username'], 'refno' => $refno, 'members' => $members,'reward'=>$reward, 'description' => $description, 'createtime' => $createtime, 'createdate' => $createdate);
				$rewardHistory = $db->insert("mlm_rewards_history", $fields4);

				$fields_rewards = array('regid'=>$firstMemberRow['regid'], 'membershipId'=>$firstMemberRow['membership_id'], 'reward_id'=>$rewardid, 'reward_amount'=>$amount, 'reward_end_date'=>$reward_end_date);

				$fields_rewards['createdate'] = $createdate;
				$fields_rewards['createtime'] = $createtime;

				$rewardQueryResult = $db->insert('mlm_reward_details', $fields_rewards);

				$registerRewardResult = $db->custom("update mlm_registrations set rewardid = {$rewardid}, direct_member = '0', current_member = '0' where regid ='{$firstMemberRow['regid']}'");
				if (!$registerRewardResult) {
					echo "Member Wallet is not added! Consult Administrator";
					exit();
				}
			}
		}
	}
}

getAllDownlines($membership_id);


?>