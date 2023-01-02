<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "user_master";

echo $validation->admin_permission();
echo $validation->read_permission();

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$username = strtolower($validation->input_validate($_GET['username']));
@$display_name = strtolower($validation->input_validate($_GET['display_name']));
@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

$where_query = "";
if($username != "")
{
	$where_query .= " and LOWER(username) LIKE '%$username%'";
}
if($display_name != "")
{
	$where_query .= " and display_name = '$display_name'";
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
	$orderby_final = "userid desc";
}

$param1 = "username";
$param2 = "order_custom";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_users";
$id = "userid";
$url_parameters = "&username=$username&display_name=$display_name&status=$status&datefrom=$datefrom&dateto=$dateto";

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
		<h1 CLASS="page-header">Users <?php if($_SESSION['per_write'] == "1") { ?><a href="user_master_form.php?mode=insert" class="btn btn-default btn-sm button">Add New</a><?php } ?></h1>
	</div>
</div>

<form name="form_actions" method="POST" action="user_master_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
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
			
			<input type="text" name="username" class="form-control mb_inline mb-2" placeholder="Username" value="<?php echo $username; ?>" />
			<input type="text" name="display_name" class="form-control mb_inline mb-2" placeholder="Display Name" value="<?php echo $display_name; ?>" />
			<select NAME="status" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($status=='') echo "selected"; ?>>Status</option>
				<option VALUE="active" <?php if($status=="active") echo "selected"; ?>>Active</option>
				<option VALUE="inactive" <?php if($status=="inactive") echo "selected"; ?>>Inactive</option>
			</select>
			<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
			<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
			<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<a href="user_master_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div>
	</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="check-row text-center"><input type="checkbox" name="select_all" onClick="selectall(this);" /></th>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="user_master_view.php?orderby=username&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Username</span> <span class="sorting-indicator"></span></a></th>
		<th>Type</th>
		<th>Display Name</th>
		<th>Permissions</th>
		<th>Status</th>
		<th class="<?php echo $th_sort3." ".$th_order_cls3; ?>"><a href="user_master_view.php?orderby=createdate&order=<?php echo $th_order3.''.$url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $userRow)
		{
		?>
		<tr class="text-center has-row-actions">
			<td class="text-center" data-label=""><input type="checkbox" name="del_items[]" value="<?php echo $validation->db_field_validate($userRow['userid']); ?>"/></td>
			<td data-label="Username - ">
				<a href="user_master_form.php?mode=edit&userid=<?php echo $validation->db_field_validate($userRow['userid']); ?>" class="fw-500"><?php echo $validation->db_field_validate($userRow['username']); ?></a>
				
				<div class="row row-actions">
					<div class="col-sm-12">
						<?php if($_SESSION['per_update'] == "1") { ?>
							<a href="user_master_form.php?mode=edit&userid=<?php echo $validation->db_field_validate($userRow['userid']); ?>">Edit</a>
							 | 
						<?php } ?>
						<?php if($_SESSION['per_delete'] == "1") { ?>
							<a href="user_master_actions.php?q=del&userid=<?php echo $validation->db_field_validate($userRow['userid']); ?>" onClick="return del();" class="delete">Delete</a>
						<?php } ?>
					</div>
				</div>
			</td>
			<td data-label="Type - "><?php echo $validation->db_field_validate($userRow['type']); ?></td>
			<td data-label="Display Name - "><?php echo $validation->db_field_validate($userRow['display_name']); ?></td>
			<td data-label="Permissions - ">
				Read: <?php if($userRow['per_read'] == "1") echo "<i class='fa fa-check' style='color:green;'></i>"; else echo "<i class='fa fa-times' style='color:red;'></i>"; ?> &nbsp;<br class="mb-hidden" />
				Write: <?php if($userRow['per_write'] == "1") echo "<i class='fa fa-check' style='color:green;'></i>"; else echo "<i class='fa fa-times' style='color:red;'></i>"; ?> &nbsp;<br class="mb-hidden" />
				Update: <?php if($userRow['per_update'] == "1") echo "<i class='fa fa-check' style='color:green;'></i>"; else echo "<i class='fa fa-times' style='color:red;'></i>"; ?> &nbsp;<br class="mb-hidden" />
				Delete: <?php if($userRow['per_delete'] == "1") echo "<i class='fa fa-check' style='color:green;'></i>"; else echo "<i class='fa fa-times' style='color:red;'></i>"; ?>
			</td>
			<td data-label="Status - "><font color="<?php if($userRow['status'] == "active") { echo "green"; } else { echo "red"; } ?>"><?php echo $validation->db_field_validate(ucfirst($userRow['status'])); ?></font></td>
			<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($userRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$userRow['createdate']} {$userRow['createtime']}"); ?>)</td>
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