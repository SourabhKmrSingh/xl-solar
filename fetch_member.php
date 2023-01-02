<?php
include_once("inc_config.php");

@$membership_id = strtolower($_POST['membership_id']);
@$mobile = $_POST['mobile'];

if(isset($membership_id) and $membership_id != "")
{
	$registerResult = $db->view('regid,first_name,last_name,sponsor_id', 'mlm_registrations', 'regid', "and LOWER(membership_id)='$membership_id' and mobile='$mobile'", 'regid desc');
	if($registerResult['num_rows'] >= 1)
	{
		$registerRow = $registerResult['result'][0];
		echo $validation->db_field_validate($registerRow['sponsor_id']);
		exit();
	}
	else
	{
		echo "userno";
	}
	
	$registerResult = $db->view('regid,first_name,last_name,sponsor_id', 'mlm_registrations', 'regid', "and membership_id='$membership_id'", 'regid desc');
	if($registerResult['num_rows'] >= 1)
	{
		$registerRow = $registerResult['result'][0];
		echo $validation->db_field_validate($registerRow['sponsor_id']);
		exit();
	}
	else
	{
		echo "no";
		exit();
	}
}
else
{
	echo '';
}
?>