<?php
include_once("inc_config.php");

// createdate < DATE_SUB(NOW(), INTERVAL 85 DAY)

$date85days = date('Y-m-d', strtotime('-85 days'));

$purchaseResult = $db->view('membership_id,createdate', 'rb_purchases', 'purchaseid', "and reminder='0'", 'membership_id, purchaseid desc', '', 'refno_custom');
if($purchaseResult['num_rows'] >= 1)
{
	$membership_id_check = "";
	foreach($purchaseResult['result'] as $purchaseRow)
	{
		if($membership_id_check != $purchaseRow['membership_id'])
		{
			$membership_id_check = $validation->db_field_validate($purchaseRow['membership_id']);
		}
		else
		{
			continue;
		}
		
		$membership_id = $validation->db_field_validate($purchaseRow['membership_id']);
		$createdate = $validation->db_field_validate($purchaseRow['createdate']);
		
		if($createdate <= $date85days)
		{
			$registerResult = $db->view("first_name,last_name,email,mobile", "mlm_registrations", "regid", "and membership_id='{$membership_id}' and status='active'");
			$registerRow = $registerResult['result'][0];
			
			$email = $registerRow['email'];
			$first_name = $registerRow['first_name'];
			$last_name = $registerRow['last_name'];
			$mobile = $registerRow['mobile'];
			
			if($mobile != "")
			{
				$message = "Dear {$first_name} {$last_name}, You have not placed any order from last 85 days, please place an order within 5 days to be active otherwise your membership will be expired.". PHP_EOL ."". PHP_EOL ."Thank You". PHP_EOL ."Grocery Master.";
				$send = $api->sendSMS('ARIHAN', $mobile, $message);
				if($send)
				{
					echo "Reminder Sent";
				}
				else
				{
					echo "Failed";
				}
			}
			
			$purchaseupdateResult = $db->update("rb_purchases", array('reminder'=>'1'), array('membership_id'=>$membership_id));
		}
	}
}
else
{
	echo "No User Found!";
}

$date90days = date('Y-m-d', strtotime('-90 days'));

$purchaseResult2 = $db->view('membership_id,createdate', 'rb_purchases', 'purchaseid', "", 'membership_id, purchaseid desc', '', 'refno_custom');
if($purchaseResult2['num_rows'] >= 1)
{
	$membership_id_check = "";
	foreach($purchaseResult2['result'] as $purchaseRow2)
	{
		if($membership_id_check != $purchaseRow2['membership_id'])
		{
			$membership_id_check = $validation->db_field_validate($purchaseRow2['membership_id']);
		}
		else
		{
			continue;
		}
		
		$membership_id2 = $validation->db_field_validate($purchaseRow2['membership_id']);
		$createdate2 = $validation->db_field_validate($purchaseRow2['createdate']);
		
		if($createdate2 <= $date90days)
		{
			$registerResult2 = $db->view("first_name,last_name,email,mobile", "mlm_registrations", "regid", "and membership_id='{$membership_id2}' and status='active'");
			$registerRow = $registerResult2['result'][0];
			
			$email = $registerRow['email'];
			$first_name = $registerRow['first_name'];
			$last_name = $registerRow['last_name'];
			$mobile = $registerRow['mobile'];
			
			$registerupdateResult = $db->update("mlm_registrations", array('status'=>'inactive'), array('membership_id'=>$membership_id2));
			$registerupdateResult2 = $db->update("rb_registrations", array('status'=>'inactive'), array('membership_id'=>$membership_id2));
			
			if($mobile != "")
			{
				$message = "Dear {$first_name} {$last_name}, You have not placed any order from last 90 days so your membership is expired now. Kindly contact Administrator for any kind of help.".PHP_EOL."".PHP_EOL."Thank You".PHP_EOL."Grocery Master.";
				$send = $api->sendSMS('ARIHAN', $mobile, $message);
				if($send)
				{
					echo "Account Deactivated!";
				}
				else
				{
					echo "Failed";
				}
			}
		}
	}
}
else
{
	echo "No User Found!";
}
?>