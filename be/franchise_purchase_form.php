<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "franchise";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: purchase_view.php");
	exit();
}

if($mode == "edit")
{
	echo $validation->update_permission();
}
else
{
	echo $validation->write_permission();
}

if($mode == "edit")
{
	$fpurchaseid = $validation->urlstring_validate($_GET['fpurchaseid']);
	$purchaseQueryResult = $db->view('*', 'rb_franchise_purchase', 'fpurchaseid', "and fpurchaseid = '$fpurchaseid'");
	$franchisePurchaseRow = $purchaseQueryResult['result'][0];
	
	if($franchisePurchaseRow['franchise_currency_code'] == 'INR')
	{
		$franchise_currency_code = '&#8377;';
	}
	else
	{
		$franchise_currency_code = $validation->db_field_validate($franchisePurchaseRow['franchise_currency_code']);
	}

	$regid = $franchisePurchaseRow['regid'];
	$registerQueryResult = $db->view("first_name,last_name", "rb_registrations", "regid", "and regid='{$regid}'");
	$registerRow = $registerQueryResult['result'][0];
	
	$franchiseid = $franchisePurchaseRow['franchiseid'];
	$franchiseQueryResult = $db->view("*", "mlm_franchise", "franchiseid", "and franchiseid='{$franchiseid}'");
	$franchiseRow = $franchiseQueryResult['result'][0];
	
	
	$userid = $franchisePurchaseRow['userid'];
	$userQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid}'");
	$userRow = $userQueryResult['result'][0];
	
	$userid_updt = $franchisePurchaseRow['userid_updt'];
	$userupdtQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid_updt}'");
	$userupdtRow = $userupdtQueryResult['result'][0];

	$subtotal = $franchiseRow['total_pins'] * $franchisePurchaseRow['franchisePrice'];
	$tax = ($subtotal * $franchisePurchaseRow['tax']) / 100;
	
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
		<h1 CLASS="page-header"><?php if($mode == "insert") echo "Add New"; else echo "Update"; ?> Order</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "franchise_purchase_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "franchise_purchase_inter.php?mode=$mode&fpurchaseid=$fpurchaseid";
													break;
													
													default : echo "franchise_purchase_inter.php";
												}
												?>" enctype="multipart/form-data">

<input type="hidden" name="old_invoicedate" value="<?php echo $validation->db_field_validate($franchisePurchaseRow['invoicedate']); ?>" />
<input type="hidden" name="membership_id" value="<?php echo $validation->db_field_validate($franchisePurchaseRow['membership_id']); ?>" />
<input type="hidden" name="sponsor_id" value="<?php echo $validation->db_field_validate($franchisePurchaseRow['sponsor_id']); ?>" />
<input type="hidden" name="regid" value="<?php echo $validation->db_field_validate($franchisePurchaseRow['regid']); ?>" />
<input type="hidden" name="refno_custom" value="<?php echo $validation->db_field_validate($franchisePurchaseRow['refno_custom']); ?>" />

<div class="form-rows-custom mt-3">
	<?php if($franchisePurchaseRow['regid'] != "" and $franchisePurchaseRow['regid'] != "0") { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>User</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><a href="register_form.php?mode=edit&regid=<?php echo $franchisePurchaseRow['regid']; ?>" target="_blank"><?php echo $validation->db_field_validate($registerRow['first_name'].' '.$registerRow['last_name']); ?></a></p>
			</div>
		</div>
	<?php } ?>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Order ID</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($franchisePurchaseRow['refno_custom']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Membership ID</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($franchisePurchaseRow['membership_id']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Sponsor ID</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($franchisePurchaseRow['sponsor_id']); ?></p>
		</div>
	</div>
	
	
	
	<h5 class="mb-4">Billing Details</h5>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_first_name">First Name</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_first_name" id="billing_first_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_first_name']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_last_name">Last Name</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_last_name" id="billing_last_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_last_name']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_mobile">Mobile No.</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_mobile" id="billing_mobile" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_mobile']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_mobile_alter">Mobile No. (Alternative)</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_mobile_alter" id="billing_mobile_alter" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_mobile_alter']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_address">Address</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_address" id="billing_address" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_address']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_landmark">Landmark</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_landmark" id="billing_landmark" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_landmark']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_city">City</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_city" id="billing_city" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_city']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_state">State</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_state" id="billing_state" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_state']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_country">Country</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_country" id="billing_country" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_country']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_pincode">Pin Code</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_pincode" id="billing_pincode" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['billing_pincode']); ?>" />
		</div>
	</div>


	<h5 class="mb-4">Order Details</h5>	


	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Subtotal</label>
		</div>
		<div class="col-sm-9">
				
			<p class="text">&#8377; <?php echo $validation->price_format($subtotal); ?></p>
		</div>
	</div>
	
	
	
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Tax</label>
		</div>
		<div class="col-sm-9">
			<p class="text">+ &#8377; <?php echo $validation->price_format($tax);?></p>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Total Price</label>
		</div>
		<div class="col-sm-9">
			<p class="text">&#8377; <?php  echo $validation->price_format($franchisePurchaseRow['totalPrice']);?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Order Status *</label>
		</div>
		<div class="col-sm-9">
		
			<?php if($franchisePurchaseRow['order_status'] == "fulfilled") { ?>
				<p class="text"><font color="green">Fulfilled</font></p>
			<?php } else if($franchisePurchaseRow['order_status'] == "pending") { ?>
				<p class="text"><font color="red">Pending</font></p>
			<?php } else{ ?>
				<p class="text"><font color="red">Cancelled</font></p>
			<?php }?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Status *</label>
		</div>
		<div class="col-sm-9">
			<?php if($validation->db_field_validate($franchisePurchaseRow['status']) != "cancelled"){?>
				<select name="status" id="status" class="form-control" required >
					<option value="paid" <?php if($mode == 'edit') { if($validation->db_field_validate($franchisePurchaseRow['status']) == "paid") echo "selected"; } ?>>Paid</option>
					<option value="unpaid" <?php if($mode == 'edit') { if($validation->db_field_validate($franchisePurchaseRow['status']) == "unpaid") echo "selected"; } ?>>Unpaid</option>
					<option value="cancelled">Cancelled</option>
				</select>
			<?php }else{?>
				<p class="text-danger" ><font color="red">Cancelled</font></p>
			<?php }?>
		</div>
	</div>
	
	<?php if($mode == 'edit') { ?>
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Author (Modified By)</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><a href="purchase_view.php?userid=<?php echo $userid_updt; ?>"><?php echo $validation->db_field_validate($userupdtRow['display_name']); ?></a></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>User's IP Address</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($franchisePurchaseRow['user_ip']); ?></p>
			<input type="hidden" name="user_ip" value="<?php if($mode == 'edit') echo $validation->db_field_validate($franchisePurchaseRow['user_ip']); ?>" />
		</div>
	</div>
	
	<?php if($franchisePurchaseRow['invoicedate'] != "") { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Invoice Date</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><?php echo $validation->date_format_custom($franchisePurchaseRow['invoicedate']); ?></p>
			</div>
		</div>
	<?php } ?>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Modification Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($franchisePurchaseRow['modifydate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($franchisePurchaseRow['modifydate'])." at ".$validation->time_format_custom($franchisePurchaseRow['modifytime']); ?></p>
			<?php } ?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Creation Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($franchisePurchaseRow['createdate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($franchisePurchaseRow['createdate'])." at ".$validation->time_format_custom($franchisePurchaseRow['createtime']); ?></p>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
	
	<div class="row mt-4 mb-4">
		<div class="col-sm-12">
			<?php
			if($mode == "insert")
			{
			?>
				<button type="submit" class="btn btn-default btn-sm mr-2 btn_submit"><i class="fa fa-arrow-circle-right"></i>&nbsp;&nbsp;Add</button>
				<button type="reset" class="btn btn-default btn-sm btn_delete"><i class="fas fa-sync-alt"></i>&nbsp;&nbsp;Reset</button>
			<?php
			}
			elseif($mode == "edit")
			{
			?>
				<?php if($franchisePurchaseRow['status'] != "cancelled" and $franchisePurchaseRow['status'] != "paid") { ?>
					<button type="submit" name="submit" class="btn btn-default btn-sm mr-2 btn_submit"><i class="fas fa-save"></i>&nbsp;&nbsp;Update</button>
				<?php } ?>
				<?php if($_SESSION['per_delete'] == "1") { ?>
					<a HREF="franchise_purchase_actions.php?q=del&refno_custom=<?php echo $franchisePurchaseRow['refno_custom']; ?>" class="btn btn-default btn-sm btn_delete" onClick="return del();"><i class="fas fa-trash"></i>&nbsp;&nbsp;Delete</a>
				<?php } ?>
			<?php
			}
			?>
		</div>
	</div>
</div>
</form>
</div>
</div>
</div>

<div ID="image_model" CLASS="modal">
	<div CLASS="modal-content">
		<div class="row">
			<div class="col-10">
				<div class="image_modal_heading"><i class="fa fa-image" aria-hidden="true"></i> Upload Image</div>
			</div>
			<div class="col-2">
				<div CLASS="image_close_button">&times;</div>
			</div>
		</div>
		<div STYLE="background:; padding:3%;">
			<p align="center">Select/Upload files from your local machine to server.</p>
			<div ID="drop-area"><p CLASS="drop-text" STYLE="margin-top:50px;">
				<p class="image_upper_text" id="image_upper_text"><i class="fas fa-check" aria-hidden="true" style="color: #0BC414;"></i> Your Image has been Uploaded. Upload more pictures!!!</p>
				<img src="images/Loading_icon.gif" class="image_model_loader" style="display:none;" />
				<p class="image_lower_text"><form name="uploadForm" id="uploadForm">
				<input type="file" name="userImage" class="d-none" onChange="uploadimage(this);" id="userImage">
				<label for="userImage" class="file_design"><i class="fa fa-image" aria-hidden="true"></i> Select File</label>&nbsp; or Drag it Here
				</form></p>
			</p></div>
			<br>
			<button TYPE="BUTTON" ID="image_close" CLASS="btn btn-success btn-sm">Done</button>
		</div>
	</div>
</div>

</body>
</html>