<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "transfer";



@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$regid = $validation->input_validate($_GET['regid']);
@$planid = $validation->input_validate($_GET['planid']);
@$title = strtolower($validation->input_validate($_GET['title']));
@$title_id = strtolower($validation->input_validate($_GET['title_id']));
@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

$where_query = "";
if ($regid != "") {
	$where_query .= " and regid = '$regid'";
}
if ($planid != "") {
	$where_query .= " and planid = '$planid'";
}
if ($title != "") {
	$where_query .= " and LOWER(title) LIKE '%$title%'";
}
if ($title_id != "") {
	$where_query .= " and title_id = '$title_id'";
}
if ($status != "") {
	$where_query .= " and status = '$status'";
}
if ($datefrom != "" and $dateto != "") {
	$where_query .= " and createdate between '$datefrom' and '$dateto'";
}

if ($orderby != "" and $order != "") {
	$orderby_final = "{$orderby} {$order}";
	if ($orderby == "createdate") {
		$orderby_final .= ", createtime {$order}";
	}
} else {
	$orderby_final = "transferid desc";
}

$where_query = " and ownMemberId='{$_SESSION['mlm_membership_id']}'";

$param1 = "title";
$param2 = "order_custom";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_wallet_transfer";
$id = "transferid";
$url_parameters = "&userid=$userid&planid=$planid&title=$title&title_id=$title_id&status=$status&datefrom=$datefrom&dateto=$dateto";

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
						<h1 CLASS="page-header">Transfer Money <a href="wallet_transfer_form.php?mode=insert&membershipid=<?php echo $data['result'][0]['membership_id'];?>" class="btn btn-default btn-sm button">Transfer</a></h1>
					</div>
				</div>

				<form name="form_actions" method="POST" action="wallet_transfer_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
					<div class="row">
						<div class="col-sm-12 mb-0">
							<div class="form-inline">
								<!-- <select NAME="bulk_actions" CLASS="form-control mb_inline mb-2">
									<option VALUE="">Bulk Actions</option>
									<option VALUE="delete">Delete</option>
								</select>
								<button type="submit" class="btn btn-default mb_inline btn-sm btn_submit mb-2 mr-4">Apply</button> -->

								<!-- <select NAME="status" CLASS="form-control mb_inline mb-2">
									<option VALUE="" <?php if ($status == '') echo "selected"; ?>>Status</option>
									<option VALUE="active" <?php if ($status == "active") echo "selected"; ?>>Active</option>
									<option VALUE="inactive" <?php if ($status == "inactive") echo "selected"; ?>>Inactive</option>
								</select> -->
								<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
								<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
								<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
								<a href="wallet_transfer_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
							</div>
						</div>
					</div>


					<div class="table-responsive">
						<table class="table table-striped table-view" cellspacing="0" width="100%">
							<thead>
								<tr>
									<!-- <th class="check-row text-center"><input type="checkbox" name="select_all" onClick="selectall(this);" /></th> -->
									<th>Refno</th>
									<th>Transfered User Membership Id</th>
									<th>Amount</th>
									<!-- <th>Status</th> -->
									<th class="<?php echo $th_sort3 . " " . $th_order_cls3; ?>"><a href="franchise_view.php?orderby=createdate&order=<?php echo $th_order3 . '' . $url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($data['num_rows'] >= 1) {
									$slr = 1;
									foreach ($data['result'] as $franchiseRow) {
										$userid = $franchiseRow['userid'];
										$userQueryResult = $db->view("display_name", "mlm_users", "userid", "and userid='{$userid}'");
										$userRow = $userQueryResult['result'][0];

										
								?>
										<tr class="text-center has-row-actions">
											<!-- <td class="text-center" data-label=""><input type="checkbox" name="del_items[]" value="<?php echo $validation->db_field_validate($franchiseRow['transferid']); ?>" /></td> -->
											
											<td data-label="Refno - "><?php echo $franchiseRow['refno']; ?>
												<!-- <div class="row row-actions">
													<div class="col-sm-12">
														<a href="wallet_transfer_actions.php?q=del&transferid=<?php echo $validation->db_field_validate($franchiseRow['transferid']); ?>" onClick="return del();" class="delete">Delete</a>
													</div>
												</div> -->
											</td>
											<td data-label="Transfered User Membership Id - "><?php echo $franchiseRow['transferMemberId']; ?></td>
											<td data-label="Amount - ">₹<?= $validation->price_format($franchiseRow['amount']);?></td>
											<!-- <td data-label="Status - ">
												<font color="<?php if ($franchiseRow['status'] == "active") {
																	echo "green";
																} else {
																	echo "red";
																} ?>"><?php echo $validation->db_field_validate(ucfirst($franchiseRow['status'])); ?></font>
											</td> -->
											<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($franchiseRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$franchiseRow['createdate']} {$franchiseRow['createtime']}"); ?>)</td>
										</tr>
									<?php
									$slr++;
									}
								} else {
									?>
									<tr class="text-center">
										<td class="text-center" colspan="6">No Record is Available!</td>
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