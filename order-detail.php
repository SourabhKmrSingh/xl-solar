<?php
include_once("inc_config.php");

if($_SESSION['regid'] == "")
{
	$_SESSION['error_msg_fe'] = "Login to continue!";
	header("Location: {$base_url}login{$suffix}?url={$full_url}");
	exit();
}

$pageid = "order-detail";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if($pageResult['num_rows'] == 0)
{
	header("Location: {$base_url}error{$suffix}");
	exit();
}
$pageRow = $pageResult['result'][0];

$refno = $validation->urlstring_validate($_GET['ref']);
$purchaseResult = $db->view("*", "rb_purchases", "purchaseid", "and refno_custom='{$refno}' and regid='{$regid}' and status='active'", "purchaseid desc");
if($purchaseResult['num_rows'] == 0)
{
	header("Location: {$base_url}orders{$suffix}");
	exit();
}
$purchaseRow = $purchaseResult['result'][0];

$productid = $purchaseRow['productid'];
$productResult = $db->view("title,title_id", "rb_products", "productid", "and productid='{$productid}'");
$productRow = $productResult['result'][0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php if ($pageRow['meta_title'] != "") { ?>
		<title><?php echo $validation->db_field_validate($pageRow['meta_title']); ?></title>
		<meta name="keywords" content="<?php echo $validation->db_field_validate($pageRow['meta_keywords']); ?>" />
		<meta name="description" content="<?php echo $validation->db_field_validate($pageRow['meta_description']); ?>" />
	<?php } else { ?>
		<title><?php echo $validation->db_field_validate($pageRow['title']) . " | ";
				include_once("inc_title.php"); ?></title>
		<meta name="keywords" content="<?php echo $validation->db_field_validate($pageRow['title']); ?>" />
	<?php } ?>
	<?php include_once("inc_files.php"); ?>
</head>
<body>
	<div class="page-wrapper">
	<?php include_once("inc_header.php"); ?>
	<main>
		<div class="page-header text-center" style="background-image: url('<?php echo BASE_URL;?>assets/images/page-header-bg.jpg')">
			<div class="container">
				<h1 class="page-title"><?php echo $validation->db_field_validate($pageRow['title']); ?><!-- <span>Pages</span> --></h1>
    		</div><!-- End .container -->
    	</div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-0">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $validation->db_field_validate($pageRow['title']); ?></li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->
		<section class="blog-details spad pt-4 pb-4">
			<div class="container-fluid ">
				<div class="row">
					<div class="col-lg-12">
						<div class="blog-details-inner">
							
							
							<div class="blog_details">
								<?php echo $validation->db_field_validate($pageRow['description']); ?>
								
								<div class="order-box mb-3">
									<div class="box-row-1 b-white no-border">
										<div class="box-column-1">
											<span class="order-title">Order placed on <?php echo $validation->date_format_custom($purchaseRow['createdate']); ?></span>
											&nbsp;&nbsp;<font color="#ddd">|</font>&nbsp;&nbsp;
											<span class="order-title">Order #<?php echo $validation->db_field_validate($purchaseRow['refno_custom']); ?></span>
										</div>
										<div class="box-column-last">
											<!--<?php if($purchaseRow['tracking_status'] == "delivered") { ?>
												<span class="order-title"><a href="<?php echo BASE_URL.'invoice'.SUFFIX.'?ref='.$purchaseRow['refno_custom']; ?>" target="_blank" class="fs-13 anchor-tag">Invoice</a></span>
											<?php } ?>-->
										</div>
									</div>
								</div>
								
								<div class="order-box mb-3">
									<div class="box-row-2">
										<h2 class="box-title"><?php echo ucfirst($validation->db_field_validate($purchaseRow['tracking_status'])); ?></h2>
										<p class="box-desc">
											<?php
											foreach($tracking_msgs as $key => $value)
											{
												if($purchaseRow['tracking_status'] == $key)
												{
													echo $value;
													break;
												}
											}
											?>
										</p>
										<?php
										$productPurchaseResult = $db->view("*", "rb_purchases", "purchaseid", "and refno_custom='{$refno}' and regid='{$regid}' and status='active'", 'purchaseid desc');
										if($productPurchaseResult['num_rows'] >= 1)
										{
											$total_price = 0;
											foreach($productPurchaseResult['result'] as $productPurchaseRow)
											{
										?>
											<div class="d-block mb-4">
												<div class="box-column-1">
													<?php if($productPurchaseRow['product_imgName'] != "" and file_exists(IMG_THUMB_LOC.$productPurchaseRow['product_imgName'])) { ?>
														<img class="img-responsive" src="<?php echo BASE_URL.IMG_THUMB_LOC.$productPurchaseRow['product_imgName']; ?>" alt="<?php echo $validation->db_field_validate($productPurchaseRow['product_title']); ?>" title="<?php echo $validation->db_field_validate($productPurchaseRow['product_title']); ?>" />
													<?php } else { ?>
														<img src="<?php echo BASE_URL; ?>images/noimage.jpg" class="img-responsive" title="<?php echo $validation->db_field_validate($productPurchaseRow['product_title']); ?>" />
													<?php } ?>
												</div>
												<div class="box-column-2">
													<a href="<?php echo BASE_URL.'products/'.$validation->db_field_validate($productPurchaseRow['product_title_id'])."/".$productPurchaseRow['variantid'].'/'; ?>" target="_blank" class="anchor-tag"><?php echo $validation->db_field_validate($productPurchaseRow['product_title']); ?></a>
													<span><?php echo $validation->db_field_validate($productPurchaseRow['product_variant']); ?></span>
													<span><?php if($productPurchaseRow['product_currency_code'] == 'INR') echo '<i class="fa fa-inr" style="font-size:11.1px;" aria-hidden="true"></i>'; else $validation->db_field_validate($productPurchaseRow['product_currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($productPurchaseRow['product_price'])); ?></span>
													<span>Quantity: <?php echo $validation->db_field_validate($productPurchaseRow['quantity']); ?></span>
													<a href="<?php echo BASE_URL.'products/'.$validation->db_field_validate($productPurchaseRow['product_title_id'])."/".$productPurchaseRow['variantid'].'/'; ?>#review" target="_blank" class="mt-2 anchor-tag">Write a Review</a>
													<a href="<?php echo BASE_URL.'products/'.$validation->db_field_validate($productPurchaseRow['product_title_id'])."/".$productPurchaseRow['variantid'].'/'; ?>" class="anchor-tag">Buy it again</a>
												</div>
											</div>
										<?php
												$total_price += $productPurchaseRow['price'];
												$total_shipping += $productPurchaseRow['shipping'];
												$total_coupon_discount += $productPurchaseRow['coupon_discount'];
												$total_taxamount += $productPurchaseRow['taxamount'];
											}
										}
										?>
									</div>
								</div>
								
								<div class="order-summary-box mb-0">
									<div class="box-column">
										<h2 class="box-title">Shipping Address</h2>
										<p class="box-desc">
											<?php echo $validation->db_field_validate($purchaseRow['shipping_first_name']." ".$purchaseRow['shipping_last_name']); ?>
											<br />
											<?php echo $validation->db_field_validate($purchaseRow['shipping_mobile']); if($purchaseRow['shipping_mobile_alter'] != "") echo ", ".$validation->db_field_validate($purchaseRow['shipping_mobile_alter']); ?>
											<br />
											<?php echo $validation->db_field_validate($purchaseRow['shipping_address']); ?>,
											<br />
											<?php if($purchaseRow['shipping_landmark'] != "") echo $validation->db_field_validate($purchaseRow['shipping_landmark'])."<br />"; ?>
											<?php echo $validation->db_field_validate($purchaseRow['shipping_city']); ?>, <?php echo $validation->db_field_validate($purchaseRow['shipping_state']); ?>, <?php echo $validation->db_field_validate($purchaseRow['shipping_pincode']); ?>
											<br />
											<?php echo $validation->db_field_validate($purchaseRow['shipping_country']); ?>
										</p>
									</div>
									
									<div class="box-column">
										<h2 class="box-title">Billing Address</h2>
										<p class="box-desc">
											<?php echo $validation->db_field_validate($purchaseRow['billing_first_name']." ".$purchaseRow['billing_last_name']); ?>
											<br />
											<?php echo $validation->db_field_validate($purchaseRow['billing_mobile']); if($purchaseRow['billing_mobile_alter'] != "") echo ", ".$validation->db_field_validate($purchaseRow['billing_mobile_alter']); ?>
											<br />
											<?php echo $validation->db_field_validate($purchaseRow['billing_address']); ?>,
											<br />
											<?php if($purchaseRow['billing_landmark'] != "") echo $validation->db_field_validate($purchaseRow['billing_landmark'])."<br />"; ?>
											<?php echo $validation->db_field_validate($purchaseRow['billing_city']); ?>, <?php echo $validation->db_field_validate($purchaseRow['billing_state']); ?>, <?php echo $validation->db_field_validate($purchaseRow['billing_pincode']); ?>
											<br />
											<?php echo $validation->db_field_validate($purchaseRow['billing_country']); ?>
										</p>
									</div>
									
									<div class="box-column">
										<h2 class="box-title">Payment Method</h2>
										<p class="box-desc">
											<?php echo strtoupper($validation->db_field_validate($purchaseRow['payment_mode'])); ?>
										</p>
									</div>
									
									<div class="box-column">
										<h2 class="box-title">Order Summary</h2>
										<p class="box-desc">
											Total: <span class="float-right"><?php if($purchaseRow['product_currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['final_price']+$purchaseRow['coupon_discount_total']-$purchaseRow['shipping_total']); ?></span>
											<br />
											Shipping: <span class="float-right">+ <?php if($purchaseRow['product_currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['shipping_total']); ?></span>
											<br />
											Coupon Discount: <span class="float-right">- <?php if($purchaseRow['product_currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['coupon_discount_total']); ?></span>
											<br />
											<?php if($total_taxamount != "0") { ?>
												Tax: <span class="float-right"><?php if($purchaseRow['product_currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($total_taxamount); ?></span>
												<br />
											<?php } ?>
											<?php //if($purchaseRow['wallet_money'] != "0") { ?>
												Wallet Amount: <span class="float-right">- <?php if($purchaseRow['product_currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['wallet_money']); ?></span>
												<br />
												<!--Net Amount: <span class="float-right"><?php if($purchaseRow['product_currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['final_price']-$purchaseRow['wallet_money']); ?></span>
												<br />-->
											<?php //} ?>
											<span class="order-total">Amount to be Paid: <span class="float-right"><?php if($purchaseRow['product_currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['final_price']-$purchaseRow['wallet_money']); ?></span></span>
										</p>
									</div>
								</div>
								
								<?php if($pageRow['fileName'] != "") { ?>
									<br /><a href="<?php echo BASE_URL.FILE_LOC.$validation->db_field_validate($pageRow['fileName']); ?>" target="_blank" class="main_btn rounded-0 w-25">Download File</a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
</body>
</html>