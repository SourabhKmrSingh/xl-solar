<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "generate_pins";

echo $validation->read_permission();

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$regid = $validation->input_validate($_GET['regid']);
@$planid = $validation->input_validate($_GET['planid']);
@$membership_id = strtolower($validation->input_validate($_GET['membership_id']));
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
if ($membership_id != "") {
	$where_query .= " and membership_id = '$membership_id'";
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
	$orderby_final = "gpinid desc";
}

$param1 = "title";
$param2 = "order_custom";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_pins_track";
$id = "gpinid";
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
						<h1 CLASS="page-header">Generate Pins <?php if ($_SESSION['per_write'] == "1") { ?><a href="generate_pins_form.php?mode=insert" class="btn btn-default btn-sm button">Generate New Pins</a><?php } ?></h1>
					</div>
				</div>

				<form name="form_actions" method="POST" action="generate_pins_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
					<div class="row">
						<div class="col-sm-12 mb-0">
							<div class="form-inline">
								<select NAME="bulk_actions" CLASS="form-control mb_inline mb-2">
									<option VALUE="">Bulk Actions</option>
									<option VALUE="delete">Delete</option>
								</select>
								<button type="submit" class="btn btn-default mb_inline btn-sm btn_submit mb-2 mr-4">Apply</button>

								<input type="text" name="membership_id" class="form-control mb_inline mb-2" placeholder="Membership ID" value="<?php echo $membership_id; ?>" />
								<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
								<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
								<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
								<a href="generate_pins_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
							</div>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-striped table-view" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th class="check-row text-center"><input type="checkbox" name="select_all" onClick="selectall(this);" /></th>
									<th>Transaction ID</th>
									<th>Membership ID</th>
									<th>Username</th>
									<th>Pins</th>
									<th>Free</th>
									<th>Tax</th>
									<th>Amount</th>
									<th>Plan</th>
									<th>Status</th>
									<th class="<?php echo $th_sort3 . " " . $th_order_cls3; ?>"><a href="generate_pins_view.php?orderby=createdate&order=<?php echo $th_order3 . '' . $url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($data['num_rows'] >= 1) {
									foreach ($data['result'] as $franchiseRow) {
										$userid = $franchiseRow['userid'];
										$userQueryResult = $db->view("display_name", "mlm_users", "userid", "and userid='{$userid}'");
										$userRow = $userQueryResult['result'][0];

										$planid = $franchiseRow['planid'];
										$planQueryResult = $db->view("title", "mlm_plans", "planid", "and planid='{$planid}'");
										$planRow = $planQueryResult['result'][0];
								?>
										<tr class="text-center has-row-actions">
											<td class="text-center" data-label=""><input type="checkbox" name="del_items[]" value="<?php echo $validation->db_field_validate($franchiseRow['gpinid']); ?>" /></td>
											
											<td data-label="Transaction ID - ">#<?php echo $validation->db_field_validate($franchiseRow['refno']); ?>
												<div class="row row-actions">
													<div class="col-sm-12">
														<?php if($_SESSION['per_delete'] == "1") { ?>
															<a href="generate_pins_actions.php?q=del&gpinid=<?php echo $validation->db_field_validate($franchiseRow['gpinid']); ?>" onClick="return del();" class="delete">Delete</a>
														<?php } ?>
													</div>
												</div>
											</td>
											<td data-label="Membership ID - "><a href="generate_pins_view.php?mode=edit&regid=<?php echo $franchiseRow['regid'];?>"><?php echo $validation->db_field_validate($franchiseRow['membership_id']); ?></a></td>
											<td data-label="username - "><?php echo $franchiseRow['username']; ?></td>
											<td data-label="Pins - "><?php echo $franchiseRow['pin_total']; ?></td>
											<td data-label="free - "><?php echo $validation->db_field_validate($franchiseRow['free_pins']); ?></td>
											<td data-label="tax - "><?php echo $validation->db_field_validate($franchiseRow['tax']); ?></td>
											<td data-label="Amount - "><?php echo $validation->db_field_validate($franchiseRow['total_amount']); ?></td>
											<td data-label="Plan - "><a href="generate_pins_view.php?planid=<?php echo $planid; ?>"><?php echo $validation->db_field_validate($planRow['title']); ?></a></td>
											<td data-label="Status - ">
												<font color="<?php if ($franchiseRow['status'] == "fulfilled") {
																	echo "green";
																} else {
																	echo "red";
																} ?>"><?php echo $validation->db_field_validate(ucfirst($franchiseRow['status'])); ?></font>
											</td>
											<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($franchiseRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$franchiseRow['createdate']} {$franchiseRow['createtime']}"); ?>)</td>
										</tr>
									<?php
									}
								} else {
									?>
									<tr class="text-center">
										<td class="text-center" colspan="11">No Record is Available!</td>
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