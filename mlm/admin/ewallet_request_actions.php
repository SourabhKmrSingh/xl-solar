<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "ewallet_request";
echo $validation->section($_SESSION['per_ewallet']);

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$requestid = $validation->urlstring_validate($_GET['requestid']);
	
	$delresult = $media->multiple_filedeletion('mlm_ewallet_requests', 'requestid', $requestid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('mlm_ewallet_requests', 'requestid', $requestid, 'fileName', FILE_LOC);

	$ewalletrequestQueryResult = $db->delete("mlm_ewallet_requests", array('requestid'=>$requestid));
	if(!$ewalletrequestQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: ewallet_request_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$ewalletrequestQueryResult} Record Deleted!";
	header("Location: ewallet_request_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$requestids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: ewallet_request_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			array_push($requestids, "$id");
		}
		
		$requestids = implode(',', $requestids);
		
		if($bulk_actions == "delete")
		{
			$ewalletrequestQueryResult = $db->custom("DELETE from mlm_ewallet_requests where FIND_IN_SET(`requestid`, '$requestids')");
			if(!$ewalletrequestQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: ewallet_request_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: ewallet_request_view.php");
			exit();
		}
		else if($bulk_actions == "pending" || $bulk_actions == "approved" || $bulk_actions == "declined" || $bulk_actions == "fulfilled")
		{
			$ewalletrequestQueryResult = $db->custom("UPDATE mlm_ewallet_requests SET status='$bulk_actions' where FIND_IN_SET(`requestid`, '$requestids')");
			if(!$ewalletrequestQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: ewallet_request_view.php");
			exit();
		}
	}
}
else if(isset($_POST['excel']) and $_POST['excel'] != "")
{
	@$userid = $validation->input_validate($_GET['userid']);
	@$regid = $validation->input_validate($_GET['regid']);
	@$refno = $validation->input_validate($_GET['refno']);
	@$status = strtolower($validation->input_validate($_GET['status']));
	@$datefrom = $validation->input_validate($_GET['datefrom']);
	@$dateto = $validation->input_validate($_GET['dateto']);

	$where_query = "";
	if($userid != "")
	{
		$where_query .= " and userid = '$userid'";
	}
	if($regid != "")
	{
		$where_query .= " and regid = '$regid'";
	}
	if($refno != "")
	{
		$where_query .= " and refno = '$refno'";
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
	$exportResult = $db->view("*", "mlm_ewallet_requests", "requestid", $where_query, 'requestid desc');
	if($exportResult['num_rows'] >= 1)
	{
		$phpExcel->getActiveSheet()->SetCellValue('A1', 'S. No.');
		$phpExcel->getActiveSheet()->SetCellValue('B1', 'Transaction ID');
		$phpExcel->getActiveSheet()->SetCellValue('C1', 'User');
		$phpExcel->getActiveSheet()->SetCellValue('D1', 'Mobile No.');
		$phpExcel->getActiveSheet()->SetCellValue('E1', 'Amount');
		$phpExcel->getActiveSheet()->SetCellValue('F1', 'Remarks');
		$phpExcel->getActiveSheet()->SetCellValue('G1', 'Bank Name');
		$phpExcel->getActiveSheet()->SetCellValue('H1', 'Account Number');
		$phpExcel->getActiveSheet()->SetCellValue('I1', 'Bank Swift/IFSC Code');
		$phpExcel->getActiveSheet()->SetCellValue('J1', 'Account Name');
		$phpExcel->getActiveSheet()->SetCellValue('K1', 'Status');
		$phpExcel->getActiveSheet()->SetCellValue('L1', 'Date');
		
		foreach($exportResult['result'] as $exportRow)
		{
			$phpExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $slr);
			$phpExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $validation->db_field_validate($exportRow['refno']));
			$phpExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $validation->db_field_validate($exportRow['membership_id']));
			$phpExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $validation->db_field_validate($exportRow['mobile']));
			$phpExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $validation->price_format($exportRow['amount']));
			$phpExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $validation->db_field_validate($exportRow['remarks']));
			$phpExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $validation->db_field_validate($exportRow['bank_name']));
			$phpExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $validation->db_field_validate($exportRow['account_number']));
			$phpExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $validation->db_field_validate($exportRow['ifsc_code']));
			$phpExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $validation->db_field_validate($exportRow['account_name']));
			$phpExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $validation->db_field_validate(ucwords($exportRow['status'])));
			$phpExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $validation->db_field_validate($exportRow['createdate']));
			
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
	
	header("Location: ewallet_request_view.php?$fields_string");
	exit();
}
?>