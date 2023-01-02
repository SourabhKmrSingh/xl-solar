<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "ewallet_request";
echo $validation->section($_SESSION['per_ewallet']);

$requestid = $validation->input_validate($_POST['requestid']);
$remarks = $validation->input_validate($_POST['remarks']);
$status = 'declined';

$requestResult = $db->view('*', 'mlm_ewallet_requests', 'requestid', "and requestid = '$requestid'");
$requestRow = $requestResult['result'][0];
$amount = $requestRow['amount'];
$balance = $requestRow['balance'];
$regid = $requestRow['regid'];

$fields = array('remarks'=>$remarks, 'status'=>$status, 'user_ip'=>$user_ip);
$fields['createtime'] = $createtime;
$fields['createdate'] = $createdate;

$ewalletrequestResult = $db->update("mlm_ewallet_requests", $fields, array('requestid'=>$requestid));
if(!$ewalletrequestResult)
{
	echo mysqli_error($connect);
	exit();
}

$registerwalletResult = $db->custom("update mlm_registrations set wallet_money = wallet_money+{$amount} where regid='{$regid}'");
if(!$registerwalletResult)
{
	echo "Member Wallet is not added! Consult Administrator";
	exit();
}

$_SESSION['success_msg'] = "You've declined the request Successfully!";
header("Location: ewallet_request_view.php");
exit();
?>