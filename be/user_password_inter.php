<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "";

$userid = $validation->input_validate($_POST['userid']);
$password = $validation->input_validate(sha1($_POST['password']));
$new_password = $validation->input_validate(sha1($_POST['new_password']));
$confirm_new_password = $validation->input_validate(sha1($_POST['confirm_new_password']));

$checkQueryResult = $db->view('userid', 'rb_users', 'userid', "and password='$password' and userid='$userid'");
if($checkQueryResult['num_rows'] >= 1)
{
	if($_POST['new_password'] != "")
	{
		if($new_password != $confirm_new_password)
		{
			$_SESSION['error_msg'] = "Password and Confirm Password should be Same!";
			header("Location: user_password.php");
			exit();
		}
		
		$passwordQueryResult = $db->update("rb_users", array('password'=>$new_password, 'modifytime'=>$createtime, 'modifydate'=>$createdate), array('userid'=>$userid));
		if(!$passwordQueryResult)
		{
			echo mysqli_error($connect);
			exit();
		}
	}
}
else
{
	$_SESSION['error_msg'] = "Your old password is incorrect. Please re-enter the correct password!";
	header("location: user_password.php");
	exit();
}

$_SESSION['success_msg'] = "Your password has been updated Successfully!";
header("Location: user_password.php");
exit();
?>