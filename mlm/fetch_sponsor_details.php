<?php
include_once("inc_config.php");

@$sponsor_id = $_POST['sponsor_id'];

if(isset($sponsor_id) and $sponsor_id != "")
{
	$registerResult = $db->view('regid,first_name,last_name,planid', 'mlm_registrations', 'regid', "and membership_id='$sponsor_id' and status='active'", 'regid desc');
	if($registerResult['num_rows'] >= 1)
	{
		$registerRow = $registerResult['result'][0];
		$planid = $registerRow['planid'];
		$planQueryResult = $db->view('planid,title,amount', 'mlm_plans', 'planid', "and planid='$planid' and status='active'", 'title asc');
		$planRow = $planQueryResult['result'][0];
		echo "You are eligible for <span style='font-weight:500;'>".$validation->db_field_validate($planRow['title'])."</span>. Make sure you have purchased the products of total &#8377;".$validation->db_field_validate($planRow['amount']);
	}
	else
	{
		echo '';
	}
}
else
{
	echo '';
}
?>