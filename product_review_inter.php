<?php
include_once("inc_config.php");

$productid = $validation->input_validate($_POST['productid']);
$name = $validation->input_validate($_POST['name']);
$email = $validation->input_validate($_POST['email']);
$ratings = $validation->input_validate($_POST['ratings']);
$message = $validation->input_validate($_POST['message']);
$redirect_url = $validation->input_validate($_POST['redirect_url']);
$status = "inactive";

if($name == "" || $email == "" || $ratings == "")
{
	$_SESSION['error_msg_fe'] = "Please select valid parameters to continue!";
	header("Location: {$redirect_url}");
	exit();
}

$fields = array('regid'=>$regid, 'productid'=>$productid, 'name'=>$name, 'email'=>$email, 'ratings'=>$ratings, 'message'=>$message, 'status'=>$status, 'user_ip'=>$user_ip);
$fields['createtime'] = $createtime;
$fields['createdate'] = $createdate;

$reviewResult = $db->insert("rb_products_reviews", $fields);
if(!$reviewResult)
{
	echo mysqli_error($connect);
	exit();
}

$_SESSION['success_msg_fe'] = "Your review is being processed. You may check it after some time.";
header("Location: {$base_url}thank-you{$suffix}");
exit();
?>