<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "ewallet";

echo $validation->read_permission();

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$userid = $validation->input_validate($_GET['userid']);
@$regid = $validation->input_validate($_GET['regid']);
@$refno = $validation->input_validate($_GET['refno']);
@$type = $validation->input_validate($_GET['type']);
@$reason = $validation->input_validate($_GET['reason']);
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
$url_parameters = "&userid=$userid&regid=$regid&refno=$refno&type=$type&reason=$reason&status=$status&datefrom=$datefrom&dateto=$dateto";

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
		<h1 CLASS="page-header">Members E-Wallet <!--<?php if($_SESSION['per_write'] == "1") { ?><a href="ewallet_form.php?mode=insert" class="btn btn-default btn-sm button">Add New</a><?php } ?>--></h1>
	</div>
</div>

<form name="form_actions" method="POST" action="ewallet_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="row">
	<div class="col-sm-12 mb-0">
		<div class="form-inline">
			<select NAME="bulk_actions" CLASS="form-control mb_inline mb-2" >
				<option VALUE="">Bulk Actions</option>
				<option VALUE="delete">Delete</option>
				<option VALUE="pending">Status to Pending</option>
				<option VALUE="approved">Status to Approved</option>
				<option VALUE="declined">Status to Declined</option>
				<option VALUE="fulfilled">Status to Fulfilled</option>
			</select>
			<button type="submit" class="btn btn-default mb_inline btn-sm btn_submit mb-2 mr-4">Apply</button>
			
			<input type="text" name="refno" class="form-control mb_inline mb-2" placeholder="Transaction ID" value="<?php echo $refno; ?>" />
			<select NAME="type" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($type=='') echo "selected"; ?>>Type</option>
				<option VALUE="credit" <?php if($type=="credit") echo "selected"; ?>>Credit</option>
				<option VALUE="debit" <?php if($type=="debit") echo "selected"; ?>>Debit</option>
			</select>
			<input type="text" name="reason" class="form-control mb_inline mb-2" placeholder="Reason" value="<?php echo $reason; ?>" />
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
			<a href="ewallet_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div>
	</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="check-row text-center"><input type="checkbox" name="select_all" onClick="selectall(this);" /></th>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="ewallet_view.php?orderby=refno&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Transaction ID</span> <span class="sorting-indicator"></span></a></th>
		<th>User</th>
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
	substr(md5(rand(1, 99999)),0,22);
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $ewalletRow)
		{
		?>
		<tr class="text-center has-row-actions">
			<td class="text-center" data-label=""><input type="checkbox" name="del_items[]" value="<?php echo $validation->db_field_validate($ewalletRow['ewalletid']); ?>"/></td>
			<td data-label="Transaction ID - ">
				<?php echo $validation->db_field_validate($ewalletRow['refno']); ?>
				
				<div class="row row-actions">
					<div class="col-sm-12">
						<?php if($_SESSION['per_delete'] == "1") { ?>
							<a href="ewallet_actions.php?q=del&ewalletid=<?php echo $validation->db_field_validate($ewalletRow['ewalletid']); ?>" onClick="return del();" class="delete">Delete</a>
						<?php } ?>
					</div>
				</div>
			</td>
			<td data-label="User - "><a href="ewallet_view.php?regid=<?php echo $validation->db_field_validate($ewalletRow['regid']); ?>"><?php echo $validation->db_field_validate($ewalletRow['membership_id']); ?></a></td>
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