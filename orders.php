<?php
include_once("inc_config.php");

if($_SESSION['regid'] == "")
{
	$_SESSION['error_msg_fe'] = "Login to continue!";
	header("Location: {$base_url}login{$suffix}?url={$full_url}");
	exit();
}

$pageid = "orders";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if($pageResult['num_rows'] == 0)
{
	header("Location: {$base_url}error{$suffix}");
	exit();
}
$pageRow = $pageResult['result'][0];

$where_query = "";
$where_query .= " and regid='$regid' and status='active'";

$table = "rb_purchases";
$id = "purchaseid";
$orderby = "purchaseid desc";
$groupby = "refno_custom";
$url_parameters = "";

$data = $pagination2->main($table, $url_parameters, $where_query, $id, $orderby, '', $groupby);
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

		<section class="blog-details spad pt-4">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<div class="blog-details-inner">
							
							
							
							<div class="blog_details">
								<?php echo $validation->db_field_validate($pageRow['description']); ?>
								
								<?php
								if($data['num_rows'] >= 1)
								{
									foreach($data['result'] as $purchaseRow)
									{
										$refno_custom = $purchaseRow['refno_custom'];
										
										$productid = $purchaseRow['productid'];
										$productResult = $db->view("title,title_id", "rb_products", "productid", "and productid='{$productid}'");
										$productRow = $productResult['result'][0];
								?>
									<div class="order-box">
										<div class="box-row-1">
											<div class="box-column-1">
												<span class="label">ORDER PLACED</span>
												<br />
												<span><?php echo $validation->date_format_custom($purchaseRow['createdate']); ?></span>
											</div>
											<div class="box-column-2">
												<span class="label">Total</span>
												<br />
												<span><?php if($purchaseRow['product_currency_code'] == 'INR') echo '<i class="fa fa-inr" style="font-size:11.1px;" aria-hidden="true"></i>'; else $validation->db_field_validate($purchaseRow['product_currency_code']); ?> <?php echo $validation->price_format($purchaseRow['final_price']); ?></span>
											</div>
											<div class="box-column-last">
												<span class="label">Order #<?php echo $validation->db_field_validate($purchaseRow['refno_custom']); ?></span>
												<br />
												<span>
													<a href="<?php echo BASE_URL.'order-detail'.SUFFIX.'?ref='.$purchaseRow['refno_custom']; ?>" class="fs-13 anchor-tag">Order Details</a>
													<!--<?php if($purchaseRow['tracking_status'] == "delivered") { ?>
														&nbsp;&nbsp;<font color="#ddd">|</font>&nbsp;&nbsp;
														<a href="<?php echo BASE_URL.'invoice'.SUFFIX.'?ref='.$purchaseRow['refno_custom']; ?>" target="_blank" class="fs-13 anchor-tag">Invoice</a>
													<?php } ?>-->
												</span>
											</div>
										</div>
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
											$productPurchaseResult = $db->view("*", "rb_purchases", "purchaseid", "and refno_custom='{$refno_custom}' and regid='{$regid}' and status='active'", 'purchaseid desc');
											if($productPurchaseResult['num_rows'] >= 1)
											{
												foreach($productPurchaseResult['result'] as $productPurchaseRow)
												{
											?>
												<div class="row mb-4">
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

														<?php if($productPurchaseRow['tracking_status'] =='delivered'){ ?>
															<a href="<?php echo BASE_URL.'products/'.$validation->db_field_validate($productPurchaseRow['product_title_id'])."/".$productPurchaseRow['variantid'].'/'; ?>#review" target="_blank" class="mt-2 anchor-tag">Write a Review</a>
															<a href="<?php echo BASE_URL.'products/'.$validation->db_field_validate($productPurchaseRow['product_title_id'])."/".$productPurchaseRow['variantid'].'/'; ?>" target="_blank" class="anchor-tag">Buy it again</a>
														<?php }?>
													</div>
												</div>
											<?php
												}
											}
											?>
										</div>
									</div>
								<?php
									}
								?>
									<nav class="justify-content-center d-flex">
										<?php echo $data['pagination']; ?>
									</nav>
								<?php
								}
								else
								{
								?>
									<h4 class="text-center mt-5">You have not placed any order yet!</h4>
								<?php
								}
								?>
								
								<?php if($pageRow['fileName'] != "") { ?>
									<br /><a href="<?php echo BASE_URL.FILE_LOC.$validation->db_field_validate($pageRow['fileName']); ?>" target="_blank" class="main_btn rounded-0 w-25">Download File</a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
</main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
</body>
</html>