<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "transfer";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: wallet_transfer_view.php");
	exit();
}


$memberAccount = $validation->input_validate($_POST['memberAccount']);
$membership_id = $validation->input_validate($_POST['membership_id']);
$username = $validation->input_validate($_POST['username']);
$mobile = $validation->input_validate($_POST['mobile']);
$amount = $validation->input_validate($_POST['amount']);

if($memberAccount == $membership_id){
	$_SESSION['error_msg'] = "Error! Cann't Send Money to Yourself.";
	header("Location: wallet_transfer_view.php");
	exit();
}

if($memberAccount == "" || $membership_id == "" || $username == "" || $amount == ""){
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: wallet_transfer_view.php");
	exit();
}


$curMemberResult = $db->view("*", "mlm_registrations", "regid", " and status = 'active' and membership_id = '$membership_id'");
$curMemberRow = $curMemberResult['result'][0];

$regid = $curMemberRow['regid'];
$refno = substr(md5(rand(1, 99999)),0,10); 
$reason = "MONEY TRANSFER";
$description = "Money Transfer form {$memberAccount}";
$status = "fullfilled";

$fields = array('regid' => $curMemberRow['regid'], "level" => $repurchaseRow['repurchaseid'],'membership_id' => $curMemberRow['membership_id'], 'username' => $curMemberRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'debit', 'reason' => $reason, 'description' => $description, 'status' => $status,  'createtime' => $createtime, 'createdate' => $createdate);


$db->insert("mlm_transactions", $fields);

$fields2 = array('regid' => $curMemberRow['regid'], "level" => $levelRow['levelid'], 'membership_id' => $curMemberRow['membership_id'], 'username' => $curMemberRow['username'], 'refno' => $refno, 'amount' => $amount, 'type' => 'credit', 'reason' => $reason, 'description' => $description, 'status' => $status, 'createtime' => $createtime, 'createdate' => $createdate);

$db->insert("mlm_ewallet", $fields2);

$db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount}, wallet_total = wallet_total+{$amount} where regid='{$regid}'");

$db->custom("update mlm_registrations set wallet_money = wallet_money-{$amount} where membership_id = '{$memberAccount}'");

$transferFields = array('ownMemberId' => $memberAccount, 'transferMemberId' => $membership_id, 'refno' => $refno, 'amount'=> $amount, 'user_ip' => $user_ip, 'createtime' => $createtime, 'createdate' => $createdate);

$db->insert("mlm_wallet_transfer", $transferFields);

	
$_SESSION['success_msg'] = "Amount Transfer Successfully!";
header("Location: wallet_transfer_view.php");
exit();


?>