<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "enquiry";

echo $validation->read_permission();

@$enquiryid = $validation->input_validate($_GET['enquiryid']);

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$userid = $validation->input_validate($_GET['userid']);
@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

$where_query = "";
if($enquiryid != "")
{
	$where_query .= " and enquiryid = '$enquiryid'";
}
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
	$orderby_final = "replyid desc";
}

$param1 = "first_name";
$param2 = "order_custom";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_enquiries_replies";
$id = "replyid";
$url_parameters = "&enquiryid=$enquiryid&userid=$userid&status=$status&datefrom=$datefrom&dateto=$dateto";

$data = $pagination->main($table, $url_parameters, $where_query, $id, $orderby_final);

echo $validation->search_filter_enable();

$enquiryQueryResult = $db->view('*', 'mlm_enquiries', 'enquiryid', "and enquiryid = '$enquiryid'");
$enquiryRow = $enquiryQueryResult['result'][0];

$regid = $enquiryRow['regid'];
$registerQueryResult = $db->view("first_name,last_name", "mlm_registrations", "regid", "and regid='{$regid}'");
$registerRow = $registerQueryResult['result'][0];

if($enquiryRow['read_check'] == 0)
{
	$update = $db->update("mlm_enquiries", array('read_check'=>1), array('enquiryid'=>$enquiryid));
}
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
		<h1 CLASS="page-header">Enquiry History <?php if($_SESSION['per_update'] == "1") { ?><a href="enquiry_form.php?mode=edit&enquiryid=<?php echo $validation->db_field_validate($enquiryRow['enquiryid']); ?>" class="btn btn-default btn-sm button">Update Status</a><?php } ?> <?php if($_SESSION['per_write'] == "1") { ?><a href="enquiry_reply_form.php?mode=insert&enquiryid=<?php echo $enquiryid; ?>" class="btn btn-default btn-sm button">Reply</a><?php } ?></h1>
	</div>
</div>

<form name="form_actions" method="POST" action="enquiry_reply_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="row mb-3">
	<div class="col-sm-6">
		Enquiry Raised by: <a href="register_form.php?mode=edit&regid=<?php echo $enquiryRow['regid']; ?>" target="_blank"><?php echo $validation->db_field_validate($registerRow['first_name'].' '.$registerRow['last_name']); ?></a>
	</div>
	<div class="col-sm-6 text-right">
		Current Status: <strong><?php echo $validation->db_field_validate(ucwords($enquiryRow['status'])); ?></strong>
	</div>
	<div class="col-sm-12 text-center">
		Enquiry: <?php echo $validation->db_field_validate($enquiryRow['message']); ?>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 mb-0">
		<div class="form-inline">
			<input type="hidden" name="enquiryid" value="<?php echo $enquiryid; ?>" />
			<select NAME="bulk_actions" CLASS="form-control mb_inline mb-2" >
				<option VALUE="">Bulk Actions</option>
				<option VALUE="delete">Delete</option>
				<option VALUE="active">Status to Active</option>
				<option VALUE="inactive">Status to Inactive</option>
			</select>
			<button type="submit" class="btn btn-default mb_inline btn-sm btn_submit mb-2 mr-4">Apply</button>
			
			<p class="pt-2">From&nbsp;</p> <input type="date" name="datefrom" class="form-control mb_inline mb-2" placeholder="From" value="<?php echo $datefrom; ?>" />
			<p class="pt-2">To&nbsp;</p> <input type="date" name="dateto" class="form-control mb_inline mb-2" placeholder="To" value="<?php echo $dateto; ?>" />
			<input type="submit" value="Filter" class="btn btn-default mb_inline btn-sm btn_submit ml-sm-2 ml-md-0 mb-2 mr-1" />
			<a href="enquiry_reply_view.php?enquiryid=<?php echo $enquiryid; ?>" class="btn btn-default mb_inline btn-sm btn_delete ml-sm-2 ml-md-0 mb-2">Clear</a>
		</div>
	</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="check-row text-center"><input type="checkbox" name="select_all" onClick="selectall(this);" /></th>
		<th>Message</th>
		<th>Posted By</th>
		<th>Status</th>
		<th>Author</th>
		<th class="<?php echo $th_sort3." ".$th_order_cls3; ?>"><a href="enquiry_reply_view.php?orderby=createdate&order=<?php echo $th_order3.''.$url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $replyRow)
		{
			$userid = $replyRow['userid'];
			$userQueryResult = $db->view("display_name", "mlm_users", "userid", "and userid='{$userid}'");
			$userRow = $userQueryResult['result'][0];
		?>
		<tr class="text-center has-row-actions">
			<td class="text-center" data-label=""><input type="checkbox" name="del_items[]" value="<?php echo $validation->db_field_validate($replyRow['replyid']); ?>"/></td>
			<td data-label="Message - ">
				<?php echo $validation->db_field_validate($replyRow['message']); ?>
				
				<div class="row row-actions">
					<div class="col-sm-12">
						<?php if($_SESSION['per_update'] == "1") { ?>
							<a href="enquiry_reply_form.php?mode=edit&replyid=<?php echo $validation->db_field_validate($replyRow['replyid']); ?>">Edit</a>
							 | 
						<?php } ?>
						<?php if($_SESSION['per_delete'] == "1") { ?>
							<a href="enquiry_reply_actions.php?q=del&replyid=<?php echo $validation->db_field_validate($replyRow['replyid']); ?>&enquiryid=<?php echo $enquiryid; ?>" onClick="return del();" class="delete">Delete</a>
						<?php } ?>
					</div>
				</div>
			</td>
			<td data-label="Posted By - "><?php echo $validation->db_field_validate($replyRow['posted_by']); ?></td>
			<td data-label="Status - "><font class="<?php if($replyRow['status'] == "active") { echo "status-green"; } else { echo "status-red"; } ?>"><?php echo $validation->db_field_validate(ucfirst($replyRow['status'])); ?></font></td>
			<td data-label="Author - "><?php echo $validation->db_field_validate($userRow['display_name']); ?></td>
			<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($replyRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$replyRow['createdate']} {$replyRow['createtime']}"); ?>)</td>
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