<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "direct_member";

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

@$userid = $validation->input_validate($_GET['userid']);
@$status = strtolower($validation->input_validate($_GET['status']));
@$datefrom = $validation->input_validate($_GET['datefrom']);
@$dateto = $validation->input_validate($_GET['dateto']);

$where_query = "";
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
$where_query .= " and sponsor_id='{$_SESSION['mlm_membership_id']}'";

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
	$orderby_final = "regid desc";
}

$param1 = "membership_id";
$param2 = "total_amount";
$param3 = "createdate";
include_once("inc_sorting.php");

$table = "mlm_registrations";
$id = "regid";
$url_parameters = "&userid=$userid&status=$status&datefrom=$datefrom&dateto=$dateto";

$data = $pagination->main($table, $url_parameters, $where_query, $id, $orderby_final);

echo $validation->search_filter_enable();
$currentMembership_id = $_SESSION['mlm_membership_id'];


$legs_data = array();
$leg = 1;
$previousParent = array();

function group_sale_leg_wise($parent)
{
    global $db,$userid, $user_ip, $legs_data ,$leg, $previousParent, $createdate, $createtime;
    $treeResult = $db->view('membership_id,imgName,username,status, sponsor_id', 'mlm_registrations', 'regid', "and sponsor_id='$parent'", 'regid asc');

    if($treeResult['num_rows'] >= 1)
    {
        foreach($treeResult['result'] as $treeRow)
        {
            $membership_id_group = $treeRow['membership_id'];
            
            $plotInventoryResult = $db->view('inventoryid','mlm_plots_inventory','inventoryid', " and sponsor_id = '$membership_id_group' and record_check = '1'");

            if($plotInventoryResult['num_rows'] >= 1){
               
                $legs_data[$leg]['groupsale'] += $plotInventoryResult['num_rows'];
               
            }
            
            $legs_data[$leg]['depth']++;
            group_sale_leg_wise($treeRow['membership_id']);

        }
    }else{
        $leg++;
    }

    
    return $legs_data;
}



$totalDownlineMember = 0;

function getAllDownlines($parent)
{
    global $db, $totalDownlineMember;
    $treeResult = $db->view('membership_id,imgName,username,status', 'mlm_registrations', 'regid', "and sponsor_id='$parent'", 'regid asc');
    // echo "<script>alert('$parent')</script>";

	if($treeResult['num_rows'] >= 1)
	{
		foreach($treeResult['result'] as $treeRow)
		{
			$totalDownlineMember++; 
			getAllDownlines($treeRow['membership_id']);
		}
	}
	return $totalDownlineMember;
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
		<h1 CLASS="page-header">Direct Members</h1>
	</div>
</div>

<form name="form_actions" method="POST" action="direct_member_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="row">
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
</div>

<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="register_view.php?orderby=first_name&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Name</span> <span class="sorting-indicator"></span></a></th>
		<th>Membership ID</th>
		<!-- <th>Email</th> -->
		<!-- <th>Mobile No.</th> -->
		<!-- <th>Members</th>
		<th>Sale</th>
		<th>E-Wallet</th> -->
		<th>Business Volume</th>
		<th>Level Percentage</th>
		<th>Level Income</th>
		<th>Status</th>
		<th class="<?php echo $th_sort3." ".$th_order_cls3; ?>"><a href="register_view.php?orderby=createdate&order=<?php echo $th_order3.''.$url_parameters; ?>"><span>Date</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $registerRow)
		{
			$regid = $registerRow['regid'];

			
			$legs_data = array();
			$leg = 1;
			$previousParent = array();

			$group_sale_data = "";
			$group_sale_data = group_sale_leg_wise($registerRow['membership_id']);
			$biggerLeg = 0;

			$group_sale_total = 0;
			$directSale_total = 0;
			for($i=1; $i <= count($group_sale_data); $i++){

				if(isset($group_sale_data[$i]['groupsale'])){
					$group_sale_total += $group_sale_data[$i]['groupsale'];
				}
				
			}

			$plotInventoryResult = $db->view('inventoryid','mlm_plots_inventory','inventoryid', " and sponsor_id = '{$registerRow['membership_id']}' and record_check = '1'");

			$directSale_total = $plotInventoryResult['num_rows'];

			$totalDownlineMember = 0;
			getAllDownlines($registerRow['membership_id']);

			$directResult = $db->view("regid", "mlm_registrations", "regid", " and sponsor_id = '{$registerRow['membership_id']}'");

			
			$cmembershipId = $registerRow['membership_id'];
			$ActivePurchaseDetailResult = $db->view("*", "rb_purchases", "purchaseid", " and tracking_status ='delivered' and membership_id = '$cmembershipId' and income_type = 'level'", "purchaseid asc", 1);
			$ActivePurchaseDetailRow = $ActivePurchaseDetailResult['result'][0];

			$purchaseide = $ActivePurchaseDetailRow['purchaseid'];
			$levelAmountResult = $db->view("amount, levelid", "mlm_distribution_level", 'distributionId', " and purchaseid = '$purchaseide' and membership_id = '$currentMembership_id'");
			$levelAmountRow = $levelAmountResult['result'][0];



		?>
		<tr class="text-center has-row-actions">
			<td data-label="Name - ">
				<a href="register_form.php?mode=edit&regid=<?php echo $validation->db_field_validate($registerRow['regid']); ?>" class="fw-500"><?php echo $validation->db_field_validate($registerRow['first_name'].' '.$registerRow['last_name']); ?></a>
				
				<!--<div class="row row-actions">
					<div class="col-sm-12">
						<a href="genealogy.php?regid=<?php echo $validation->db_field_validate($registerRow['regid']); ?>">Genealogy</a>
					</div>
				</div>-->
			</td>
			<td data-label="Membership ID - "><?php echo $validation->db_field_validate($registerRow['membership_id']); ?></td>
			<td data-label="Business Volume - "><?php echo $ActivePurchaseDetailRow['business_volume'] != "" ?  $validation->db_field_validate($ActivePurchaseDetailRow['business_volume']) : "-"; ?></td>
			<td data-label="Level Percentage - "><?php echo $levelAmountRow['amount'] != "" ? $validation->db_field_validate(($levelAmountRow['amount'] /$ActivePurchaseDetailRow['business_volume']) * 100) . "%": "-"; ?></td>
			<td data-label="Level Income - "><?php echo $levelAmountRow['amount'] != "" ? $validation->db_field_validate($levelAmountRow['amount']) : "-"; ?></td>
			<!-- <td data-label="Email - "><?php echo $validation->db_field_validate($registerRow['email']); ?></td> -->
			<!-- <td data-label="Mobile No. - "><?php echo $validation->db_field_validate($registerRow['mobile']); ?></td> -->
			<!-- <td data-label="Members - ">
				Total Members: <?php echo $totalDownlineMember; ?>
				<br />
				Direct Members: <?php echo $directResult['num_rows']; ?>
				<br />
				Group Members: <?php echo $totalDownlineMember - $directResult['num_rows']; ?>
			</td>
			<td data-label="Sale - ">
				Total Sale: <?php echo $directSale_total + $group_sale_total; ?>
				<br />
				Direct Sale: <?php echo $directSale_total; ?>
				<br />
				Group Sale: <?php echo $group_sale_total; ?>
			</td>
			<td data-label="E-Wallet - ">
				Wallet Balance: &#8377;<?php echo $validation->price_format($registerRow['wallet_money']); ?>
				<br />
				Total Credit: &#8377;<?php echo $validation->price_format($registerRow['wallet_total']); ?>
				<br />
				Total Debit:&nbsp; &#8377;<?php echo $validation->price_format($registerRow['total_debit']); ?>
			</td> -->
			<td data-label="Status - "><font color="<?php if($registerRow['status'] == "active") { echo "green"; } else { echo "red"; } ?>"><?php echo $validation->db_field_validate(ucfirst($registerRow['status'])); ?></font></td>
			<td class="date" data-label="Date - "><?php echo $validation->date_format_custom($registerRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$registerRow['createdate']} {$registerRow['createtime']}"); ?>)</td>
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