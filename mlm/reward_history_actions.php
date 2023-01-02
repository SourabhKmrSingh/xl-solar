<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "challengehistory";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "claim")
{
	$historyid = $validation->urlstring_validate($_GET['historyid']);
	
	$challengehistoryResult = $db->update("mlm_challenges_history", array('status'=>'claimed'), array('historyid'=>$historyid));
	if(!$challengehistoryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "{$challengehistoryResult} Record Updated!";
	header("Location: challenge_history_view.php");
	exit();
}
else
{
	$fields = $_POST;
	
	foreach($fields as $key=>$value)
	{
		$fields_string .= $key.'='.$value.'&';
	}
	rtrim($fields_string, '&');
	$fields_string = str_replace("bulk_actions=&", "", $fields_string);
	$fields_string = substr($fields_string, 0, -1);
	
	header("Location: challenge_history_view.php?$fields_string");
	exit();
}
?>