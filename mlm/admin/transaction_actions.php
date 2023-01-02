<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "transaction";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$transactionid = $validation->urlstring_validate($_GET['transactionid']);
	
	$delresult = $media->multiple_filedeletion('mlm_transactions', 'transactionid', $transactionid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('mlm_transactions', 'transactionid', $transactionid, 'fileName', FILE_LOC);

	$transactionQueryResult = $db->delete("mlm_transactions", array('transactionid'=>$transactionid));
	if(!$transactionQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: transaction_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$transactionQueryResult} Record Deleted!";
	header("Location: transaction_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$transactionids = array();
	$refnos = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: transaction_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($transactionids, "$id");
				
				$delresult = $media->filedeletion('mlm_transactions', 'transactionid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('mlm_transactions', 'transactionid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "pending" || $bulk_actions == "approved" || $bulk_actions == "declined" || $bulk_actions == "fulfilled")
			{
				array_push($transactionids, "$id");
				
				$transactionResult = $db->view('refno', 'mlm_transactions', 'transactionid', "and transactionid='$id'", 'transactionid desc');
				$transactionRow = $transactionResult['result'][0];
				array_push($refnos, $transactionRow['refno']);
			}
		}
		
		$transactionids = implode(',', $transactionids);
		$refnos = implode(',', $refnos);
		
		if($bulk_actions == "delete")
		{
			$transactionQueryResult = $db->custom("DELETE from mlm_transactions where FIND_IN_SET(`transactionid`, '$transactionids')");
			if(!$transactionQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: transaction_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: transaction_view.php");
			exit();
		}
		else if($bulk_actions == "pending" || $bulk_actions == "approved" || $bulk_actions == "declined" || $bulk_actions == "fulfilled")
		{
			$transactionQueryResult = $db->custom("UPDATE mlm_transactions SET status='$bulk_actions' where FIND_IN_SET(`transactionid`, '$transactionids')");
			if(!$transactionQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			$ewalletResult = $db->custom("UPDATE mlm_ewallet SET status='$bulk_actions' where FIND_IN_SET(`refno`, '$refnos')");
			if(!$ewalletResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: transaction_view.php");
			exit();
		}
	}
}
else if(isset($_POST['excel']) and $_POST['excel'] != "")
{
	@$refno = $validation->input_validate($_POST['refno']);
	@$type = $validation->input_validate($_POST['type']);
	@$reason = $validation->input_validate($_POST['reason']);
	@$status = strtolower($validation->input_validate($_POST['status']));
	@$datefrom = $validation->input_validate($_POST['datefrom']);
	@$dateto = $validation->input_validate($_POST['dateto']);

	$where_query = "";
	if($refno != "")
	{
		$where_query .= " and refno = '$refno'";
	}
	if($type != "")
	{
		$where_query .= " and type = '$type'";
	}
	if($reason != "")
	{
		$where_query .= " and reason = '$reason'";
	}
	if($status != "")
	{
		$where_query .= " and status = '$status'";
	}
	if($datefrom != "" and $dateto != "")
	{
		$where_query .= " and createdate between '$datefrom' and '$dateto'";
	}
	//$where_query .= "and amount != '0.00'";
	
	$slr = 1;
	$rowCount = 3;
	$exportResult = $db->view("SUM(amount) as total_amount,membership_id,amount,type,reason,description,status,createdate", "mlm_transactions", "transactionid", $where_query, 'transactionid desc', '', 'membership_id');
	if($exportResult['num_rows'] >= 1)
	{
		$phpExcel->getActiveSheet()->SetCellValue('A1', 'S. No.');
		$phpExcel->getActiveSheet()->SetCellValue('B1', 'User');
		$phpExcel->getActiveSheet()->SetCellValue('C1', 'Email');
		$phpExcel->getActiveSheet()->SetCellValue('D1', 'Amount');
		$phpExcel->getActiveSheet()->SetCellValue('E1', 'Type');
		$phpExcel->getActiveSheet()->SetCellValue('F1', 'Reason');
		$phpExcel->getActiveSheet()->SetCellValue('G1', 'Description');
		$phpExcel->getActiveSheet()->SetCellValue('H1', 'Bank Name');
		$phpExcel->getActiveSheet()->SetCellValue('I1', 'Account Number');
		$phpExcel->getActiveSheet()->SetCellValue('J1', 'Bank Swift/IFSC Code');
		$phpExcel->getActiveSheet()->SetCellValue('K1', 'Account Name');
		$phpExcel->getActiveSheet()->SetCellValue('L1', 'Status');
		$phpExcel->getActiveSheet()->SetCellValue('M1', 'Date');
		
		foreach($exportResult['result'] as $exportRow)
		{
			$membership_id = $exportRow['membership_id'];
			$registerResult = $db->view("email,bank_name,account_number,ifsc_code,account_name", "mlm_registrations", "regid", "and membership_id='{$membership_id}'");
			$registerRow = $registerResult['result'][0];
			
			$phpExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $slr);
			$phpExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $validation->db_field_validate($exportRow['membership_id']));
			$phpExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $validation->db_field_validate($registerRow['email']));
			$phpExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $validation->price_format($exportRow['total_amount']));
			$phpExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $validation->db_field_validate(ucwords($exportRow['type'])));
			$phpExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $validation->db_field_validate(ucwords($exportRow['reason'])));
			$phpExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $validation->db_field_validate($exportRow['description']));
			$phpExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $validation->db_field_validate($registerRow['bank_name']));
			$phpExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $validation->db_field_validate($registerRow['account_number']));
			$phpExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $validation->db_field_validate($registerRow['ifsc_code']));
			$phpExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $validation->db_field_validate($registerRow['account_name']));
			$phpExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $validation->db_field_validate(ucwords($exportRow['status'])));
			$phpExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $validation->db_field_validate($exportRow['createdate']));
			
			$slr++;
			$rowCount++;
		}
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="transactions_list.xlsx"');
		header('Cache-Control: max-age=0');
		
		$writer = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$writer->save('php://output');
		
		//$_SESSION['success_msg'] = "{$exportResult['num_rows']} Record(s) Downloaded Successfully!";
		//header("Location: transaction_view.php");
		exit();
	}
	else
	{
		$_SESSION['error_msg'] = "There is no record in the database!";
		header("Location: transaction_view.php");
		exit();
	}
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
	$fields_string = str_replace("excel=Download Data&", "", $fields_string);
	$fields_string = substr($fields_string, 0, -1);
	
	header("Location: transaction_view.php?$fields_string");
	exit();
}
?>