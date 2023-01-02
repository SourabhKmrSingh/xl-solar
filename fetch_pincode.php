<?php
include_once("inc_config.php");

@$pincode = $_POST['pincode'];

if(isset($pincode) and $pincode != "")
{
	$pincodeResult = $db->view('*', 'rb_pincodes', 'pincodeid', "and pincode='$pincode'", 'pincodeid desc');
	if($pincodeResult['num_rows'] >= 1)
	{
		$pincodeRow = $pincodeResult['result'][0];
		//echo "{$pincodeRow['city']}|{$pincodeRow['state']}|{$pincodeRow['country']}";
		echo json_encode(array($pincodeRow['city'], $pincodeRow['state'], $pincodeRow['country']));
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