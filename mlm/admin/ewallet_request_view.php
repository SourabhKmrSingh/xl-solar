<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "ewallet_request";
echo $validation->section($_SESSION['per_ewallet']);

echo $validation->read_permission();

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

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

if($orderby != "" and $order != "")
{
	$orderby_final = "{$orderby} {$order}";
	if($orderby == "createdate")
	{
		$orderby_final .= ", createtime {$order}";
	}
}
else
{
	$orderby_final = "requestid desc";
}

$param1 = "refno";
$param2 = "amount";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_ewallet_requests";
$id = "requestid";
$url_parameters = "&userid=$userid&regid=$regid&refno=$refno&status=$status&datefrom=$datefrom&dateto=$dateto";

$data = $pagination->main($table, $url_parameters, $where_query, $id, $orderby_final);

echo $validation->search_filter_enable();
?>
<!DOCTYPE html>
<html LANG="en">
<head>
<?php include_once("inc_title.php"); ?>
<?php include_once("inc_files.php"); ?>
</head>
<body>
<div ID="wrapper">
<?php include_once("inc_header.php"); ?>
<div ID="page-wrapper">
<div CLASS="container-fluid">
<div CLASS="row">
	<div CLASS="col-lg-12">
		<h1 CLASS="page-header">Members E-Wallet Transactions</h1>
	</div>
</div>

<form name="form_actions" method="POST" action="ewallet_request_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="row">
	<div class="col-sm-12 mb-0">
		<div class="form-inline">
			<select NAME="bulk_actions" CLASS="form-control mb_inline mb-2" >
				<option VALUE="">Bulk Actions</option>
				<option VALUE="delete">Delete</option>
				<option VALUE="pending">Status to Pending</option>
				<option VALUE="approved">Status to Approved</option>
				<!--<option VALUE="declined">Status to Declined</option>-->
				<option VALUE="fulfilled">Status to Fulfilled</option>
			</select>
			<button type="submit" class="btn btn-default mb_inline btn-sm btn_submit mb-2 mr-4">Apply</button>
			
			<input type="text" name="refno" class="form-control mb_inline mb-2" placeholder="Transaction ID" value="<?php echo $refno; ?>" />
			<select NAME="status" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($status=='') echo "selected"; ?>>Status</option>
				<option VALUE="pending" <?php if($status=="pending") echo "selected"; ?>>Pending</option>
				<option VALUE="approved" <?php if($status=="approved") echo "selected"; ?>>Approved</option>
				<option VALUE="declined" <?php if($status=="declined") echo "selected"; ?>>Declined</option>
				<option VALUE="fulfilled" <?php if($status=="fulfilled") echo "selected"; ?>>Fulfilled</option>
			</select>
			<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
			<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
			<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<input type="submit" name="excel" value="Download Data" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<a href="ewallet_request_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div>
	</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="check-row text-center"><input type="checkbox" name="select_all" onClick="selectall(this);" /></th>
		<th>Transaction ID</th>
		<th>User</th>
		<th>Name</th>
		<th class="<?php echo $th_sort2." ".$th_order_cls2; ?>"><a href="ewallet_request_view.php?orderby=amount&order=<?php echo $th_order2; echo $url_parameters; ?>"><span>Amount</span> <span class="sorting-indicator"></span></a></th>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="ewallet_request_view.php?orderby=balance&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Balance</span> <span class="sorting-indicator"></span></a></th>
		<th>Bank Details</th>
		<th>Remarks</th>
		<th>Status</th>
		<th class="<?php echo $th_sort3." ".$th_order_cls3; ?>"><a href="ewallet_request_view.php?orderby=createdate&order=<?php echo $th_order3.''.$url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $ewalletrequestRow)
		{
			$memberRegid = $ewalletrequestRow['regid'];
			$memberResult = $db->view('*','mlm_registrations','regid'," and regid='{$memberRegid}'");
			$memberRow = $memberResult['result'][0];
		?>
		<tr class="text-center has-row-actions">
			<td class="text-center" data-label=""><input type="checkbox" name="del_items[]" value="<?php echo $validation->db_field_validate($ewalletrequestRow['requestid']); ?>"/></td>
			<td data-label="Transaction ID - ">
				<?php echo $validation->db_field_validate($ewalletrequestRow['refno']); ?>
				
				<div class="row row-actions">
					<div class="col-sm-12">
						<?php if($ewalletrequestRow['status'] == "pending") { ?>
						<!--<a href="payout/Transaction.php?id=<?php echo $validation->db_field_validate($ewalletrequestRow['requestid']); ?>">Approve Request</a>
						 | -->
						<a href="ewallet_request_form.php?requestid=<?php echo $validation->db_field_validate($ewalletrequestRow['requestid']); ?>" class="delete">Decline Request</a>
						 | 
						<?php } ?>
						<?php if($_SESSION['per_delete'] == "1") { ?>
							<a href="ewallet_request_actions.php?q=del&requestid=<?php echo $validation->db_field_validate($ewalletrequestRow['requestid']); ?>" onClick="return del();" class="delete">Delete</a>
						<?php } ?>
					</div>
				</div>
			</td>
			<td data-label="User - "><a href="ewallet_request_view.php?regid=<?php echo $validation->db_field_validate($ewalletrequestRow['regid']); ?>"><?php echo $validation->db_field_validate($ewalletrequestRow['membership_id']); ?></a></td>
			<td data-label="Name - "><?php echo $validation->db_field_validate($memberRow['first_name']). " " . $validation->db_field_validate($memberRow['last_name']);?></td>
			<td data-label="Amount - ">
				<?php $amount = $validation->price_format($ewalletrequestRow['amount']);
							
							$adminAmount = $ewalletrequestRow['amount'] * $configRow['admin_rate']/100;
							$TdsAmount = $ewalletrequestRow['amount'] * $configRow['tax_rate']/100;
							
							echo "Amount : {$amount}<Br> TDS:  - ₹". $TdsAmount ."<br> Admin: - ₹" . $adminAmount . "<br><b>Total Amount :- ₹" . ($ewalletrequestRow['amount'] - $adminAmount - $TdsAmount) . "</b>";
					?>
			</td>
			<td data-label="Balance - ">&#8377;<?php echo $validation->price_format($ewalletrequestRow['balance']); ?></td>
			<td data-label="Bank Details - ">
				Bank Name: <?php echo $validation->db_field_validate($ewalletrequestRow['bank_name']); ?><br />
				Account Number: <?php echo $validation->db_field_validate($ewalletrequestRow['account_number']); ?><br />
				Bank Swift/IFSC Code: <?php echo $validation->db_field_validate($ewalletrequestRow['ifsc_code']); ?><br />
				Account Name: <?php echo $validation->db_field_validate($ewalletrequestRow['account_name']); ?><br />
			</td>
			<td data-label="Remarks - "><?php echo $validation->db_field_validate($ewalletrequestRow['remarks']); ?></td>
			<td data-label="Status - "><font color="<?php if($ewalletrequestRow['status'] == "approved" || $ewalletrequestRow['status'] == "fulfilled") { echo "green"; } else { echo "red"; } ?>"><?php echo $validation->db_field_validate(ucfirst($ewalletrequestRow['status'])); ?></font></td>
			<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($ewalletrequestRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$ewalletrequestRow['createdate']} {$ewalletrequestRow['createtime']}"); ?>)</td>
		</tr>
		<?php
		}
	}
	else
	{
	?>
		<tr class="text-center">
			<td class="text-center" colspan="8">No Record is Available!</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
</div>
</form>

<hr />
<?php echo $data['content']; ?>
<hr />
</div>
</div>
</div>
</body>
</html>