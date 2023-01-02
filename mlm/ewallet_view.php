<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "ewallet";

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$refno = $validation->input_validate($_GET['refno']);
@$type = $validation->input_validate($_GET['type']);
@$reason = $validation->input_validate($_GET['reason']);
@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

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
$where_query .= " and regid='$regid'";

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
	$orderby_final = "ewalletid desc";
}

$param1 = "refno";
$param2 = "amount";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_ewallet";
$id = "ewalletid";
$url_parameters = "&refno=$refno&type=$type&reason=$reason&status=$status&datefrom=$datefrom&dateto=$dateto";

$data = $pagination->main($table, $url_parameters, $where_query, $id, $orderby_final);

echo $validation->search_filter_enable();

// $walletResult = $db->view("SUM(amount) as total_wallet_amount", "mlm_ewallet", "regid", "and type='credit' and regid='{$regid}'");
// $walletRow = $walletResult['result'][0];
// $total_wallet_amount = $walletRow['total_wallet_amount'];

// $walletrequestsResult = $db->view("SUM(amount) as total_requests_amount", "mlm_ewallet_requests", "regid", "and status != 'declined' and regid='{$regid}'");
// $walletrequestsRow = $walletrequestsResult['result'][0];
// $total_requests_amount = $walletrequestsRow['total_requests_amount'];

$totalwalletResult = $db->view('wallet_total,wallet_money', 'mlm_registrations', 'regid', "and regid = '$regid' and status='active'");
$totalwalletRow = $totalwalletResult['result'][0];
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
		<h1 CLASS="page-header">E-Wallet <a href="ewallet_request_form.php" class="btn btn-default btn-sm button">Request Money</a></h1>
	</div>
</div>

<form name="form_actions" method="POST" action="ewallet_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="row">
	<div class="col-sm-9 mb-0">
		<div class="form-inline">
			<input type="text" name="refno" class="form-control mb_inline mb-2" placeholder="Transaction ID" value="<?php echo $refno; ?>" />
			<select NAME="type" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($type=='') echo "selected"; ?>>Type</option>
				<option VALUE="credit" <?php if($type=="credit") echo "selected"; ?>>Credit</option>
				<option VALUE="debit" <?php if($type=="debit") echo "selected"; ?>>Debit</option>
			</select>
			<input type="text" name="reason" class="form-control mb_inline mb-2" placeholder="Reason" value="<?php echo $reason; ?>" />
			<!--<select NAME="status" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($status=='') echo "selected"; ?>>Status</option>
				<option VALUE="pending" <?php if($status=="pending") echo "selected"; ?>>Pending</option>
				<option VALUE="approved" <?php if($status=="approved") echo "selected"; ?>>Approved</option>
				<option VALUE="declined" <?php if($status=="declined") echo "selected"; ?>>Declined</option>
				<option VALUE="fulfilled" <?php if($status=="fulfilled") echo "selected"; ?>>Fulfilled</option>
			</select>-->
			<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
			<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
			<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<a href="ewallet_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div>
	</div>
	
	<div CLASS="col-sm-3 d-flex align-items-center justify-content-end">
		<h5>E-Wallet Balance - &#8377;<?php echo $validation->price_format($totalwalletRow['wallet_money']); ?></h5>
	</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="check-row text-center"><input type="checkbox" name="select_all" onClick="selectall(this);" /></th>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="ewallet_view.php?orderby=refno&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Transaction ID</span> <span class="sorting-indicator"></span></a></th>
		<th class="<?php echo $th_sort2." ".$th_order_cls2; ?>"><a href="ewallet_view.php?orderby=amount&order=<?php echo $th_order2; echo $url_parameters; ?>"><span>Amount</span> <span class="sorting-indicator"></span></a></th>
		<th>Type</th>
		<th>Reason</th>
		<th>Description</th>
		<!--<th>Status</th>-->
		<th class="<?php echo $th_sort3." ".$th_order_cls3; ?>"><a href="ewallet_view.php?orderby=createdate&order=<?php echo $th_order3.''.$url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $ewalletRow)
		{
		?>
		<tr class="text-center has-row-actions">
			<td class="text-center" data-label=""><input type="checkbox" name="del_items[]" value="<?php echo $validation->db_field_validate($ewalletRow['ewalletid']); ?>"/></td>
			<td data-label="Transaction ID - ">
				<?php echo $validation->db_field_validate($ewalletRow['refno']); ?>
			</td>
			<td data-label="Amount - ">&#8377;<?php echo $validation->price_format($ewalletRow['amount']); ?></td>
			<td data-label="Type - "><?php echo $validation->db_field_validate(ucwords($ewalletRow['type'])); ?></td>
			<td data-label="Reason - "><?php echo $validation->db_field_validate(ucwords($ewalletRow['reason'])); ?></td>
			<td data-label="Description - "><?php echo $validation->db_field_validate($ewalletRow['description']); ?></td>
			<!--<td data-label="Status - "><font color="<?php if($ewalletRow['status'] == "approved" || $ewalletRow['status'] == "fulfilled") { echo "green"; } else { echo "red"; } ?>"><?php echo $validation->db_field_validate(ucfirst($ewalletRow['status'])); ?></font></td>-->
			<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($ewalletRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$ewalletRow['createdate']} {$ewalletRow['createtime']}"); ?>)</td>
		</tr>
		<?php
		}
	}
	else
	{
	?>
		<tr class="text-center">
			<td class="text-center" colspan="7">No Record is Available!</td>
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