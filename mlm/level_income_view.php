<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "level_income_view";

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);
@$userid = $validation->input_validate($_GET['userid']);
@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

$membership_id = $_SESSION['mlm_membership_id'];
$where_query = " and membership_id = '$membership_id' and status = 'unpaid'";

if($userid != "")
{
	$where_query .= " and userid = '$userid'";
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
	$orderby_final = "distributionId desc";
}

$param1 = "membership_id";
$param2 = "total_amount";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_distribution_level";
$id = "distributionId";
$url_parameters = "&userid=$userid&status=$status&datefrom=$datefrom&dateto=$dateto";

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
		<h1 CLASS="page-header">Level Income</h1>
	</div>
</div>

<form name="form_actions" method="POST" action="level_income_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<!-- <div class="row">
	<div class="col-sm-12 mb-0">
		<div class="form-inline">
			<select NAME="status" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($status=='') echo "selected"; ?>>Status</option>
				<option VALUE="active" <?php if($status=="active") echo "selected"; ?>>Active</option>
				<option VALUE="inactive" <?php if($status=="inactive") echo "selected"; ?>>Inactive</option>
			</select>
			<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
			<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
			<input type="submit" value="Filter" class="btn  mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<a href="direct_member_view.php" class="btn  mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div>
	</div>
</div> -->

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="level_income_view.php?orderby=first_name&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Refno</span> <span class="sorting-indicator"></span></a></th>
		<th>Clear Level</th>
		<th>Amount</th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $distributionRow)
		{
			$distributionId = $distributionRow['distributionId'];
			$levelid = $distributionRow['levelid'];
			$levelResult = $db->view("title", "mlm_levels", "levelid", " and levelid = '$levelid'");
			$levelRow = $levelResult['result'][0];
		?>
		<tr class="text-center has-row-actions">
			<td data-label="Name - ">
				<a href="javascript:void(0);" class="fw-500"><?php echo $validation->db_field_validate($distributionRow['refno']); ?></a>
			</td>
			<td data-label="Clear Level - " class="font-weight-bold"><?php echo $validation->db_field_validate($levelRow['title']); ?></td>
			<td data-label="Amount - " class="font-weight-bold">₹<?php echo $validation->db_field_validate($distributionRow['amount']); ?></td>
		</tr>
		<?php
		}
	}
	else
	{
	?>
		<tr class="text-center">
			<td class="text-center" colspan="9">No Record is Available!</td>
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