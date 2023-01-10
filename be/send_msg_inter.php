<?php
include_once('inc_config.php');
include_once("login_user_check.php");

if(count($_POST['members']) >= 1){
    $members = $_POST['members'];
    $remarks = $_POST['remarks'];

    foreach($members as $member){
        $memberResult = $db->view('*', "rb_registrations", "regid", " and regid = '$member'");
        $memberRow = $memberResult['result'][0];

        if($memberRow['mobile'] != ""){
            $response = WhatsApp::sendMSG("+91{$memberRow['mobile']}", $remarks);
        }
    }

    $_SESSION['success_msg'] = "Message Sent!";
	header("Location: send_msg.php");
	exit();

}else{
    $_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: send_msg.php");
	exit();
}

?>