<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "ewallet_request";

$total_balance = $validation->input_validate($_POST['total_balance']);
$redeemable_amount = $validation->input_validate($_POST['redeemable_amount']);
$membership_id = $validation->input_validate($_POST['membership_id']);
$mobile = $validation->input_validate($_POST['mobile']);
$bank_name = $validation->input_validate($_POST['bank_name']);
$account_number = $validation->input_validate($_POST['account_number']);
$ifsc_code = $validation->input_validate($_POST['ifsc_code']);
$account_name = $validation->input_validate($_POST['account_name']);
$amount = $validation->input_validate($_POST['amount']);
if($amount=='')
{
	$amount = 0;
}
$balance = $validation->input_validate($_POST['balance']);
if($balance=='')
{
	$balance = 0;
}
$balance = $balance - $amount;
$remarks = $validation->input_validate($_POST['remarks']);
$refno = substr(md5(rand(1, 99999)),0,22);
$status = 'pending';


if($amount < $configRow['min_wallet_amount'])
{
	$_SESSION['error_msg'] = "Minimum Transaction should be {$configRow['min_wallet_amount']}";
	header("Location: ewallet_request_form.php");
	exit();
}



if($amount > $redeemable_amount)
{
	$_SESSION['error_msg'] = "Maximum Transaction should be under {$redeemable_amount}";
	header("Location: ewallet_request_form.php");
	exit();
}

if($amount % 300 != 0 )
{
	$_SESSION['error_msg'] = "You can only redeem amount which are multiple of 300.";
	header("Location: ewallet_request_form.php");
	exit();
}

$fields = array('regid'=>$regid, 'membership_id'=>$membership_id, 'refno'=>$refno, 'mobile'=>$mobile, 'bank_name'=>$bank_name, 'account_number'=>$account_number, 'ifsc_code'=>$ifsc_code, 'account_name'=>$account_name, 'amount'=>$amount, 'balance'=>$balance, 'remarks'=>$remarks, 'status'=>$status, 'user_ip'=>$user_ip);
$fields['createtime'] = $createtime;
$fields['createdate'] = $createdate;

$ewalletrequestResult = $db->insert("mlm_ewallet_requests", $fields);
if(!$ewalletrequestResult)
{
	echo mysqli_error($connect);
	exit();
}

$registerwalletResult = $db->custom("update mlm_registrations set wallet_money = wallet_money-{$amount} where regid='{$regid}'");
if(!$registerwalletResult)
{
	echo "Member Wallet is not added! Consult Administrator";
	exit();
}

$_SESSION['success_msg'] = "Money Requested Successfully!";
header("Location: ewallet_request_view.php");
exit();
?>