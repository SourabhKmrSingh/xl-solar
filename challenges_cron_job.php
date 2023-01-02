<?php
include_once("inc_config.php");

$memberQuery = $db->view('*',"mlm_registrations",'regid'," and status='active' and challenge_id > '0'");

if($memberQuery['num_rows'] >= 1){

    $startdate = date('Y-m-d', strtotime($createdate . " - 30 days"));
    $purchaseQuery = $db->view('*',"mlm_transactions",'transactionid'," and type='credit' and pay_type='0' and createdate between '{$startdate}' and '{$createdate}'");
    
    $turnover = 0;
    foreach($purchaseQuery['result'] as $purchaseRow){
        $turnover += $purchaseRow['amount'];
    }
    $total_members = $memberQuery['num_rows'];

    foreach($memberQuery['result'] as $memberRow){
        $challengeid = $memberRow['challenge_id'];
        $challengeDetails = $db->view('*','mlm_challenges','challengeid'," and challengeid='{$challengeid}'");
        $challengeRow = $challengeDetails['result'][0];
        $percentage = $challengeRow['reward'];
        
        $amount = ($turnover * $percentage) / (100 * $total_members);

        $regid = $memberRow['regid'];
        $refno = substr(md5(rand(1, 99999)),0,10);

        $members = $challengeRow['members'];
        $time_period = $challengeRow['time_period'];
        $membership_id = $memberRow['membership_id'];
        $username = $memberRow['username'];
        $reason = "Challenge";
        $description = "For adding {$members} members in {$time_period} days";
        $status = 'fullfilled';

        $fields = array('regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'refno'=>$refno, 'amount'=>$amount, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
        $transactionResult = $db->insert("mlm_transactions", $fields);
        if(!$transactionResult)
        {
            echo "Transaction History is not updated! Consult Administrator";
            exit();
        }
        
        $fields2 = array('regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'refno'=>$refno, 'amount'=>$amount, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
        $ewalletResult = $db->insert("mlm_ewallet", $fields2);
        if(!$ewalletResult)
        {
            echo "E-Wallet History is not updated! Consult Administrator";
            exit();
        }

        $registerwalletResult = $db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount}, wallet_total = wallet_total+{$amount},challenge_id = 0 where regid='{$regid}'");
        if(!$registerwalletResult)
        {
            echo "Member Wallet is not added! Consult Administrator";
            exit();
        }
    }
}
?>