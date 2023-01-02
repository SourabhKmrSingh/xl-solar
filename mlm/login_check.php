<?php
include_once("inc_config.php");

$membership_id = $validation->input_validate($_POST['membership_id']);
$password = $validation->input_validate(sha1($_POST['password']));

$userQueryResult = $db->view('*', 'mlm_registrations', 'regid', "and membership_id = '$membership_id' and password = '$password'");
if(!$userQueryResult)
{
	echo mysqli_error($connect);
	exit();
}
if($userQueryResult['num_rows'] >= 1)
{
	$usersRow = $userQueryResult['result'][0];
	
	$_SESSION['mlm_regid'] = $usersRow['regid'];
	$_SESSION['mlm_username'] = $usersRow['username'];
	$_SESSION['mlm_membership_id'] = $usersRow['membership_id'];
	$_SESSION['mlm_status'] = $usersRow['status'];
	$_SESSION['mlm_first_name'] = $usersRow['first_name'];
	$_SESSION['mlm_last_name'] = $usersRow['last_name'];
	$_SESSION['mlm_email'] = $usersRow['email'];
	$_SESSION['mlm_mobile'] = $usersRow['mobile'];
	$_SESSION['mlm_imgName'] = $usersRow['imgName'];
	$_SESSION['mlm_account_number'] = $usersRow['account_number'];
	$_SESSION['mlm_document'] = $usersRow['document'];
	$_SESSION['pan_card_number'] = $usersRow['pan_card_number'];
	
	$regid = $usersRow['regid'];
	$username = $usersRow['username'];
	$status = $usersRow['status'];
	
	$fields = array('regid'=>$regid, 'username'=>$username, 'membership_id'=>$membership_id, 'status'=>$status, 'user_ip'=>$user_ip, 'createtime'=>$createtime, 'createdate'=>$createdate);
	$logQueryResult = $db->insert("mlm_logdetail_frontend", $fields);
	if(!$logQueryResult)
	{
		$_SESSION['error_msg'] = "Error while creating your log. Please try again!";
		header("Location: index.php");
		exit();
	}
	
	// if($_SESSION['mlm_status'] == 'inactive')
	// {
	// 	$_SESSION['error_msg'] = "Your Account is still in under verification. Please wait, we'll notify you!";
	// 	$_SESSION['mlm_regid'] = "";
	// 	$_SESSION['mlm_username'] = "";
	// 	$_SESSION['mlm_status'] = "";
		
	// 	header("Location: index.php");
	// 	exit();
	// }
	
	// $_SESSION['success_msg'] = "You're Logged In!";
	// if($_SESSION['redirect_url'] != "")
	// {
	// 	header("Location: {$_SESSION['redirect_url']}");
	// 	exit();
	// }
	// else
	// {
		header("Location: home.php");
		exit();
	// }
}
else
{
	$_SESSION['error_msg'] = "Invalid login details!";
	header("Location: index.php");
}
?>