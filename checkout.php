<?php
include_once("inc_config.php");
include_once("ccavenue/config.php");

if($_SESSION['regid'] == "")
{
    $_SESSION['error_msg_fe'] = "Login to continue!";
    header("Location: {$base_url}login{$suffix}?url={$full_url}");
    exit();
}

$pageid = "checkout";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if($pageResult['num_rows'] == 0)
{
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageRow = $pageResult['result'][0];

$registerResult = $db->view("*", "rb_registrations", "regid", "and regid='{$regid}'");
$registerRow = $registerResult['result'][0];

$refno = $_SESSION['cart_refno'];

$cartResult = $db->view('*', 'rb_cart', 'cartid', "and regid = '$regid' and refno = '$refno' and status = 'active'", "cartid desc");

$purchasetempResult = $db->view('*', 'rb_purchases_temp', 'tempid', "and regid = '$regid' and refno = '$refno' and status = 'active'");
if($purchasetempResult['num_rows'] == 0)
{
    header("Location: {$base_url}cart{$suffix}");
    exit();
}
$purchasetempRow = $purchasetempResult['result'][0];

if($purchasetempRow['final_price'] == 0)
{
    header("Location: {$base_url}cart{$suffix}");
    exit();
}

$_SESSION['csrf_token'] = substr(sha1(rand(1, 99999)),0,32);
$csrf_token = $_SESSION['csrf_token'];

$coupon_success_msg_fe = "";
$coupon_error_msg_fe = "";
$coupon_code = "";
$coupon_discount = "";

$_SESSION['coupon_error_msg_fe'] = "";
$_SESSION['coupon_success_msg_fe'] = "";
$_SESSION['coupon_discount'] = "";
$_SESSION['coupon_code'] = "";

$price_detail = explode(",", $purchasetempRow['price_detail']);

$pincode = $_SESSION['pincode'];
$pincodeResult = $db->view('pincodeid,pincode', 'rb_pincodes', 'pincodeid', "and pincode = '$pincode' and status = 'active'");
if($pincodeResult['num_rows'] == 0)
{
    $_SESSION['error_msg_fe'] = "One of your selected product is maybe Out of Stock now!";
    header("Location: {$base_url}cart{$suffix}");
    exit();
}



$totalwalletResult = $db->view('wallet_total,wallet_money', 'mlm_registrations', 'regid', "and membership_id = '{$_SESSION['mlm_membership_id']}' and status='active'");
$totalwalletRow = $totalwalletResult['result'][0];

$wallet_money = $totalwalletRow['wallet_money'];

if($wallet_money > $purchasetempRow['final_price'])
{
    $wallet_money = $purchasetempRow['final_price'];
}
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
            	<div class="checkout">
	                <div class="container-fluid">
            			
            			<form id="checkout_form" action="<?php echo BASE_URL; ?>checkout_inter.php" method="post">
                            <input type="hidden" name="token" value="<?php echo $csrf_token; ?>" />
                            <input type="hidden" name="amount" id="amount" value="<?php echo $validation->db_field_validate($purchasetempRow['final_price']); ?>" />
                            <input type="hidden" name="order_id" value="<?php echo $validation->db_field_validate($purchasetempRow['refno']); ?>"/>
                            <input type="hidden" name="currency" value="INR"/>
                            <input type="hidden" name="language" value="EN"/>
                            <input type="hidden" name="email" value="<?php echo $_SESSION['email']; ?>" />
                            <input type="hidden" name="wallet_money" id="wallet_money" value="0" />
                            <input type="hidden" name="merchant_id" value="<?php echo CCA_MERCHANT_ID; ?>"/>
                            <input type="hidden" name="redirect_url" value="<?php echo BASE_URL; ?>checkout_inter.php"/>
                            <input type="hidden" name="cancel_url" value="<?php echo BASE_URL; ?>page/payment-failed/"/>
                            <input type="hidden" name="cart_refno" value="<?php echo $_SESSION['cart_refno']; ?>"/>
                            <input type="hidden" name="pincode" value="<?php echo $_SESSION['pincode']; ?>"/>
                            <input type="hidden" name="regid" value="<?php echo $_SESSION['regid']; ?>"/>
		                	<div class="row">
		                		<div class="col-lg-9">
		                			     <h2 class="checkout-title">Billing & Shipping Details</h2><!-- End .checkout-title -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="membership_id">Your Referral ID *</label>
                                                <input name="membership_id" id="membership_id" type="text" value="<?php echo $validation->db_field_validate($registerRow['membership_id']); ?>" class="mb-0 form-control" <?php if($registerRow['membership_id'] != "") echo "readonly";?> onBlur="fetch_member('membership_id');" required/>
                                                <p style="display:none;" class="mt-1 mb-0 error_cls text-left"><font color="red">Please enter a valid Membership ID</font></p>
                                                <p style="display:none;" class="mt-1 mb-0 error_cls2 text-left"><font color="red">Please enter your own membership ID</font></p>
                                                <div style="color:green;" class="mt-1 mb-0 success_cls text-left"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sponsor_id">Referral ID</label>
                                                <input name="sponsor_id" class='form-control' id="sponsor_id" type="text" value="<?php echo $validation->db_field_validate($registerRow['sponsor_id']); ?>"  class="mb-0" readonly />
                                            </div>
                                            <div class="col-md-12">
                                                Enter your membership ID (e.g. SL….) here.<br />If you have not filled the membership form yet, <a href="<?php echo BASE_URL.'mlm/register.php'; ?>" target="_blank" class="anchor-tag">click here</a>
                                            </div>
                                        </div>
                                      
		                				<div class="row">
		                					<div class="col-sm-6">
		                						<label>First Name *</label>
		                						<input name="billing_first_name" id="billing_first_name" type="text" value="<?php echo $validation->db_field_validate($registerRow['first_name']);?>" class="form-control" required>
		                					</div><!-- End .col-sm-6 -->

		                					<div class="col-sm-6">
		                						<label>Last Name *</label>
		                						<input name="billing_last_name" id="billing_last_name" type="text" value="<?php echo $validation->db_field_validate($registerRow['last_name']); ?>" class="form-control" required>
		                					</div><!-- End .col-sm-6 -->
		                				</div><!-- End .row -->

	            						
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="billing_mobile">Mobile No. *</label>
                                                <input name="billing_mobile" id="billing_mobile" type="text" value="<?php echo $validation->db_field_validate($registerRow['mobile']); ?>" required class="form-control" />
                                            </div>
                                            <div class="col-md-6">
                                                <label for="billing_mobile_alter">Mobile No. (Alternative)</label>
                                                <input name="billing_mobile_alter" id="billing_mobile_alter" type="text" value="<?php echo $validation->db_field_validate($registerRow['mobile_alter']); ?>" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="billing_address">Address *</label>
                                            <textarea class="form-control mb-2" name="billing_address" id="billing_address" required ><?php echo $validation->db_field_validate($registerRow['address']); ?></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="billing_landmark">Landmark</label>
                                            <input name="billing_landmark" id="billing_landmark" type="text" value="<?php echo $validation->db_field_validate($registerRow['landmark']); ?>" class="form-control" />
                                        </div>
                                       <div class="row">
                                            <div class="col-md-6">
                                                <label for="billing_pincode">Pincode</label>
                                                <input name="billing_pincode" class='form-control' id="billing_pincode" type="text" onBlur="fetch_pincode();" value="<?php echo $validation->db_field_validate($registerRow['pincode']); ?>" class="form-control" required  />
                                                <span class="pincode_success" style="color:green;display:none;"><i class="fa fa-check"></i> Verified!</span>
                                                <span class="pincode_failure" style="color:red;display:none;"><i class="fa fa-times"></i> Please enter a valid Pincode!</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="billing_city">City *</label>
                                                <input name="billing_city" id="billing_city" type="text" value="<?php echo $validation->db_field_validate($registerRow['city']); ?>" class="form-control" required />
                                            </div>
                                       </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                            <label for="billing_state">State *</label>
                                            <input name="billing_state" id="billing_state" type="text" value="<?php echo $validation->db_field_validate($registerRow['state']); ?>" class="form-control" required />
                                        </div>
                                        <div class="col-md-6">
                                            <label for="billing_country">Country *</label>
                                            <input name="billing_country" id="billing_country" type="text" value="<?php echo $validation->db_field_validate($registerRow['country']); ?>" class="form-control" required />
                                        </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="note">Note</label>
                                            <textarea class="form-control mb-2" name="note" id="note"></textarea>
                                        </div>
		                		</div><!-- End .col-lg-9 -->
		                		<aside class="col-lg-3">
		                			<div class="summary">
		                				<h3 class="summary-title">Your Order</h3><!-- End .summary-title -->

		                				<table class="table table-summary">
		                					<thead>
		                						<tr>
		                							<th>Product</th>
		                							<th>Total</th>
		                						</tr>
		                					</thead>

		                					<tbody>
                                                <?php
                                                    if($cartResult['num_rows'] >= 1)
                                                    {
                                                        $total_price = 0;
                                                        $slr = 0;
                                                        foreach($cartResult['result'] as $cartRow)
                                                        {
                                                            $productid = $cartRow['productid'];
                                                            $productResult = $db->view("*", "rb_products", "productid", "and productid='{$productid}'");
                                                            $productRow = $productResult['result'][0];
                                                            
                                                            $variantid = $cartRow['variantid'];
                                                            $variantResult = $db->view('stock_quantity', 'rb_products_variants', 'variantid', "and productid = '$productid' and variantid='$variantid'", 'variantid asc');
                                                            $variantRow = $variantResult['result'][0];
                                                            $product_stock_quantity = $validation->db_field_validate($variantRow['stock_quantity']);
                                                            
                                                            if($cartRow['quantity'] > $product_stock_quantity)
                                                            {
                                                                $fields = array('quantity'=>$product_stock_quantity, 'user_ip'=>$user_ip);
                                                                $fields['modifytime'] = $createtime;
                                                                $fields['modifydate'] = $createdate;
                                                                
                                                                $cartupdateResult = $db->update("rb_cart", $fields, array('regid'=>$regid, 'productid'=>$productid, 'status'=>'active'));
                                                                if(!$cartupdateResult)
                                                                {
                                                                    echo mysqli_error($connect);
                                                                    exit();
                                                                }
                                                                
                                                                $_SESSION['error_msg_fe'] = "One of your selected product is maybe Out of Stock now!";
                                                                header("Location: {$base_url}cart{$suffix}");
                                                                exit();
                                                            }
                                                    ?>
		                						<tr>
		                							<td><?php echo $validation->getplaintext($productRow['title'], 20); ?> x <?php echo $validation->db_field_validate($cartRow['quantity']); ?></td>
		                							<td><?php if($productRow['currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($price_detail[$slr])); ?></td>
		                						</tr>
                                                <?php
                                                        if($productRow['cod'] == "no")
                                                        {
                                                            $cod_available = $productRow['cod'];
                                                        }
                                                        $slr++;
                                                    }
                                                }
                                                ?>

		                						<tr class="summary-subtotal">
		                							<td>Subtotal:</td>
		                							<td><?php if($productRow['currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($purchasetempRow['total_price'])); ?></td>
		                						</tr><!-- End .summary-subtotal -->
                                                <?php if($purchasetempRow['coupon_discount'] != "0") { ?>
    		                						<tr>
    		                							<td>Coupon Discount</td>
    		                							<td><?php if($productRow['currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($purchasetempRow['coupon_discount'])); ?></td>
    		                						</tr>
                                                <?php }?>
                                                <?php if($purchasetempRow['shipping_total'] != "0") { ?>
                                                    <tr>
                                                        <td>Shipping</td>
                                                        <td><?php if($productRow['currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($purchasetempRow['shipping_total'])); ?></td>
                                                    </tr>
                                                <?php } ?>
                                                <?php if($purchasetempRow['taxamount_total'] != "0") { ?>
                                                    <tr>
                                                        <td>Tax</td>
                                                        <td><?php echo $validation->db_field_validate($validation->price_format($purchasetempRow['taxamount_total'])); ?></td>
                                                    </tr>
                                                <?php } ?>
		                						<tr class="summary-total">
		                							<td>Total:</td>
		                							<td><?php if($productRow['currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($productRow['currency_code']); ?>&nbsp;<span class="price_total"><?php echo $validation->price_format($purchasetempRow['final_price']); ?></td>
		                						</tr><!-- End .summary-total -->
                                                <tr>
                                                    <td>Payment</td>
                                                    <td>Cash on Delivery</td>
                                                </tr>
		                					</tbody>
		                				</table><!-- End .table table-summary -->
		                				<button type="submit" name='proceed' id='proceed' class="btn btn-outline-primary-2 btn-order btn-block">Place Order</button>
		                			</div><!-- End .summary -->
		                		</aside><!-- End .col-lg-3 -->
		                	</div><!-- End .row -->
            			</form>
	                </div><!-- End .container -->
                </div><!-- End .checkout -->
            </div><!-- End .page-content -->
        </main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
</body>
<script type="text/javascript">
    function payment_mode_check(input)
{
    if (input == "cod")
    {
        $("#checkout_form").attr('action','<?php echo BASE_URL; ?>checkout_inter.php');
    }
    else if(input == "online")
    {
        $("#checkout_form").attr('action','<?php echo BASE_URL; ?>checkout_pay.php');
    }
}

function wallet(input)
{
    if ($('#wallet_option').is(':checked'))
    {
        $("#wallet_money").val(input);
        var price = parseInt("<?php echo $purchasetempRow['final_price']; ?>",10);
        var total_price = price-input;
        $(".price_total").html(total_price + ".00");
        $("#amount").val(total_price);
        
        if(total_price == '0')
        {
            $(".online_area").hide();
        }
        else
        {
            $(".online_area").show();
        }
    }
    else
    {
        $("#wallet_money").val("0");
        $(".price_total").html("<?php echo $purchasetempRow['final_price']; ?>");
        $("#amount").val("<?php echo $purchasetempRow['final_price']; ?>");
        $(".online_area").show();
    }
}

function fetch_member(field)
{
    $.ajax({
        type: 'post',
        url: '<?php echo BASE_URL; ?>fetch_member.php',
        data:
        {
            membership_id: $("#"+field).val(),
            mobile: <?php echo $_SESSION['mobile']; ?>
        },
        success: function (response)
        {
            if(response.substr(0, 8) == "usernono")
            {
                $(".error_cls").show();
                $("#sponsor_id").val("");
                $("#membership_id").val("");
                $(".success_cls").html('');
            }
            else if(response.substr(0, 6) == "userno")
            {
                $(".error_cls2").show();
                $("#sponsor_id").val("");
                $("#membership_id").val("");
                $(".success_cls").html('');
            }
            else if(response != "")
            {
                $(".success_cls").html('Verified!');
                $("#sponsor_id").val(response);
                $(".error_cls").hide();
                $(".error_cls2").hide();
            }
            else
            {
                $("#sponsor_id").val(response);
                $(".error_cls").hide();
                $(".error_cls2").hide();
                $(".success_cls").html('');
            }
        }
    });
}

function fetch_pincode()
{
    $.ajax({
        type: 'post',
        url: '<?php echo BASE_URL; ?>fetch_pincode.php',
        data:
        {
            pincode: $("#billing_pincode").val()
        },
        success: function (response)
        {
            if(response == "no")
            {
                $(".pincode_failure").show();
                $(".pincode_success").hide();
                $("#proceed").prop('disabled', true);
            }
            else
            {
                var result = $.parseJSON(response);
                $("#billing_city").val(result[0]);
                $("#billing_state").val(result[1]);
                $("#billing_country").val(result[2]);
                $(".pincode_success").show();
                $(".pincode_failure").hide();
                $("#proceed").prop('disabled', false);
            }
        }
    });
}

function fetch_pincode2()
{
    $.ajax({
        type: 'post',
        url: '<?php echo BASE_URL; ?>fetch_pincode.php',
        data:
        {
            pincode: $("#shipping_pincode").val()
        },
        success: function (response)
        {
            if(response == "no")
            {
                $(".pincode_failure2").show();
                $(".pincode_success2").hide();
                $("#proceed").prop('disabled', true);
            }
            else
            {
                var result = $.parseJSON(response);
                $("#shipping_city").val(result[0]);
                $("#shipping_state").val(result[1]);
                $("#shipping_country").val(result[2]);
                $(".pincode_success2").show();
                $(".pincode_failure2").hide();
                $("#proceed").prop('disabled', false);
            }
        }
    });
}
</script>
</html>