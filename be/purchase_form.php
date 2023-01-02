<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "purchase";

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
	$purchaseid = $validation->urlstring_validate($_GET['purchaseid']);
	$purchaseQueryResult = $db->view('*', 'rb_purchases', 'purchaseid', "and purchaseid = '$purchaseid'");
	$purchaseRow = $purchaseQueryResult['result'][0];
	
	if($purchaseRow['product_currency_code'] == 'INR')
	{
		$product_currency_code = '&#8377;';
	}
	else
	{
		$product_currency_code = $validation->db_field_validate($purchaseRow['product_currency_code']);
	}

	$regid = $purchaseRow['regid'];
	$registerQueryResult = $db->view("first_name,last_name", "rb_registrations", "regid", "and regid='{$regid}'");
	$registerRow = $registerQueryResult['result'][0];
	
	$productid = $purchaseRow['productid'];
	$productQueryResult = $db->view("title", "rb_products", "productid", "and productid='{$productid}'");
	$productRow = $productQueryResult['result'][0];
	
	$userid = $purchaseRow['userid'];
	$userQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid}'");
	$userRow = $userQueryResult['result'][0];
	
	$userid_updt = $purchaseRow['userid_updt'];
	$userupdtQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid_updt}'");
	$userupdtRow = $userupdtQueryResult['result'][0];
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
													case "insert" : echo "purchase_form_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "purchase_form_inter.php?mode=$mode&purchaseid=$purchaseid";
													break;
													
													default : echo "purchase_form_inter.php";
												}
												?>" enctype="multipart/form-data">

<input type="hidden" name="old_invoicedate" value="<?php echo $validation->db_field_validate($purchaseRow['invoicedate']); ?>" />
<input type="hidden" name="membership_id" value="<?php echo $validation->db_field_validate($purchaseRow['membership_id']); ?>" />
<input type="hidden" name="sponsor_id" value="<?php echo $validation->db_field_validate($purchaseRow['sponsor_id']); ?>" />
<input type="hidden" name="regid" value="<?php echo $validation->db_field_validate($purchaseRow['regid']); ?>" />
<input type="hidden" name="refno_custom" value="<?php echo $validation->db_field_validate($purchaseRow['refno_custom']); ?>" />
<input type="hidden" name="record_check" value="<?php echo $validation->db_field_validate($purchaseRow['record_check']); ?>" />

<div class="form-rows-custom mt-3">
	<?php if($purchaseRow['regid'] != "" and $purchaseRow['regid'] != "0") { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>User</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><a href="register_form.php?mode=edit&regid=<?php echo $purchaseRow['regid']; ?>" target="_blank"><?php echo $validation->db_field_validate($registerRow['first_name'].' '.$registerRow['last_name']); ?></a></p>
			</div>
		</div>
	<?php } ?>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Order ID</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($purchaseRow['refno_custom']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Membership ID</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($purchaseRow['membership_id']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Sponsor ID</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($purchaseRow['sponsor_id']); ?></p>
		</div>
	</div>
	
	<h5 class="mb-4">Bank Details</h5>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Bank Order ID</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($purchaseRow['order_id']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Tracking ID</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($purchaseRow['tracking_id']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Merchant ID</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($purchaseRow['merchant_id']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Bank Ref. No.</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($purchaseRow['bank_ref_no']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Order Status</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($purchaseRow['order_status']); ?></p>
		</div>
	</div>
	
	
	<h5 class="mb-4">Billing Details</h5>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_first_name">First Name</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_first_name" id="billing_first_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_first_name']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_last_name">Last Name</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_last_name" id="billing_last_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_last_name']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_mobile">Mobile No.</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_mobile" id="billing_mobile" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_mobile']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_mobile_alter">Mobile No. (Alternative)</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_mobile_alter" id="billing_mobile_alter" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_mobile_alter']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_address">Address</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_address" id="billing_address" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_address']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_landmark">Landmark</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_landmark" id="billing_landmark" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_landmark']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_city">City</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_city" id="billing_city" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_city']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_state">State</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_state" id="billing_state" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_state']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_country">Country</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_country" id="billing_country" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_country']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="billing_pincode">Pin Code</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="billing_pincode" id="billing_pincode" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['billing_pincode']); ?>" />
		</div>
	</div>
	
	<h5 class="mb-4">Shipping Details</h5>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_first_name">First Name</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_first_name" id="shipping_first_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_first_name']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_last_name">Last Name</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_last_name" id="shipping_last_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_last_name']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_mobile">Mobile No.</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_mobile" id="shipping_mobile" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_mobile']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_mobile_alter">Mobile No. (Alternative)</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_mobile_alter" id="shipping_mobile_alter" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_mobile_alter']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_address">Address</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_address" id="shipping_address" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_address']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_landmark">Landmark</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_landmark" id="shipping_landmark" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_landmark']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_city">City</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_city" id="shipping_city" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_city']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_state">State</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_state" id="shipping_state" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_state']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_country">Country</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_country" id="shipping_country" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_country']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping_pincode">Pin Code</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="shipping_pincode" id="shipping_pincode" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['shipping_pincode']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="note">Note</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="note" id="note" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['note']); ?>" />
		</div>
	</div>
	
	<br />
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Payment Mode</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo strtoupper($validation->db_field_validate($purchaseRow['payment_mode'])); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-12">
			<table class="table variants" id="variants_table">
			<thead>
				<tr>
					<th>Image</th>
					<th>Product</th>
					<th>Quantity</th>
					<th>Net Amount</th>
					<th>Discount</th>
					<th>Shipping</th>
					<th>Tax Rate</th>
					<th>Tax Type</th>
					<th>Tax Amount</th>
					<th>Total Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$refno_custom = $purchaseRow['refno_custom'];
				$productPurchaseResult = $db->view("*", "rb_purchases", "purchaseid", "and refno_custom='{$refno_custom}' and regid='{$regid}' and status='active'", 'purchaseid desc');
				if($productPurchaseResult['num_rows'] >= 1)
				{
					$total_price = 0;
					foreach($productPurchaseResult['result'] as $productPurchaseRow)
					{
				?>
					<tr>
						<td>
							<?php if($productPurchaseRow['product_imgName'] != "" and file_exists(IMG_THUMB_LOC.$productPurchaseRow['product_imgName'])) { ?>
								<img class="img-responsive" src="<?php echo IMG_THUMB_LOC.$productPurchaseRow['product_imgName']; ?>" alt="<?php echo $validation->db_field_validate($productPurchaseRow['product_title']); ?>" title="<?php echo $validation->db_field_validate($productPurchaseRow['product_title']); ?>" height="60" />
							<?php } else { ?>
								<img src="<?php echo BASE_URL; ?>images/noimage.jpg" class="img-responsive" title="<?php echo $validation->db_field_validate($productPurchaseRow['product_title']); ?>" height="60" />
							<?php } ?>
						</td>
						<td>
							<a href="product_form.php?mode=edit&productid=<?php echo $productPurchaseRow['productid']; ?>" target="_blank"><?php echo $validation->db_field_validate($productPurchaseRow['product_title']); ?></a>
							<br />
							<span><?php echo $validation->db_field_validate($productPurchaseRow['product_variant']); ?></span>
						</td>
						<td><?php echo $validation->db_field_validate($productPurchaseRow['quantity']); ?></td>
						<td><?php echo ($productPurchaseRow['tax_information'] == 'included' ? $product_currency_code."".$validation->price_format($productPurchaseRow['price'] - $validation->calculate_discounted_price($productPurchaseRow['tax'], $productPurchaseRow['price'])) : $product_currency_code."".$validation->price_format($productPurchaseRow['price'])); ?></td>
						<td><?php echo$product_currency_code."".$validation->price_format($productPurchaseRow['coupon_discount']) ?></td>
						<td><?php echo $product_currency_code."".$validation->price_format($productPurchaseRow['shipping']) ?></td>
						<td><?php echo $validation->db_field_validate($productPurchaseRow['tax']); ?>%</td>
						<td><?php echo $validation->db_field_validate($productPurchaseRow['tax_type']); ?></td>
						<td><?php echo ($productPurchaseRow['tax_information'] == 'included' ? $product_currency_code."".$validation->price_format($validation->calculate_discounted_price($productPurchaseRow['tax'], $productPurchaseRow['price']+$productPurchaseRow['shipping']-$productPurchaseRow['coupon_discount'])) : $product_currency_code."".$validation->price_format($productPurchaseRow['taxamount'])); ?></td>
						<td><?php echo $product_currency_code."".$validation->db_field_validate($validation->price_format($productPurchaseRow['price']+$productPurchaseRow['shipping']-$productPurchaseRow['coupon_discount']+$productPurchaseRow['taxamount'])); ?></td>
					</tr>
				<?php
						$total_price += $productPurchaseRow['price'];
						$total_shipping += $productPurchaseRow['shipping'];
						$total_coupon_discount += $productPurchaseRow['coupon_discount'];
						$total_taxamount += $productPurchaseRow['taxamount'];
					}
				}
				?>
			</tbody>
			</table>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Subtotal</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php if($purchaseRow['product_currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['final_price']+$purchaseRow['coupon_discount_total']-$purchaseRow['shipping_total']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Shipping</label>
		</div>
		<div class="col-sm-9">
			<p class="text">+ <?php if($purchaseRow['product_currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['shipping_total']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Coupon Discount</label>
		</div>
		<div class="col-sm-9">
			<p class="text">- <?php if($purchaseRow['product_currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['coupon_discount_total']); if($purchaseRow['coupon_code'] != "") echo " (".$validation->db_field_validate($purchaseRow['coupon_code']).")"; ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Tax</label>
		</div>
		<div class="col-sm-9">
			<p class="text">+ <?php if($purchaseRow['product_currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($total_taxamount); ?></p>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Wallet Amount</label>
		</div>
		<div class="col-sm-9">
			<p class="text">- <?php if($purchaseRow['product_currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['wallet_money']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Total Price</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php if($purchaseRow['product_currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['final_price']-$purchaseRow['wallet_money']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Tracking Status *</label>
		</div>
		<div class="col-sm-9">
			<?php if($purchaseRow['tracking_status'] == "ordered" || $purchaseRow['tracking_status'] == "packed" || $purchaseRow['tracking_status'] == "shipped") { ?>
				<select name="tracking_status" id="tracking_status" class="form-control" required >
					<?php
					foreach($tracking_msgs as $key => $value)
					{
					?>
						<option value="<?php echo $key; ?>" <?php if($mode == 'edit') { if($validation->db_field_validate($purchaseRow['tracking_status']) == $key) echo "selected"; } ?>><?php echo ucwords($key); ?></option>
					<?php
					}
					?>
				</select>
			<?php } else if($purchaseRow['tracking_status'] == "delivered") { ?>
				<p class="text"><font color="green">Delivered</font></p>
			<?php } else { ?>
				<p class="text"><font color="red">Cancelled</font></p>
			<?php } ?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Status *</label>
		</div>
		<div class="col-sm-9">
			<select name="status" id="status" class="form-control" required >
				<option value="active" <?php if($mode == 'edit') { if($validation->db_field_validate($purchaseRow['status']) == "active") echo "selected"; } ?>>Active</option>
				<option value="inactive" <?php if($mode == 'edit') { if($validation->db_field_validate($purchaseRow['status']) == "inactive") echo "selected"; } ?>>Inactive</option>
			</select>
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
			<p class="text"><?php echo $validation->db_field_validate($purchaseRow['user_ip']); ?></p>
			<input type="hidden" name="user_ip" value="<?php if($mode == 'edit') echo $validation->db_field_validate($purchaseRow['user_ip']); ?>" />
		</div>
	</div>
	
	<?php if($purchaseRow['invoicedate'] != "") { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Invoice Date</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><?php echo $validation->date_format_custom($purchaseRow['invoicedate']); ?></p>
			</div>
		</div>
	<?php } ?>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Modification Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($purchaseRow['modifydate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($purchaseRow['modifydate'])." at ".$validation->time_format_custom($purchaseRow['modifytime']); ?></p>
			<?php } ?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Creation Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($purchaseRow['createdate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($purchaseRow['createdate'])." at ".$validation->time_format_custom($purchaseRow['createtime']); ?></p>
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
				<?php if($purchaseRow['tracking_status'] != "cancelled" and $purchaseRow['tracking_status'] != "delivered") { ?>
					<button type="submit" name="submit" class="btn btn-default btn-sm mr-2 btn_submit"><i class="fas fa-save"></i>&nbsp;&nbsp;Update</button>
				<?php } ?>
				<?php if($_SESSION['per_delete'] == "1") { ?>
					<a HREF="purchase_actions.php?q=del&refno_custom=<?php echo $purchaseRow['refno_custom']; ?>" class="btn btn-default btn-sm btn_delete" onClick="return del();"><i class="fas fa-trash"></i>&nbsp;&nbsp;Delete</a>
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