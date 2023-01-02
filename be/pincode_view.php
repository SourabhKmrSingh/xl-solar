<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "pincode";

echo $validation->read_permission();

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$userid = $validation->input_validate($_GET['userid']);
@$pincode = $validation->input_validate($_GET['pincode']);
@$city = strtolower($validation->input_validate($_GET['city']));
@$state = strtolower($validation->input_validate($_GET['state']));
@$country = strtolower($validation->input_validate($_GET['country']));
@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

$where_query = "";
if($userid != "")
{
	$where_query .= " and userid = '$userid'";
}
if($pincode != "")
{
	$where_query .= " and pincode LIKE '%$pincode%'";
}
if($city != "")
{
	$where_query .= " and LOWER(city) LIKE '%$city%'";
}
if($state != "")
{
	$where_query .= " and LOWER(state) LIKE '%$state%'";
}
if($country != "")
{
	$where_query .= " and LOWER(country) LIKE '%$country%'";
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
	$orderby_final = "pincodeid desc";
}

$param1 = "pincode";
$param2 = "order_custom";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "rb_pincodes";
$id = "pincodeid";
$url_parameters = "&userid=$userid&pincode=$pincode&city=$city&state=$state&country=$country&status=$status&datefrom=$datefrom&dateto=$dateto";

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
		<h1 CLASS="page-header">Pincodes <?php if($_SESSION['per_write'] == "1") { ?><a href="pincode_form.php?mode=insert" class="btn btn-default btn-sm button">Add New</a><?php } ?></h1>
	</div>
</div>

<form name="form_actions" method="POST" action="pincode_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="row">
	<div class="col-sm-12 mb-0">
		<div class="form-inline">
			<select NAME="bulk_actions" CLASS="form-control mb_inline mb-2" >
				<option VALUE="">Bulk Actions</option>
				<option VALUE="delete">Delete</option>
				<option VALUE="active">Status to Active</option>
				<option VALUE="inactive">Status to Inactive</option>
			</select>
			<button type="submit" class="btn btn-default mb_inline btn-sm btn_submit mb-2 mr-4">Apply</button>
			
			<input type="text" name="pincode" class="form-control mb_inline mb-2" placeholder="Pincode" value="<?php echo $pincode; ?>" />
			<input type="text" name="city" class="form-control mb_inline mb-2" placeholder="City" value="<?php echo $city; ?>" />
			<input type="text" name="state" class="form-control mb_inline mb-2" placeholder="State" value="<?php echo $state; ?>" />
			<input type="text" name="country" class="form-control mb_inline mb-2" placeholder="Country" value="<?php echo $country; ?>" />
			<select NAME="status" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($status=='') echo "selected"; ?>>Status</option>
				<option VALUE="active" <?php if($status=="active") echo "selected"; ?>>Active</option>
				<option VALUE="inactive" <?php if($status=="inactive") echo "selected"; ?>>Inactive</option>
			</select>
			<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
			<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
			<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<a href="pincode_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div>
	</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="check-row text-center"><input type="checkbox" name="select_all" onClick="selectall(this);" /></th>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="pincode_view.php?orderby=pincode&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Pincode</span> <span class="sorting-indicator"></span></a></th>
		<th>Author</th>
		<th>City</th>
		<th>State</th>
		<th>Country</th>
		<th>Status</th>
		<th class="<?php echo $th_sort3." ".$th_order_cls3; ?>"><a href="pincode_view.php?orderby=createdate&order=<?php echo $th_order3.''.$url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $pincodeRow)
		{
			$userid = $pincodeRow['userid'];
			$userQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid}'");
			$userRow = $userQueryResult['result'][0];
		?>
		<tr class="text-center has-row-actions">
			<td class="text-center" data-label=""><input type="checkbox" name="del_items[]" value="<?php echo $validation->db_field_validate($pincodeRow['pincodeid']); ?>"/></td>
			<td data-label="Pincode - ">
				<a href="pincode_form.php?mode=edit&pincodeid=<?php echo $validation->db_field_validate($pincodeRow['pincodeid']); ?>" class="fw-500"><?php echo $validation->db_field_validate($pincodeRow['pincode']); ?></a>
				
				<div class="row row-actions">
					<div class="col-sm-12">
						<?php if($_SESSION['per_update'] == "1") { ?>
							<a href="pincode_form.php?mode=edit&pincodeid=<?php echo $validation->db_field_validate($pincodeRow['pincodeid']); ?>">Edit</a>
							 | 
						<?php } ?>
						<?php if($_SESSION['per_delete'] == "1") { ?>
							<a href="pincode_actions.php?q=del&pincodeid=<?php echo $validation->db_field_validate($pincodeRow['pincodeid']); ?>" onClick="return del();" class="delete">Delete</a>
						<?php } ?>
					</div>
				</div>
			</td>
			<td data-label="Author - "><a href="pincode_view.php?userid=<?php echo $userid; ?>"><?php echo $validation->db_field_validate($userRow['display_name']); ?></a></td>
			<td data-label="City - "><?php echo $validation->db_field_validate($pincodeRow['city']); ?></td>
			<td data-label="State - "><?php echo $validation->db_field_validate($pincodeRow['state']); ?></td>
			<td data-label="Country - "><?php echo $validation->db_field_validate($pincodeRow['country']); ?></td>
			<td data-label="Status - "><font color="<?php if($pincodeRow['status'] == "active") { echo "green"; } else { echo "red"; } ?>"><?php echo $validation->db_field_validate(ucfirst($pincodeRow['status'])); ?></font></td>
			<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($pincodeRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$pincodeRow['createdate']} {$pincodeRow['createtime']}"); ?>)</td>
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