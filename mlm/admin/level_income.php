<?php 
include_once('inc_config.php');

$slr = 1; 
$cMemberid = "XLS47624";
$total_amount = "2000.00";
$purchaseid = 1;
//Level Income 
function getAllDownlines($parent)
{
    
    global $db, $slr, $total_amount ,$cMemberid, $purchaseid, $createdate, $createtime;
    $dataResult = $db->view('*', 'mlm_registrations', 'regid', "and membership_id IN('$parent')", 'regid asc');
    $children = array();
    if($dataResult['num_rows'] >= 1)
    {

        foreach($dataResult['result'] as $memberRow)
        {

			echo "<pre>";
			echo "Level {$slr} ";
			print_r($memberRow['membership_id']);
			echo "<br />";

			$levelResult = $db->view("*", "mlm_levels", "levelid", " and status = 'active' and order_custom = '$slr'");

			if($levelResult['num_rows'] >= 1)
			{
				
				$levelRow = $levelResult['result'][0];
				
				print_r($levelRow);

				$levelPercentage = $levelRow['percentage'];

				if($levelPercentage != "0.00" && $levelPercentage != "" && $total_amount != "0.00" && $total_amount != ""){
					
					$amount = round($total_amount * ($levelPercentage / 100));
					$regid = $memberRow['regid'];
					$refno = substr(md5(rand(1, 99999)),0,10);
					$reason = "LEVEL INCOME";
					$description = "Level {$slr} Income for Purchase By {$cMemberid}";
					$status = "fullfilled";

					$fields = array('regid' => $memberRow['regid'], "level" => $levelRow['levelid'], "purchaseid" => $purchaseid,'membership_id' => $memberRow['membership_id'], 'username' => $memberRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'debit', 'reason' => $reason, 'description' => $description, 'status' => $status,  'createtime' => $createtime, 'createdate' => $createdate);

					print_r($fields);
					exit();

					$db->insert("mlm_transactions", $fields);

					$fields2 = array('regid' => $memberRow['regid'], "level" => $levelRow['levelid'], "purchaseid" => $purchaseid, 'membership_id' => $memberRow['membership_id'], 'username' => $memberRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'credit', 'reason' => $reason, 'description' => $description, 'status' => $status, 'createtime' => $createtime, 'createdate' => $createdate);
					
					$db->insert("mlm_ewallet", $fields2);
					
					$db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount}, wallet_total = wallet_total+{$amount} where regid='{$regid}'");

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

$sponsor_Sponsor_ID ='XLS37101';

$memberCountResult = $db->view('*', 'mlm_registrations', 'regid', " and membership_id='{$sponsor_Sponsor_ID}'");
if($memberCountResult['num_rows'] >= 1)
{   
    $memberRow = $memberCountResult['result'][0];
    getAllDownlines($memberRow['membership_id']);
}
