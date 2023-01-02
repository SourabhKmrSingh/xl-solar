<?php
include_once("inc_config.php");

if ($_SESSION['regid'] == "") {
    $_SESSION['error_msg_fe'] = "Login to continue!";
    header("Location: {$base_url}login{$suffix}?url={$full_url}");
    exit();
}

$pageid = "cart";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if ($pageResult['num_rows'] == 0) {
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageRow = $pageResult['result'][0];

$cartResult = $db->view('*', 'rb_cart', 'cartid', "and regid = '$regid' and status = 'active'", "cartid desc");

$_SESSION['csrf_token'] = substr(sha1(rand(1, 99999)), 0, 32);
$csrf_token = $_SESSION['csrf_token'];

$coupon = $validation->input_validate($_GET['coupon']);

if ($coupon != "remove") {
    $coupon_success_msg_fe = $_SESSION['coupon_success_msg_fe'];
    $coupon_error_msg_fe = $_SESSION['coupon_error_msg_fe'];
    $coupon_code = $_SESSION['coupon_code'];
    $coupon_discount = $_SESSION['coupon_discount'];
    if ($coupon_discount == "") {
        $coupon_discount = 0;
    }
} else {
    $coupon_code = "";
    $coupon_discount = "";
    unset($_SESSION['coupon_discount']);
    unset($_SESSION['coupon_code']);
    unset($_SESSION['coupon_error_msg_fe']);
    unset($_SESSION['coupon_success_msg_fe']);
}

$pincode = $_SESSION['pincode'];
$pincodeResult = $db->view('pincodeid,pincode', 'rb_pincodes', 'pincodeid', "and pincode = '$pincode' and status = 'active'");
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

            <div class="page-content">
            	<div class="cart">
	                <div class="container-fluid mt-4">
	                	<div class="row">
	                		<div class="col-lg-9">
	                			<table class="table table-cart table-mobile">
									<thead>
										<tr>
											<th>Product</th>
											<th>Price</th>
											<th>Quantity</th>
											<th>Total</th>
											<th></th>
										</tr>
									</thead>

									<tbody>
                                        <?php
                                    $cartids = array();
                                    $variantids = array();
                                    $productids = array();
                                    $total_price = 0;
                                    $price_array = array();
                                    $shipping_array = array();
                                    $tax_array = array();
                                    $taxamount_array = array();
                                    $taxinformation_array = array();
                                    $taxtype_array = array();
                                    $slr = 1;
                                    foreach ($cartResult['result'] as $cartRow) {
                                        $productid = $cartRow['productid'];
                                        $productResult = $db->view("*", "rb_products", "productid", "and productid='{$productid}'");
                                        $productRow = $productResult['result'][0];

                                        $variantid = $cartRow['variantid'];
                                        $variantResult = $db->view('*', 'rb_products_variants', 'variantid', "and productid = '$productid' and variantid='$variantid'", 'variantid asc');
                                        $variantRow = $variantResult['result'][0];
                                        $product_variantid = $validation->db_field_validate($variantRow['variantid']);
                                        $product_variant = $validation->db_field_validate($variantRow['variant']);
                                        $product_sku = $validation->db_field_validate($variantRow['sku']);
                                        $product_price = $validation->db_field_validate($variantRow['price']);
                                        $product_mrp = $validation->db_field_validate($variantRow['mrp']);
                                        $product_stock_quantity = $validation->db_field_validate($variantRow['stock_quantity']);

                                        if ($productRow['url'] == "#") {
                                            $product_url = "#";
                                            $product_url_target = "";
                                        } else if ($productRow['url'] != "http://www." and $productRow['url'] != "https://www." and $productRow['url'] != "") {
                                            if (substr($productRow['url'], 0, 7) == 'http://' || substr($productRow['url'], 0, 8) == 'https://') {
                                                $product_url = $validation->db_field_validate($productRow['url']);
                                                $product_url_target = $validation->db_field_validate($productRow['url_target']);
                                            } else {
                                                $product_url = BASE_URL . "" . $validation->db_field_validate($productRow['url']);
                                                $product_url_target = $validation->db_field_validate($productRow['url_target']);
                                            }
                                        } else {
                                            $product_url = BASE_URL . 'products/' . $validation->db_field_validate($productRow['title_id']) . "/" . $variantid . "/";
                                            $product_url_target = "_blank";
                                        }

                                        $product_img = explode(" | ", $productRow['imgName']);
                                        array_push($cartids, $cartRow['cartid']);
                                        array_push($variantids, $product_variantid);
                                        array_push($productids, $cartRow['productid']);

                                        if ($product_stock_quantity >= 1 and $pincodeResult['num_rows'] >= 1) {
                                            $quantity = $cartRow['quantity'];
                                            $product_shipping = $productRow['shipping'];
                                        } else {
                                            $quantity = 0;
                                            $product_shipping = 0;
                                        }
                                        $price = $product_price;
                                    ?>
										<tr>
											<td class="product-col">
												<div class="product">
													<figure class="product-media">
														<a href="<?php echo $product_url; ?>" target="<?php echo $product_url_target; ?>">
                                                            <?php if ($product_img[0] != "" and file_exists(IMG_THUMB_LOC . $product_img[0])) { ?>
															<img src="<?php echo BASE_URL . IMG_THUMB_LOC . $product_img[0]; ?>" alt="<?php echo $validation->db_field_validate($productRow['title']); ?>" title="<?php echo $validation->db_field_validate($productRow['title']); ?>">
                                                            <?php } else { ?>
                                                                <img class="cart-img" src="<?php echo BASE_URL; ?>images/noimage.jpg" title="<?php echo $validation->db_field_validate($productRow['title']); ?>" />
                                                            <?php } ?>
														</a>
													</figure>

													<h3 class="product-title">
														<a  href="<?php echo $product_url; ?>" target="<?php echo $product_url_target; ?>" ><?php echo $validation->db_field_validate($productRow['title']); ?><span class="fs-13"><?php if ($product_variant != "") echo "(" . $product_variant . ")"; ?></span></a>
													</h3><!-- End .product-title -->
												</div><!-- End .product -->
											</td>
											<td class="price-col"><?php if ($productRow['currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>';
                                                    else $validation->db_field_validate($productRow['currency_code']); ?> &#8377; <?php echo $validation->db_field_validate($validation->price_format($price)); ?></td>
											<td class="quantity-col">
                                                <div class="cart-product-quantity">
                                                    <div class="product_count d-flex justify-content-center">
                                                    <?php if ($product_stock_quantity >= 1 and $pincodeResult['num_rows'] >= 1) { ?>
                                                        <?php if ($quantity <= 9) { ?>
                                                            <select class="form-control mw-80" name="quantity" id="quantity<?php echo $slr; ?>" onChange="get_quantity(<?php echo $productid; ?>, <?php echo $variantid; ?>, <?php echo $slr; ?>);">
                                                                <?php
                                                                $max_quantity = ($product_stock_quantity >= 9 ? '9' : $product_stock_quantity);
                                                                for ($i = 1; $i <= $max_quantity; $i++) {
                                                                ?>
                                                                    <option value="<?php echo $i; ?>" <?php if ($i == $quantity) echo "selected"; ?>><?php echo $i; ?></option>
                                                                <?php
                                                                }
                                                                if ($product_stock_quantity >= 10) {
                                                                ?>
                                                                    <option value="10+" <?php if ("10+" == $quantity) echo "selected"; ?>>10+</option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        <?php } ?>
                                                        <input type="number" name="quantity_custom" id="quantity_custom<?php echo $slr; ?>" class="form-control mw-80 mr-1" min="1" value="<?php echo $quantity; ?>" style="<?php if ($quantity <= 9) echo "display:none;"; ?>" />
                                                        <button type="submit" id="quantity_custom_btn<?php echo $slr; ?>" class="btn btn-default border" onClick="get_quantitycustom(<?php echo $productid; ?>, <?php echo $variantid; ?>, <?php echo $slr; ?>);" style="<?php if ($quantity <= 9) echo "display:none;"; ?>">Update</button>
                                                    <?php } else { ?>
                                                        0
                                                    <?php } ?>
                                                </div>
                                            </td>
                                                </div><!-- End .cart-product-quantity -->
                                            </td>
											<td class="total-col"><?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';
                                                    else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($price * $quantity)); ?></td>
											<td class="remove-col"><a href="<?php echo BASE_URL . "cart_inter.php?token=$csrf_token&id=$productid&cartid=" . $cartRow['cartid']; ?>" class="btn-remove"><i class="icon-close"></i></a></td>
										</tr>
										<?php
                                        $total_price += $price * $quantity;
                                        $shipping += $product_shipping;
                                        array_push($price_array, $price * $quantity);
                                        array_push($shipping_array, $product_shipping);
                                        array_push($tax_array, $productRow['tax']);
                                        array_push($taxinformation_array, $productRow['tax_information']);
                                        array_push($taxtype_array, $productRow['tax_type']);
                                        if ($productRow['tax'] != "0" and $productRow['tax_information'] == "excluded") {
                                            $tax = $productRow['tax'];
                                        }
                                        array_push($taxamount_array, $validation->calculate_discounted_price($tax, $price * $quantity));
                                        $slr++;
                                    }
                                    $cartids = implode(",", $cartids);
                                    $variantids = implode(",", $variantids);
                                    $productids = implode(",", $productids);
                                    $shipping_total = array_sum($shipping_array);
                                    $shipping_detail = implode(",", $shipping_array);
                                    $taxamount_total = array_sum($taxamount_array);
                                    $taxamount_detail = implode(",", $taxamount_array);
                                    $tax_detail = implode(",", $tax_array);
                                    $taxinformation_detail = implode(",", $taxinformation_array);
                                    $taxtype_detail = implode(",", $taxtype_array);
                                    $price_detail = implode(",", $price_array);

                                    if ($shipping_total != "" and $shipping_total != "0" and $shipping_total != "0.00") {
                                        $shipping_total = $shipping_total;
                                    } else {
                                        $shipping_total = $configRow['cart_shipping'];
                                    }
                                    ?>
									</tbody>
								</table><!-- End .table table-wishlist -->

	                			<div class="cart-bottom">
			            			<div class="cart-discount">
			            				<form action="<?php echo BASE_URL; ?>cart_inter.php?token=<?php echo $csrf_token; ?>" method="post">
                                            <input type="hidden" name="price" value="<?php echo $total_price; ?>" />
                                        
                                        
			            					<div class="input-group">
				        						<input name="coupon_code" class='p-2 pl-4 pr-4' id="coupon_code" placeholder="Coupon Code" autocomplete="off" value="<?php echo $coupon_code; ?>" style="<?php if ($coupon_error_msg_fe != "") echo "border:1px solid red; color:red;";else if ($coupon_success_msg_fe != "")  echo "border:1px solid green; color:green;"; ?>" required >
				        						<div class="input-group-append">
													<?php if ($coupon_code == "") { ?>
                                                        <button type="submit" class="btn btn-primary p-2 pl-4 pr-4">Apply</button>
                                                    <?php } else { ?>
                                                        <a href="<?php echo BASE_URL . 'cart' . SUFFIX . '?coupon=remove'; ?>" class="btn btn-danger p-2 pl-4 pr-4">Remove</a>
                                                    <?php } ?>
                                                    
												</div><!-- .End .input-group-append -->
                                                <?php if ($coupon_error_msg_fe != "") { ?>
                                                        <p class="mt-1">
                                                            <font color="red"><i class='fa fa-times'></i> <?php echo $coupon_error_msg_fe; ?></font>
                                                        </p>
                                                    <?php } else if ($coupon_success_msg_fe != "") { ?>
                                                        <p class="mt-1">
                                                            <font color="green"><i class='fa fa-check'></i> <?php echo $coupon_success_msg_fe; ?></font>
                                                        </p>
                                                    <?php } ?>
			        						</div><!-- End .input-group -->
			            				</form>
			            			</div><!-- End .cart-discount -->

			            			
		            			</div><!-- End .cart-bottom -->
	                		</div><!-- End .col-lg-9 -->
	                		<aside class="col-lg-3">
	                			<div class="summary summary-cart">
	                				<h3 class="summary-title">Cart Total</h3><!-- End .summary-title -->

	                				<table class="table table-summary">
	                					<tbody>
	                						<tr class="summary-subtotal">
	                							<td>Subtotal:</td>
	                							<td><?php if ($productRow['currency_code'] == 'INR') echo ' &#8377;';else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->price_format($total_price); ?></td>
	                						</tr><!-- End .summary-subtotal -->
                                            <?php if ($coupon_discount != "") { ?>
	                						<tr class="summary-shipping">
                                                
	                							<td>Coupon Discount</td>
	                							<td> <span><?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->price_format($coupon_discount); ?></span></td>
	                						</tr>
                                        <?php }?>
                                        <?php if ($coupon_discount != "") { ?>
                                            <tr class="summary-shipping-row">
                                                <td>
                                                    <p class="">Coupon Discount</p>
                                                </td>
                                                <td><?php if ($productRow['currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>';else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->price_format($coupon_discount); ?></td>
                                            </tr>
                                                
                                        <?php } ?>
                                        <?php if ($shipping_total != "") { ?>
                                            <tr class="summary-shipping-row">
                                                <td>
                                                    <p class="">Shipping</p>
                                                </td>
                                                <td><?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->price_format($shipping_total); ?></td>
                                            </tr>
                                                
                                        <?php } ?>
                                        <tr class="summary-total">
                                                <td>Total:</td>
                                                <td><?php
                                                if ($productRow['currency_code'] == 'INR') echo '&#8377;';
                                                else $validation->db_field_validate($productRow['currency_code']);
                                                echo " ";
                                                $final_price = ($total_price + $shipping_total - $coupon_discount) + $taxamount_total;
                                                echo $validation->price_format($final_price);
                                                ?></td>
                                            </tr><!-- End .summary-total -->
                                        <form action="<?php echo BASE_URL; ?>cart_inter.php?token=<?php echo $csrf_token; ?>" method="post">
                                        <input type="hidden" name="cartids" value="<?php echo $cartids; ?>" />
                                        <input type="hidden" name="variantids" value="<?php echo $variantids; ?>" />
                                        <input type="hidden" name="productids" value="<?php echo $productids; ?>" />
                                        <input type="hidden" name="price_detail" value="<?php echo $price_detail; ?>" />
                                        <input type="hidden" name="total_price" value="<?php echo $total_price; ?>" />
                                        <input type="hidden" name="coupon_code" value="<?php echo $coupon_code; ?>" />
                                        <input type="hidden" name="coupon_discount" value="<?php echo $coupon_discount; ?>" />
                                        <input type="hidden" name="shipping_total" value="<?php echo $shipping_total; ?>" />
                                        <input type="hidden" name="shipping_detail" value="<?php echo $shipping_detail; ?>" />
                                        <input type="hidden" name="taxamount_total" value="<?php echo $taxamount_total; ?>" />
                                        <input type="hidden" name="taxamount_detail" value="<?php echo $taxamount_detail; ?>" />
                                        <input type="hidden" name="tax_detail" value="<?php echo $tax_detail; ?>" />
                                        <input type="hidden" name="taxinformation_detail" value="<?php echo $taxinformation_detail; ?>" />
                                        <input type="hidden" name="taxtype_detail" value="<?php echo $taxtype_detail; ?>" />
                                        <input type="hidden" name="final_price" value="<?php echo $final_price; ?>" />

	                					</tbody>
	                				</table><!-- End .table table-summary -->
                                    
	                				<button type="submit" name="checkout" <?php if ($final_price == 0) echo "disabled"; ?> class="btn btn-outline-primary-2 btn-order btn-block">PROCEED TO CHECKOUT</button>
	                			</div><!-- End .summary -->
                                </form>
		            			<a href="<?php echo BASE_URL . 'products' . SUFFIX; ?>" class="btn btn-outline-dark-2 btn-block mb-3"><span>CONTINUE SHOPPING</span><i class="icon-refresh"></i></a>
	                		</aside><!-- End .col-lg-3 -->
	                	</div><!-- End .row -->
	                </div><!-- End .container -->
                </div><!-- End .cart -->
            </div><!-- End .page-content -->
        </main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
</body>
</html>