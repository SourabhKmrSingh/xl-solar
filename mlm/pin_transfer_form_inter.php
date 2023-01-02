<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "pinTransfer";


$membership_id = $validation->input_validate($_POST['membership_id']);
$mobile = $validation->input_validate($_POST['mobile']);
$pins = $_POST['pinid'];

if($membership_id =="" || $mobile == ""){
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: active_account_view.php");
	exit();
}
$regidQuery = $db->view('*','mlm_registrations','regid'," and membership_id='{$membership_id}'");
$regid = $regidQuery['result'][0]['regid'];

foreach($pins as $pin){
	$memberQuery = $db->custom("UPDATE `mlm_activate_pins` SET `membership_id`='{$membership_id}', regid='{$regid}' WHERE pinid ='{$pin}'");
}
if(!$memberQuery){
	$_SESSION['error_msg'] = "There is some problem Pin Transfer! Please try again later.";
	header("Location: active_account_view.php");
	exit();
}


	
$_SESSION['success_msg'] = "Pin Transfer Succesfully!";
header("Location: active_account_view.php");
exit();


?>