 <?php
    include_once("inc_config.php");

    $pageid = "our-products";
    $pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
    if ($pageResult['num_rows'] == 0) {
        header("Location: {$base_url}error{$suffix}");
        exit();
    }
    $pageRow = $pageResult['result'][0];

    $title_id = $validation->urlstring_validate($_GET['id']);
    $productResult = $db->view("*", "rb_products", "productid", "and title_id='{$title_id}' and status='active'");
    $productRow = $productResult['result'][0];

    $productid = $productRow['productid'];

    if ($productRow['url'] != "http://www." and $productRow['url'] != "https://www." and $productRow['url'] != "" and $_SESSION['full_url'] != $full_url) {
        if (substr($productRow['url'], 0, 7) == 'http://' || substr($productRow['url'], 0, 8) == 'https://') {
            $page_url = $validation->db_field_validate($productRow['url']);
            $page_url_target = $validation->db_field_validate($productRow['url_target']);
        } else {
            $page_url = BASE_URL . "" . $validation->db_field_validate($productRow['url']);
            $page_url_target = $validation->db_field_validate($productRow['url_target']);
        }

        $_SESSION['full_url'] = $full_url;
        header("Location: {$page_url}");
        exit();
    }
    $_SESSION['full_url'] = "";

    $variantid = $validation->urlstring_validate($_GET['id2']);
    if ($variantid != "") {
        $checkvariantResult = $db->view('*', 'rb_products_variants', 'variantid', "and productid = '$productid' and variantid='$variantid'", 'variantid asc');
        if ($checkvariantResult['num_rows'] >= 1) {
            $checkvariantRow = $checkvariantResult['result'][0];
            $product_variantid = $validation->db_field_validate($checkvariantRow['variantid']);
            $product_variant = $validation->db_field_validate($checkvariantRow['variant']);
            $product_sku = $validation->db_field_validate($checkvariantRow['sku']);
            $product_price = $validation->db_field_validate($checkvariantRow['price']);
            $product_mrp = $validation->db_field_validate($checkvariantRow['mrp']);
            $product_stock_quantity = $validation->db_field_validate($checkvariantRow['stock_quantity']);
        } else {
            $_SESSION['success_msg_fe'] = "No Variant exists!";
            header("Location: {$full_url}");
            exit();
        }
    } else {
        $checkvariantResult = $db->view('*', 'rb_products_variants', 'variantid', "and productid = '$productid'", 'variantid asc');
        $checkvariantRow = $checkvariantResult['result'][0];

        $product_variantid = $validation->db_field_validate($checkvariantRow['variantid']);
        $product_variant = $validation->db_field_validate($checkvariantRow['variant']);
        $product_sku = $validation->db_field_validate($checkvariantRow['sku']);
        $product_price = $validation->db_field_validate($checkvariantRow['price']);
        $product_mrp = $validation->db_field_validate($checkvariantRow['mrp']);
        $product_stock_quantity = $validation->db_field_validate($checkvariantRow['stock_quantity']);
    }

    $categoryid = $productRow['categoryid'];
    $categoryQueryResult = $db->view("title,title_id", "rb_categories", "categoryid", "and categoryid='{$categoryid}'");
    $categoryRow = $categoryQueryResult['result'][0];

    $subcategoryid = $productRow['subcategoryid'];
    $subcategoryQueryResult = $db->view("title,title_id", "rb_subcategories", "subcategoryid", "and subcategoryid='{$subcategoryid}'");
    $subcategoryRow = $subcategoryQueryResult['result'][0];

    $db->unique_visitors('rb_products_views', 'rb_products', 'productid', $productid, $user_ip, $regid);
    $visitorsResult = $db->view("views", "rb_products", "productid", "and title_id='{$title_id}' and status='active'");
    $visitorsRow = $visitorsResult['result'][0];
    $visitorsCount = $visitorsRow['views'];

    $wishlistResult = $db->view('*', 'rb_wishlist', 'wishlistid', "and regid = '$regid' and productid = '$productid' and status = 'active'");

    $cartResult = $db->view('*', 'rb_cart', 'cartid', "and regid = '$regid' and productid = '$productid' and variantid='$product_variantid' and status = 'active'");

    $reviewResult = $db->view('*', 'rb_products_reviews', 'reviewid', "and productid = '$productid' and status = 'active'");
    $reviewCount = $reviewResult['num_rows'];
    $reviewmainRow = $reviewResult['result'][0];

    $userreviewResult = $db->view('reviewid', 'rb_products_reviews', 'reviewid', "and regid = '$regid' and productid = '$productid' and status = 'active'");
    $userreviewCount = $userreviewResult['num_rows'];

    $userpurchaseResult = $db->view('purchaseid', 'rb_purchases', 'purchaseid', "and regid = '$regid' and productid = '$productid' and tracking_status = 'delivered' and status = 'active'");
    $userpurchaseCount = $userpurchaseResult['num_rows'];

    $reviewsumResult = $db->view('SUM(ratings) as reviews_sum', 'rb_products_reviews', 'reviewid', "and productid = '$productid' and status = 'active'");
    $reviewsumRow = $reviewsumResult['result'][0];
    $product_ratings = ceil($reviewsumRow['reviews_sum'] / $reviewCount);

    $final_price = $product_price + $productRow['shipping'];
    $final_total_price = $final_price + $validation->calculate_discounted_price($productRow['tax'], $final_price);

    $pincode = $_SESSION['pincode'];
    $pincodeResult = $db->view('pincodeid,pincode', 'rb_pincodes', 'pincodeid', "and pincode = '$pincode' and status = 'active'");
    $pincodeRow = $pincodeResult['result'][0];

    $_SESSION['csrf_token'] = substr(sha1(rand(1, 99999)), 0, 32);
    $csrf_token = $_SESSION['csrf_token'];
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
                    <li class="breadcrumb-item" aria-current="page"><?php echo $validation->db_field_validate($pageRow['title']); ?></li>
                    <?php if ($categoryRow['title'] != "") { ?>
                         <a href="<?php echo BASE_URL . 'products' . SUFFIX . '?cat=' . $validation->db_field_validate($categoryRow['title_id']); ?>" class="breadcrumb-item"><?php echo $validation->db_field_validate($categoryRow['title']); ?></a>
                     <?php } ?>
                     <?php if ($subcategoryRow['title'] != "") { ?>
                         <a href="<?php echo BASE_URL . 'products' . SUFFIX . '?cat=' . $validation->db_field_validate($categoryRow['title_id']) . '&subcat=' . $validation->db_field_validate($subcategoryRow['title_id']); ?>" class="breadcrumb-item"><?php echo $validation->db_field_validate($subcategoryRow['title']); ?></a>
                     <?php } ?>
                     <span class="breadcrumb-item "><?php echo $validation->getplaintext($productRow['title'], 80); ?></span>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

            <div class="page-content">
                <div class="container-fluid mt-3">
                    <div class="col-12">
                     <?php if ($_SESSION['success_msg_fe'] != "") { ?>
                         <div class="alert alert-success text-center mt-0 mb-4">
                             <?php
                                echo @$_SESSION['success_msg_fe'];
                                @$_SESSION['success_msg_fe'] = "";
                                ?>
                         </div>
                     <?php }
                        if ($_SESSION['error_msg_fe'] != "") { ?>
                         <div class="alert alert-danger text-center mt-0 mb-4">
                             <?php
                                echo @$_SESSION['error_msg_fe'];
                                @$_SESSION['error_msg_fe'] = "";
                                ?>
                         </div>
                     <?php } ?>
                 </div>
                    <div class="product-details-top">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="product-gallery product-gallery-vertical">
                                    <div class="row">
                                        <figure class="product-main-image">
                                             <?php
                                                $product_img = explode(" | ", $productRow['imgName']);
                                                $slr = 0;
                                                if ($product_img[0] != "") {
                                            ?>
                                                <img id="product-zoom" src="<?php echo BASE_URL . IMG_MAIN_LOC . $product_img[0]; ?>" data-zoom-image="<?php echo BASE_URL . IMG_MAIN_LOC . $product_img[0]; ?>" alt="<?php echo $validation->db_field_validate($productRow['title']); ?>">
                                                <?php
                                            }
                                            ?>
                                           
                                        </figure><!-- End .product-main-image -->

                                        <div id="product-zoom-gallery" class="product-image-gallery">
                                            <?php
                                                $slr = 1;
                                                if ($product_img[0] != "") {
                                                    foreach ($product_img as $img) {
                                                ?>
                                                     <a class="product-gallery-item <?php if ($slr == 1) echo "active"; ?>" href="#" data-image="<?php echo BASE_URL . IMG_MAIN_LOC . $img; ?>" data-zoom-image="<?php echo BASE_URL . IMG_MAIN_LOC . $img; ?>">
                                                        <img src="<?php echo BASE_URL . IMG_MAIN_LOC . $img; ?>" alt="product side">
                                                    </a>
                                                 <?php
                                                        $slr++;
                                                    }
                                                } else {
                                            ?>
                                            <!-- <a class="product-gallery-item active" href="#" data-image="<?php echo BASE_URL;?>assets/images/products/single/1.jpg" data-zoom-image="<?php echo BASE_URL;?>assets/images/products/single/1-big.jpg">
                                                <img src="<?php echo BASE_URL;?>assets/images/products/single/1-small.jpg" alt="product side">
                                            </a> -->

                                            <?php
                                                }
                                            ?>
                                        </div><!-- End .product-image-gallery -->
                                    </div><!-- End .row -->
                                </div><!-- End .product-gallery -->
                            </div><!-- End .col-md-6 -->

                            <div class="col-md-6">
                                <div class="product-details">
                                    <h1 class="product-title"><?php echo $validation->db_field_validate($productRow['title']); ?></h1><!-- End .product-title -->

                                    <div class="ratings-container">
                                        <div class="ratings">
                                            <div class="ratings-val" style="width: <?php echo $product_ratings * 20;?>%;"></div><!-- End .ratings-val -->
                                        </div><!-- End .ratings -->
                                        <a class="ratings-text" href="#product-review-link" id="review-link">( <?php echo $reviewCount;?> Reviews )</a>
                                    </div><!-- End .rating-container -->

                                    <div class="product-price">
                                    <?php if ($product_price != "" and $product_price != "0" and $product_price != "0.00") { ?>
                                         <span class="offer_price mr-2"><?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';
                                                                        else $validation->db_field_validate($productRow['currency_code']); ?><?php echo $validation->price_format($product_price); ?></span>
                                     <?php } ?>
                                     <?php if ($product_mrp != "" and $product_mrp != "0" and $product_mrp != "0.00") { ?>
                                          <del class="mrp_price text-muted"><?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';
                                                                else $validation->db_field_validate($productRow['currency_code']); ?><?php echo $validation->price_format($product_mrp); ?></del>
                                         <span style="font-size: 14px; " class='ml-2'> <?php echo $validation->calculate_discount($product_mrp, $product_price); ?> off</span>
                                     <?php } ?>
                                     <span style="font-size: 14px;"><?php if ($productRow['tax_information'] == "included") echo '(Inclusive of all taxes)';if ($productRow['tax_information'] == "excluded") echo '(Exclusive of all taxes)'; ?></span>
                                    </div><!-- End .product-price -->

                                    <div class="product-content">
                                        <p><?php echo $validation->getplaintext($productRow['description'],160); ?></p>
                                    </div><!-- End .product-content -->


                                    <div class="details-filter-row details-row-size">
                                       <?php if ($product_stock_quantity >= 1){ echo "<span class='text-success'>In Stock</span>";}
                                                                else{ echo "<span class='text-danger'>Out of Stock</span>"; ?> <?php if ($product_stock_quantity <= 5 and $product_stock_quantity != 0) echo "<span class='text-warning'>Hurry, Only {$product_stock_quantity} left</span>";}?>    <!-- End .product-details-quantity -->
                                    </div><!-- End .details-filter-row -->
                                    <div class="details-filter-row details-row-size filter-widget">
                                         <?php
                                        $slr = 1;
                                        $variantResult = $db->view('*', 'rb_products_variants', 'variantid', "and productid = '$productid' and variant != ''", 'variantid asc');
                                        if ($variantResult['num_rows'] >= 1) {
                                            foreach ($variantResult['result'] as $variantRow) {
                                        ?>
                                             <div class="sc-item">
                                                 <a href="<?php echo BASE_URL . 'products/' . $title_id . '/' . $variantRow['variantid'] . '/'; ?>">
                                                     <label for="s-size" class="<?php if ($variantid !="") {
                                                                if ($variantid == $variantRow['variantid']) echo 'active';
                                                            } else {
                                                                if ($slr == 1) echo 'active';
                                                            } ?>"><?php echo $validation->db_field_validate($variantRow['variant']); ?><br /><span><?php if ($productRow['currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>'; else $validation->db_field_validate($productRow['currency_code']); ?><?php echo $validation->price_format($variantRow['price']); ?></span></label></a>
                                             </div>
                                     <?php
                                                $slr++;
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="details-filter-row details-row-size">
                                        <ul>
                                             <?php if ($productRow['cod'] == "yes") { ?><li>COD Available</li><?php } ?>
                                             <?php if ($productRow['shipping'] == "0" || $productRow['shipping'] == "0.00") { ?>
                                                 <li>Free Shipping</li>
                                             <?php } ?>
                                             <?php if ($configRow['expected_delivery'] != "") { ?>
                                                 <li><?php echo $validation->db_field_validate($configRow['expected_delivery']); ?></li>
                                             <?php } ?>
                                         </ul>
                                    </div>
                                    <div class="quantity">
                                 <form action="<?php echo BASE_URL; ?>product-detail_inter.php" method="post">
                                     <input type="hidden" name="token" value="<?php echo $csrf_token; ?>" />
                                     <input type="hidden" name="id" value="<?php echo $productid; ?>" />
                                     <input type="hidden" name="redirect_url" value="<?php echo $full_url; ?>" />
                                     <input type="hidden" name="variantid" value="<?php echo $product_variantid; ?>" />
                                     <input type="hidden" name="price" value="<?php echo $product_price; ?>" />
                                     <input type="hidden" name="shipping" value="<?php echo $productRow['shipping']; ?>" />
                                     <input type="hidden" name="tax" value="<?php echo $productRow['tax']; ?>" />
                                     <input type="hidden" name="taxamount" value="<?php echo $validation->calculate_discounted_price($productRow['tax'], $product_price); ?>" />
                                     <input type="hidden" name="final_price" value="<?php echo $final_total_price; ?>" />
                                     <?php if ($product_stock_quantity >= 1) { ?>
                                         <div class="d-flex justify-content-start align-items-center ">
                                             <label for="quantity">Quantity:</label>&nbsp;
                                             <div class="d-inline-block align-middle">
                                                 <select name="quantity" id="quantity" class="form-control ml-2" onChange="get_quantity_product();">
                                                     <?php
                                                        $max_quantity = ($product_stock_quantity >= 9 ? '9' : $product_stock_quantity);
                                                        for ($i = 1; $i <= $max_quantity; $i++) {
                                                        ?>
                                                         <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                     <?php
                                                        }
                                                        if ($product_stock_quantity >= 10) {
                                                        ?>
                                                         <option value="10+" <?php if ("10+" == $quantity) echo "selected"; ?>>10+</option>
                                                     <?php
                                                        }
                                                        ?>
                                                 </select>
                                                 <input type="number" name="quantity_custom" id="quantity_custom" min="1" class="form-control mw-80 mr-1" style="display:none;" />
                                             </div>
                                         </div>
                                     <?php } ?>

                                     <?php if ($pincodeResult['num_rows'] == 0 and $pincode != "") { ?><p class="mt-3 stock-red"><?php echo "Currently Out of Stock for <strong>" . $pincode . "</strong>"; ?></p><?php } ?>
                                     <div class="product-details-action">
                                         <?php if ($product_stock_quantity >= 1  and ($pincode != '' ? $pincodeResult['num_rows'] >= 1 : $productid != "")) { ?>
                                             <?php if ($cartResult['num_rows'] >= 1) { ?>
                                                 <a href="<?php echo BASE_URL . "cart" . SUFFIX; ?>" class="btn-product btn-cart">Go to Cart</a>
                                             <?php } else { ?>
                                                 <button type="submit" name="add_to_cart" class="btn-product btn-cart" value="cart"> <span>Add to Cart</span></button>
                                             <?php } ?>
                                             <button type="submit" name="buy_now" class="btn-product btn-cart ml-2" value="buy"><span>Buy Now </span></button>
                                         <?php } else { ?>
                                             <a href="javascript:void(0);" class="btn-product btn-cart">Coming Soon</a>
                                         <?php } ?>
                                     </div>
                                     <div class="clearfix"></div>
                                 </form>
                             </div>

                                    <div class="product-details-footer">
                                        <div class="product-cat">
                                            <span>Category:</span>
                                             <?php
                                        if ($categoryRow['title'] != "") {
                                            echo $validation->db_field_validate($categoryRow['title']);
                                        }
                                        if ($subcategoryRow['title'] != "") {
                                            echo ", " . $validation->db_field_validate($subcategoryRow['title']);
                                        }
                                        ?>
                                        </div><!-- End .product-cat -->
                                        <div class="social-icons social-icons-sm">
                                            <span class="social-label">Share:</span>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $full_url; ?>&title=<?php echo $validation->db_field_validate($sectionRow['title']); ?>" title="Facebook" target="_blank" class="social-icon" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                                            <a href="http://twitter.com/share?text=<?php echo $validation->db_field_validate($postRow['title']); ?>&url=<?php echo $full_url; ?>" title="Twitter" target="_blank" class="social-icon" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                                            <a  href="http://pinterest.com/pin/create/button/?url=<?php echo $full_url; ?>&media=<?php echo BASE_URL . IMG_MAIN_LOC . $validation->db_field_validate($sectionRow['imgName']); ?>" title="Pinterest" target="_blank" class="social-icon" title="Pinterest" target="_blank"><i class="icon-pinterest"></i></a>
                                        </div>
                                        
                                    </div><!-- End .product-details-footer -->
                                </div><!-- End .product-details -->
                            </div><!-- End .col-md-6 -->
                        </div><!-- End .row -->
                    </div><!-- End .product-details-top -->

                    <div class="product-details-tab">
                        <ul class="nav nav-pills justify-content-center" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab" role="tab" aria-controls="product-desc-tab" aria-selected="true">Description</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="product-info-link" data-toggle="tab" href="#product-info-tab" role="tab" aria-controls="product-info-tab" aria-selected="false">Additional information</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="product-review-link" data-toggle="tab" href="#product-review-tab" role="tab" aria-controls="product-review-tab" aria-selected="false">Reviews (<?php echo $reviewCount; ?>)</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
                                <div class="product-desc-content">
                                    <?php echo $validation->db_field_validate($productRow['description']); ?>
                                </div><!-- End .product-desc-content -->
                            </div><!-- .End .tab-pane -->
                            <div class="tab-pane fade" id="product-info-tab" role="tabpanel" aria-labelledby="product-info-link">
                                <div class="product-desc-content">
                                    <p class='font-weight-bold'>Specification: </p>
                                    <table class='table border mt-2'>
                                         <tr>
                                             <td class="p-catagory p-2">Price</td>
                                             <td class="p-catagory p-2">
                                                 <div class="p-price"><?php if ($productRow['currency_code'] == 'INR') echo '<i class="fa fa-inr" aria-hidden="true"></i>';
                                                                        else $validation->db_field_validate($productRow['currency_code']); ?><?php echo $validation->price_format($product_price); ?></div>
                                             </td>
                                         </tr>
                                         <tr>
                                             <td class="p-catagory p-2">Availability</td>
                                             <td class="p-catagory p-2">
                                                 <div class="p-stock"><?php echo $validation->db_field_validate($product_stock_quantity); ?> in stock</div>
                                             </td>
                                         </tr>
                                         <?php if ($product_sku != "") { ?>
                                             <tr>
                                                 <td class="p-catagory">Sku</td>
                                                 <td>
                                                     <div class="p-code"><?php echo $product_sku; ?></div>
                                                 </td>
                                             </tr>
                                         <?php } ?>
                                     </table>
                                </div><!-- End .product-desc-content -->
                            </div><!-- .End .tab-pane -->
                           
                            <div class="tab-pane fade" id="product-review-tab" role="tabpanel" aria-labelledby="product-review-link">
                                <div class="reviews">
                                    
                                    <?php if ($userreviewCount == 0 and $userpurchaseCount >= 1) { ?>
                                         <div class="leave-comment pt-4">
                                             <h4>Write your Review</h4>
                                             <form action="<?php echo BASE_URL; ?>product_review_inter.php" method="post" class="comment-form">
                                                 <input type="hidden" name="redirect_url" value="<?php echo $full_url; ?>" />
                                                 <input type="hidden" name="productid" value="<?php echo $productid; ?>" />
                                                 <fieldset CLASS="rating mb-2">
                                                     <input CLASS="stars" TYPE="radio" ID="star5" NAME="ratings" VALUE="5" checked="checked" />
                                                     <label class="full" FOR="star5" TITLE="5 stars"></label>
                                                     <input CLASS="stars" TYPE="radio" ID="star4" NAME="ratings" VALUE="4" />
                                                     <label class="full" FOR="star4" TITLE="4 stars"></label>
                                                     <input CLASS="stars" TYPE="radio" ID="star3" NAME="ratings" VALUE="3" />
                                                     <label class="full" FOR="star3" TITLE="3 stars"></label>
                                                     <input CLASS="stars" TYPE="radio" ID="star2" NAME="ratings" VALUE="2" />
                                                     <label class="full" FOR="star2" TITLE="2 stars"></label>
                                                     <input CLASS="stars" TYPE="radio" ID="star1" NAME="ratings" VALUE="1" />
                                                     <label class="full" FOR="star1" TITLE="1 star"></label>
                                                 </fieldset>
                                                 <div class="clearfix"></div>
                                                 <div class="row">
                                                     <div class="col-lg-4">
                                                         <input type="text" name="name" placeholder="Name" class="mb-3" required />
                                                     </div>
                                                     <div class="col-lg-4">
                                                         <input type="email" name="email" placeholder="Email" class="mb-3" required />
                                                     </div>
                                                     <div class="col-lg-8">
                                                         <textarea name="message" placeholder="Message" class="mb-3"></textarea>
                                                         <button type="submit" class="site-btn">Send message</button>
                                                     </div>
                                                 </div>
                                             </form>
                                         </div>
                                     <?php } ?>
                                     <h3>Reviews (<?php echo $reviewCount; ?>)</h3>
                                     <?php
                                            foreach ($reviewResult['result'] as $reviewRow) {
                                                $regid = $reviewRow['regid'];
                                                $registerQueryResult = $db->view("regid,first_name,last_name", "rb_registrations", "regid", "and regid='{$regid}'");
                                                $registerRow = $registerQueryResult['result'][0];
                                            ?>
                                    <div class="review">
                                        <div class="row no-gutters">
                                            <div class="col-auto">
                                                <h4><a href="#"><?php echo $validation->db_field_validate($reviewRow['name']); ?></a></h4>
                                                <div class="ratings-container">
                                                    <div class="ratings">
                                                        <div class="ratings-val" style="width: <?php echo $reviewRow['ratings'] * 20; ?>%;"></div><!-- End .ratings-val -->
                                                    </div><!-- End .ratings -->
                                                </div><!-- End .rating-container -->
                                                <span class="review-date"><?php echo $validation->date_format_custom($reviewRow['createdate']); ?></span>
                                            </div><!-- End .col -->
                                            <div class="col">
                                                <div class="review-content">
                                                    <p><?php echo $validation->db_field_validate($reviewRow['message']); ?></p>
                                                </div><!-- End .review-content -->

                                                
                                            </div><!-- End .col-auto -->
                                        </div><!-- End .row -->
                                    </div><!-- End .review -->
                                     <?php
                                        }
                                    ?>
                                    
                            </div><!-- .End .tab-pane -->
                        </div><!-- End .tab-content -->
                    </div><!-- End .product-details-tab -->
                    <?php
                        if ($subcategoryid != "" and $subcategoryid != "0") {
                            $where_query .= " and subcategoryid = '$subcategoryid'";
                        } else if ($categoryid != "" and $categoryid != "0") {
                            $where_query .= " and categoryid = '$categoryid'";
                        }
                        $relatedproductsResult = $db->view('*', 'rb_products', 'productid', "{$where_query} and productid != '$productid' and status='active'", 'order_custom desc', '20');
                        $slr = 1;
                        if ($relatedproductsResult['num_rows'] >= 1) {
                    ?>

                    <h2 class="title text-center mb-4 mt-4">You May Also Like</h2><!-- End .title text-center -->


                    <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                        data-owl-options='{
                            "nav": false, 
                            "dots": true,
                            "margin": 20,
                            "loop": false,
                            "responsive": {
                                "0": {
                                    "items":1
                                },
                                "480": {
                                    "items":2
                                },
                                "768": {
                                    "items":3
                                },
                                "992": {
                                    "items":4
                                },
                                "1200": {
                                    "items":4,
                                    "nav": true,
                                    "dots": false
                                }
                            }
                        }'>
                        <?php
                                foreach ($relatedproductsResult['result'] as $relatedproductsRow) {
                                    $productid = $relatedproductsRow['productid'];

                                    $categoryid = $relatedproductsRow['categoryid'];
                                    $categoryQueryResult = $db->view("title,title_id", "rb_categories", "categoryid", "and categoryid='{$categoryid}'");
                                    $categoryRow = $categoryQueryResult['result'][0];

                                    $subcategoryid = $relatedproductsRow['subcategoryid'];
                                    $subcategoryQueryResult = $db->view("title,title_id", "rb_subcategories", "subcategoryid", "and subcategoryid='{$subcategoryid}'");
                                    $subcategoryRow = $subcategoryQueryResult['result'][0];

                                    if ($relatedproductsRow['url'] == "#") {
                                        $product_url = "#";
                                        $product_url_target = "";
                                    } else if ($relatedproductsRow['url'] != "http://www." and $relatedproductsRow['url'] != "https://www." and $relatedproductsRow['url'] != "") {
                                        if (substr($relatedproductsRow['url'], 0, 7) == 'http://' || substr($relatedproductsRow['url'], 0, 8) == 'https://') {
                                            $product_url = $validation->db_field_validate($relatedproductsRow['url']);
                                            $product_url_target = $validation->db_field_validate($relatedproductsRow['url_target']);
                                        } else {
                                            $product_url = BASE_URL . "" . $validation->db_field_validate($relatedproductsRow['url']);
                                            $product_url_target = $validation->db_field_validate($relatedproductsRow['url_target']);
                                        }
                                    } else {
                                        $product_url = BASE_URL . 'products/' . $validation->db_field_validate($relatedproductsRow['title_id']) . "/";
                                        $product_url_target = $validation->db_field_validate($relatedproductsRow['url_target']);
                                    }

                                    $relatedproduct_img = explode(" | ", $relatedproductsRow['imgName']);

                                    $wishlistResult = $db->view('*', 'rb_wishlist', 'wishlistid', "and regid = '$regid' and productid = '$productid' and status = 'active'");

                                    $checkvariantResult = $db->view('variantid,stock_quantity', 'rb_products_variants', 'variantid', "and productid = '$productid'", 'variantid asc');
                                    $checkvariantRow = $checkvariantResult['result'][0];
                                    $product_variantid = $validation->db_field_validate($checkvariantRow['variantid']);

                                    $cartResult2 = $db->view('cartid', 'rb_cart', 'cartid', "and regid = '$regid' and productid = '$productid' and variantid='$product_variantid' and status = 'active'");

                                    $pincode2 = $_SESSION['pincode'];
                                    $pincodeResult2 = $db->view('pincodeid,pincode', 'rb_pincodes', 'pincodeid', "and pincode = '$pincode2' and status = 'active'");
                                ?>

                        <div class="product product-7 text-center">
                            <figure class="product-media">
                                <?php if ($relatedproductsRow['sale'] == 1) { ?>
                                    <span class="product-label label-sale">Sale</span>
                               <?php } ?>
                                <a href="product.html">
                                     <?php if ($relatedproduct_img[0] != "" and file_exists(IMG_MAIN_LOC . $relatedproduct_img[0])) { ?>
                                    <img src="<?php echo BASE_URL . IMG_MAIN_LOC . $relatedproduct_img[0]; ?>" alt="<?php echo $validation->db_field_validate($relatedproductsRow['title']); ?>" title="<?php echo $validation->db_field_validate($relatedproductsRow['title']); ?>" class="product-image">
                                     <?php } else { ?>
                                         <img src="<?php echo BASE_URL; ?>images/noimage.jpg" title="<?php echo $validation->db_field_validate($relatedproductsRow['title']); ?>" class="product-image" />
                                     <?php } ?>
                                </a>

                                <div class="product-action-vertical">
                                    <?php if ($wishlistResult['num_rows'] >= 1) { ?>
                                        <a href="<?php echo BASE_URL . 'wishlist_delete.php?id=' . $productid; ?>" title="Remove from wishlist" class="btn-product-icon btn-wishlist btn-expandable"><span>Remove from wishlist</span></a>
                                    <?php } else { ?>
                                        <a href="<?php echo BASE_URL . 'wishlist_inter.php?id=' . $productid . '&q=' . $full_url; ?>" title="Add to wishlist" class="btn-product-icon btn-wishlist btn-expandable"><span>Add to wishlist</span></a>
                                     <?php } ?>
                                    
                                </div><!-- End .product-action-vertical -->
                                <form id="product-list-cart<?php echo $slr; ?>" action="<?php echo BASE_URL; ?>product-detail_inter.php?q=cart" method="post">
                                    <?php if ($checkvariantRow['stock_quantity'] >= 1  and ($pincode != '' ? $pincodeResult2['num_rows'] >= 1 : $productid != "")) { ?>
                                                     <input type="hidden" name="id" value="<?php echo $productid; ?>" />
                                                     <input type="hidden" name="redirect_url" value="<?php echo $full_url; ?>" />
                                                     <input type="hidden" name="price" value="<?php echo $productRow['price']; ?>" />
                                                     <input type="hidden" name="variantid" value="<?php echo $product_variantid; ?>" />
                                                     <input type="hidden" name="quantity" value="1" />
                                <div class="product-action">
                                    <?php if ($cartResult2['num_rows'] >= 1) { ?>
                                        <a href="<?php echo BASE_URL . "cart" . SUFFIX; ?>" class="btn-product btn-cart"><span>Go to cart</span></a>
                                    <?php } else { ?>
                                         <a  href="javascript:void(0);" onClick="cart_add('<?php echo $slr; ?>');" class="btn-product btn-cart"><span>Add to cart</span></a>
                                    <?php } ?>
                                    <?php } else { ?>
                                        <a  href="javascript:void(0);"  class="btn-product btn-cart bg danger"><span>Out of stock <?php if ($pincodeResult2['num_rows'] == 0 and $pincode2 != "") echo "for " . $pincode; ?></span></a>
                                    <?php } ?>
                                </div><!-- End .product-action -->
                                </form>
                            </figure><!-- End .product-media -->

                            <div class="product-body">
                                <div class="product-cat">
                                    <a href="javascript:void(0);"><?php echo $validation->db_field_validate($categoryRow['title']); ?></a>
                                </div><!-- End .product-cat -->
                                <h3 class="product-title"><a href="<?php echo $product_url; ?>"><?php echo $validation->db_field_validate($relatedproductsRow['title']); ?></h3><!-- End .product-title -->
                                <div class="product-price">
                                     <?php if ($relatedproductsRow['price'] != '0') { ?>
                                                 <?php if ($relatedproductsRow['currency_code'] == 'INR') echo '&#8377;';
                                                    else $validation->db_field_validate($relatedproductsRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($relatedproductsRow['price'])); ?>
                                     <?php } ?>
                                     <?php if ($relatedproductsRow['mrp'] != '0') { ?>
                                         <del class='text-muted ml-2'><?php if ($relatedproductsRow['currency_code'] == 'INR') echo '&#8377;';
                                                else $validation->db_field_validate($relatedproductsRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($relatedproductsRow['mrp'])); ?></del>
                                     <?php } ?>
                                </div><!-- End .product-price -->
                                
                            </div><!-- End .product-body -->
                        </div><!-- End .product -->
                        <?php
                            $slr++;
                        }
                        ?>
                    </div><!-- End .owl-carousel -->
                     <?php
        }
        ?>
                </div><!-- End .container -->
            </div><!-- End .page-content -->
        </main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
</body>
</html>