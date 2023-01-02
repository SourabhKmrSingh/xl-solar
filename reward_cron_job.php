<?php
include_once("inc_config.php");

$rewardQuery = $db->view('*',"mlm_reward_details",'rewardid'," and reward_end_date >= '{$createdate}'");

if($rewardQuery['num_rows'] >= 1){
    foreach($rewardQuery['result'] as $rewardRow){
        $regid = $rewardRow['regid'];
        $reward_id = $rewardRow['reward_id'];
        $refno = substr(md5(rand(1, 99999)),0,10);

        $rewardDetailsQuery = $db->view('*','mlm_rewards','rewardid'," and rewardid={$reward_id}");
        $rewardDetailsRow = $rewardDetailsQuery['result'][0];

        $memberQuery =  $db->view('*','mlm_registrations','regid'," and regid={$regid}");
        $memberRow = $memberQuery['result'][0];

        

        $title = $rewardDetailsRow['title'];
        $membership_id = $memberRow['membership_id'];
        $username = $memberRow['membership_id'];
        $amount = $rewardDetailsRow['amount'];
        $reason = "Reward";
        $description = "Reward for Reaching {$title}";
        $status = 'fullfilled';

        $fields = array('regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'refno'=>$refno, 'amount'=>$amount, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'createtime'=>$createtime, 'createdate'=>$createdate);
        $transactionResult = $db->insert("mlm_transactions", $fields);
        if(!$transactionResult)
        {
            echo "Transaction History is not updated! Consult Administrator";
           
        }
        
        $fields2 = array('regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'refno'=>$refno, 'amount'=>$amount, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'createtime'=>$createtime, 'createdate'=>$createdate);
        $ewalletResult = $db->insert("mlm_ewallet", $fields2);
        if(!$ewalletResult)
        {
            echo "E-Wallet History is not updated! Consult Administrator";
        }

        $registerwalletResult = $db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount}, wallet_total = wallet_total+{$amount} where regid='{$regid}'");
        if(!$registerwalletResult)
        {
            echo "Member Wallet is not added! Consult Administrator";
        }
    }
}
?>