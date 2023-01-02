<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "enquiry";

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

$where_query = "";
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
	$orderby_final = "enquiryid desc";
}

$param1 = "first_name";
$param2 = "order_custom";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_enquiries";
$id = "enquiryid";
$url_parameters = "&status=$status&datefrom=$datefrom&dateto=$dateto";

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
		<h1 CLASS="page-header">Enquiries <a href="enquiry_form.php?mode=insert" class="btn btn-default btn-sm button">Raise a New Ticket</a></h1>
	</div>
</div>

<form name="form_actions" method="POST" action="enquiry_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="row">
	<div class="col-sm-12 mb-0">
		<div class="form-inline">
			<select NAME="status" CLASS="form-control mb_inline mb-2">
				<option VALUE="" <?php if($status=='') echo "selected"; ?>>Status</option>
				<option VALUE="open" <?php if($status=='open') echo "selected"; ?>>Open</option>
				<option VALUE="in-process" <?php if($status=='in-process') echo "selected"; ?>>In-Process</option>
				<option VALUE="rejected" <?php if($status=='rejected') echo "selected"; ?>>Rejected</option>
				<option VALUE="closed" <?php if($status=='closed') echo "selected"; ?>>Closed</option>
			</select>
			<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
			<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
			<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<a href="enquiry_view.php" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div>
	</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="enquiry_view.php?orderby=first_name&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Name</span> <span class="sorting-indicator"></span></a></th>
		<th>Email</th>
		<th>Mobile No.</th>
		<th>Message</th>
		<th>Status</th>
		<th class="<?php echo $th_sort3." ".$th_order_cls3; ?>"><a href="enquiry_view.php?orderby=createdate&order=<?php echo $th_order3.''.$url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $enquiryRow)
		{
			$userid = $enquiryRow['userid'];
			$userQueryResult = $db->view("display_name", "mlm_users", "userid", "and userid='{$userid}'");
			$userRow = $userQueryResult['result'][0];
		?>
		<tr class="text-center has-row-actions">
			<td data-label="Name - ">
				<a href="enquiry_reply_view.php?enquiryid=<?php echo $validation->db_field_validate($enquiryRow['enquiryid']); ?>" class="fw-500"><?php echo $validation->db_field_validate($enquiryRow['first_name'].' '.$enquiryRow['last_name']); ?></a>
				
				<div class="row row-actions">
					<div class="col-sm-12">
						<!--<a href="enquiry_form.php?mode=edit&enquiryid=<?php echo $validation->db_field_validate($enquiryRow['enquiryid']); ?>">Update</a>
						 | -->
						<a href="enquiry_reply_view.php?enquiryid=<?php echo $validation->db_field_validate($enquiryRow['enquiryid']); ?>">Replies</a>
					</div>
				</div>
			</td>
			<td data-label="Email - "><?php echo $validation->db_field_validate($enquiryRow['email']); ?></td>
			<td data-label="Mobile No. - "><?php echo $validation->db_field_validate($enquiryRow['mobile']); ?></td>
			<td data-label="Message - "><?php echo $validation->db_field_validate($enquiryRow['message']); ?></td>
			<td data-label="Status - "><font class="<?php if($enquiryRow['status'] == "rejected") { echo "status-red"; } else { echo "status-green"; } ?>"><?php echo $validation->db_field_validate(ucwords($enquiryRow['status'])); ?></font></td>
			<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($enquiryRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$enquiryRow['createdate']} {$enquiryRow['createtime']}"); ?>)</td>
		</tr>
		<?php
		}
	}
	else
	{
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