<?php
include_once("inc_config.php");

@$sponsor_id = $_POST['sponsor_id'];

if(isset($sponsor_id) and $sponsor_id != "")
{
	$registerResult = $db->view('regid,first_name,last_name', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id'", 'regid desc');
	if($registerResult['num_rows'] >= 1)
	{
		$registerRow = $registerResult['result'][0];
		echo $validation->db_field_validate($registerRow['first_name']." ".$registerRow['last_name']);
	}
	else
	{
		echo 'no';
	}
}
else
{
	echo '';
}
?>