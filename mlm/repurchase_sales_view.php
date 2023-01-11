<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "purchase";

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$userid = $validation->input_validate($_GET['userid']);
@$productid = $validation->input_validate($_GET['productid']);
@$refno_custom = strtoupper($validation->input_validate($_GET['refno_custom']));
@$payment_mode = $validation->input_validate($_GET['payment_mode']);
@$tracking_status = $validation->input_validate($_GET['tracking_status']);
@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

$where_query = " and tracking_status ='delivered' and income_type = 'repurchase'";
if($userid != "")
{
	$where_query .= " and userid = '$userid'";
}
if($productid != "")
{
	$where_query .= " and productid = '$productid'";
}
if($refno_custom != "")
{
	$where_query .= " and refno_custom = '$refno_custom'";
}
if($payment_mode != "")
{
	$where_query .= " and payment_mode = '$payment_mode'";
}
if($tracking_status != "")
{
	$where_query .= " and tracking_status = '$tracking_status'";
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
	$orderby_final = "purchaseid desc";
}

$param1 = "refno_custom";
$param2 = "price";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "rb_purchases";
$id = "purchaseid";
$groupby = "refno_custom";
$url_parameters = "&userid=$userid&productid=$productid&refno_custom=$refno_custom&payment_mode=$payment_mode&tracking_status=$tracking_status&status=$status&datefrom=$datefrom&dateto=$dateto";

$data = $pagination->main($table, $url_parameters, $where_query, $id, $orderby_final, $groupby);

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
		<h1 CLASS="page-header">Company Repurchase Details</h1>
	</div>
</div>

<form name="form_actions" method="POST" action="repurchase_sales_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="row">
	<div class="col-sm-12 mb-0">
		<!-- <div class="form-inline">
			<select NAME="bulk_actions" CLASS="form-control mb_inline mb-2" >
				<option VALUE="">Bulk Actions</option>
				<option VALUE="delete">Delete</option>
				<option VALUE="active">Status to Active</option>
				<option VALUE="inactive">Status to Inactive</option>
			</select>
			<button type="submit" class="btn btn-default mb_inline btn-sm btn_submit mb-2 mr-4">Apply</button>
			
			<input type="text" name="refno_custom" class="form-control mb_inline mb-2" placeholder="Order ID" value="<?php echo $refno_custom; ?>" />
			<select NAME="payment_mode" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($payment_mode=='') echo "selected"; ?>>Payment Mode</option>
				<option VALUE="cod" <?php if($payment_mode=="cod") echo "selected"; ?>>COD</option>
				<option VALUE="online transfer" <?php if($payment_mode=="online transfer") echo "selected"; ?>>Online Transfer</option>
			</select>
			<select NAME="tracking_status" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($tracking_status=='') echo "selected"; ?>>Tracking Status</option>
				<?php
				foreach($tracking_msgs as $key => $value)
				{
				?>
					<option value="<?php echo $key; ?>" <?php if($tracking_status == $key) echo "selected"; ?>><?php echo ucwords($key); ?></option>
				<?php
				}
				?>
			</select>
			<select NAME="status" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($status=='') echo "selected"; ?>>Status</option>
				<option VALUE="active" <?php if($status=="active") echo "selected"; ?>>Active</option>
				<option VALUE="inactive" <?php if($status=="inactive") echo "selected"; ?>>Inactive</option>
			</select>
			<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
			<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
			<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<a href="repurchase_sales_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div> -->
	</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th>Pincode</th>
		<th>Amount</th>
		<th>Business Volume</th>
		<th class="<?php echo $th_sort3." ".$th_order_cls3; ?>"><a href="repurchase_sales_view.php?orderby=createdate&order=<?php echo $th_order3.''.$url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $purchaseRow)
		{
			$productid = $purchaseRow['productid'];
			$productQueryResult = $db->view("title", "rb_products", "productid", "and productid='{$productid}'");
			$productRow = $productQueryResult['result'][0];
		?>
		<tr class="text-center has-row-actions">
			<td data-label="Pincode - "><?php echo strtoupper($validation->db_field_validate($purchaseRow['billing_pincode'])); ?></td>
			<td data-label="Amount - ">&#8377;<?php echo $validation->price_format($purchaseRow['final_price']); ?></td>
			<td data-label="Business Volume - "><?php echo $validation->price_format($purchaseRow['business_volume']); ?></td>
			<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($purchaseRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$purchaseRow['createdate']} {$purchaseRow['createtime']}"); ?>)</td>
		</tr>
		<?php
		}
	}
	else
	{
	?>
		<tr class="text-center">
			<td class="text-center" colspan="10">No Record is Available!</td>
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