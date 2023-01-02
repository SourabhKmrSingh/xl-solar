<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "generate_pins";

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


$planid = $validation->input_validate($_POST['planid']);
$username = $validation->input_validate($_POST['username']);
$membership_id = $validation->input_validate($_POST['membership_id']);
$pin_total = $validation->input_validate($_POST['pin_total']);
$free_pins = $validation->input_validate($_POST['free']);
$tax = $validation->input_validate($_POST['tax']);
$tax_amount = $validation->input_validate($_POST['tax_amount']);
$subtotal = $validation->input_validate($_POST['subtotal']);
$total_amount = $validation->input_validate($_POST['total_amount']);
$status ='fulfilled';

if($username =="" || $membership_id == "" || $planid == ""){
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: generate_pins_view.php");
	exit();
}

$memberQuery = $db->view('*','mlm_registrations',"regid"," and membership_id='{$membership_id}'");
$memberRow = $memberQuery['result'][0];
if($memberQuery['num_rows'] < 1){
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: generate_pins_view.php");
	exit();
}
$regid = $memberRow['regid'];

$refno = strtoupper(substr(md5(rand(1, 99999)),0,8));


$fields = array('refno'=>$refno, 'planid'=>$planid,'regid'=>$regid, 'username'=>$username, 'membership_id'=>$membership_id, 'pin_total'=>$pin_total, 'free_pins'=>$free_pins,'tax'=>$tax, 'subtotal'=>$subtotal,'tax_amount'=>$tax_amount, 'total_amount'=>$total_amount, 'status'=>$status);

if($mode == "insert")
{
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$PinsQueryResult = $db->insert("mlm_pins_track", $fields);
	if(!$PinsQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	if($user_id == ""){
		$user_id = 0;
	}

	$reason ="Pin Generate";
	$description = "{$pin_total} Pins are credited to account {$membership_id}";

	$fields = array('userid'=>$userid, 'regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'refno'=>$refno, 'amount'=>$subtotal, 'type'=>'credit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
	$transactionResult = $db->insert("mlm_transactions", $fields);
	if(!$transactionResult)
	{
		echo "Transaction History is not updated! Consult Administrator";
		exit();
	}
	
	$fields2 = array('userid'=>$userid, 'regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'refno'=>$refno, 'amount'=>$subtotal, 'type'=>'debit', 'reason'=>$reason, 'description'=>$description, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
	$ewalletResult = $db->insert("mlm_ewallet", $fields2);
	if(!$ewalletResult)
	{
		echo "E-Wallet History is not updated! Consult Administrator";
		exit();
	}


	// Generate random pins 
	$totalPins = $pin_total + $free_pins;

	for($i=0; $i< $totalPins; $i++){
		$pin = substr(md5(time() . mt_rand(1,100000000000)),0,16);

		$fields = array('regid'=>$regid, 'membership_id'=>$membership_id, 'username'=>$username, 'pin'=>$pin,'status' =>'active','createtime'=>$createtime,'createdate'=>$createdate);

		$activePinResult = $db->insert("mlm_activate_pins", $fields);
		if(!$activePinResult){
			echo "Pins are not generated! Consult Administrator";
			exit();
		}

	}
	

	
	$_SESSION['success_msg'] = "Pins Generated Successfully!";
	header("Location: generate_pins_view.php");
	exit();
}

?>