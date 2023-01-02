<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "challengehistory";

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$membership_id = $validation->input_validate($_GET['membership_id']);
@$reward = $validation->input_validate($_GET['reward']);
@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

$where_query = "";
if($membership_id != "")
{
	$where_query .= " and membership_id = '$membership_id'";
}
if($reward != "")
{
	$where_query .= " and reward LIKE '%$reward%'";
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
	$orderby_final = "historyid desc";
}

$param1 = "membership_id";
$param2 = "price";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_challenges_history";
$id = "historyid";
$url_parameters = "&membership_id=$membership_id&type=$type&reward=$reward&status=$status&datefrom=$datefrom&dateto=$dateto";

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
		<h1 CLASS="page-header">Achievements</h1>
	</div>
</div>

<form name="form_actions" method="POST" action="challenge_history_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="row">
	<div class="col-sm-12 mb-0">
		<div class="form-inline">
			<input type="text" name="reward" class="form-control mb_inline mb-2" placeholder="Reward" value="<?php echo $reward; ?>" />
			<select NAME="status" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($status=='') echo "selected"; ?>>Status</option>
				<option VALUE="pending" <?php if($status=="pending") echo "selected"; ?>>Pending</option>
				<option VALUE="claimed" <?php if($status=="claimed") echo "selected"; ?>>Claimed</option>
				<option VALUE="declined" <?php if($status=="declined") echo "selected"; ?>>Declined</option>
				<option VALUE="fulfilled" <?php if($status=="fulfilled") echo "selected"; ?>>Fulfilled</option>
				<option VALUE="achieved" <?php if($status=="achieved") echo "selected"; ?>>Achieved</option>
			</select>
			<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
			<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
			<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<a href="challenge_history_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div>
	</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="check-row text-center"><input type="checkbox" name="select_all" onClick="selectall(this);" /></th>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="challenge_history_view.php?orderby=membership_id&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Membership ID</span> <span class="sorting-indicator"></span></a></th>
		<th class="<?php echo $th_sort2." ".$th_order_cls2; ?>"><a href="challenge_history_view.php?orderby=time_period&order=<?php echo $th_order2; echo $url_parameters; ?>"><span>Time Period</span> <span class="sorting-indicator"></span></a></th>
		<th>Members</th>
		<th>Reward</th>
		<th>Description</th>
		<th>Status</th>
		<th class="<?php echo $th_sort3." ".$th_order_cls3; ?>"><a href="challenge_history_view.php?orderby=createdate&order=<?php echo $th_order3.''.$url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $challengehistoryRow)
		{
		?>
		<tr class="text-center has-row-actions">
			<td class="text-center" data-label=""><input type="checkbox" name="del_items[]" value="<?php echo $validation->db_field_validate($challengehistoryRow['historyid']); ?>"/></td>
			<td data-label="Membership ID - ">
				<?php echo $validation->db_field_validate($challengehistoryRow['membership_id']); ?>
				
				<?php if($challengehistoryRow['status'] != "claimed" and $challengehistoryRow['status'] != "fulfilled") { ?>
				<div class="row row-actions">
					<div class="col-sm-12">
						<a href="challenge_history_actions.php?q=claim&historyid=<?php echo $validation->db_field_validate($challengehistoryRow['historyid']); ?>">Claim</a>
					</div>
				</div>
				<?php } ?>
			</td>
			<td data-label="Time Period - "><?php echo $validation->db_field_validate($challengehistoryRow['time_period']); ?> Days</td>
			<td data-label="Members - "><?php echo $validation->db_field_validate($challengehistoryRow['members']); ?></td>
			<td data-label="Reward - "><?php echo $validation->db_field_validate($challengehistoryRow['reward']); ?></td>
			<td data-label="Description - "><?php echo $validation->db_field_validate($challengehistoryRow['description']); ?></td>
			<td data-label="Status - "><font color="<?php if($challengehistoryRow['status'] == "claimed" || $challengehistoryRow['status'] == "fulfilled" || $challengehistoryRow['status'] == "achieved") { echo "green"; } else { echo "red"; } ?>"><?php echo $validation->db_field_validate(ucfirst($challengehistoryRow['status'])); ?></font></td>
			<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($challengehistoryRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$challengehistoryRow['createdate']} {$challengehistoryRow['createtime']}"); ?>)</td>
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