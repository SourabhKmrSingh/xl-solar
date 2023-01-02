<?php include_once("inc_config.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php include_once("inc_title.php");?></title>
    <meta name="keywords" content="SUNLIEF">
    <meta name="description" content="SUNLIEF - ECOMMERCE AND MLM PORTAL">
    <meta name="author" content="SUNLIEF">
    <!-- Favicon -->
    <!-- <link rel="apple-touch-icon" sizes="180x180" href="assets/images/icons/apple-touch-icon.png"> -->
    <?php include_once("inc_files.php");?>
</head>

<body>
    <div class="page-wrapper">
        <?php include_once("inc_header.php");?>
        <main class="main">
            <?php include_once("inc_slider.php");?>

            

            <div class="mb-3"></div><!-- End .mb-6 -->

            <div class="container">
                <ul class="nav nav-pills nav-big nav-border-anim justify-content-center mb-2 mb-md-3" role="tablist">
                    
                    <li class="nav-item">
                        <a class="nav-link text-dark" >On Sale</a>
                    </li>
                    
                </ul>

                <div class="tab-content tab-content-carousel">
                    <div class="tab-pane p-0 fade show active" id="products-featured-tab" role="tabpanel" aria-labelledby="products-featured-link">
                        <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                            data-owl-options='{
                                "nav": false, 
                                "dots": true,
                                "margin": 20,
                                "loop": false,
                                "responsive": {
                                    "0": {
                                        "items":2
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
                            $productResult = $db->view('*', 'rb_products', 'productid', "and priority='1' and status='active' and sale='1'", 'order_custom desc', '20');
                            if ($productResult['num_rows'] >= 1) {
                                foreach ($productResult['result'] as $productRow) {
                                    $productid = $productRow['productid'];

                                    $categoryid = $productRow['categoryid'];
                                    $categoryQueryResult = $db->view("title,title_id", "rb_categories", "categoryid", "and categoryid='{$categoryid}'");
                                    $categoryRow = $categoryQueryResult['result'][0];

                                    $subcategoryid = $productRow['subcategoryid'];
                                    $subcategoryQueryResult = $db->view("title,title_id", "rb_subcategories", "subcategoryid", "and subcategoryid='{$subcategoryid}'");
                                    $subcategoryRow = $subcategoryQueryResult['result'][0];

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
                                        $product_url = BASE_URL . 'products/' . $validation->db_field_validate($productRow['title_id']) . "/";
                                        $product_url_target = $validation->db_field_validate($productRow['url_target']);
                                    }

                                    $product_img = explode(" | ", $productRow['imgName']);

                                    $wishlistResult = $db->view('wishlistid', 'rb_wishlist', 'wishlistid', "and regid = '$regid' and productid = '$productid' and status = 'active'");

                                    $checkvariantResult = $db->view('variantid,stock_quantity', 'rb_products_variants', 'variantid', "and productid = '$productid'", 'variantid asc');
                                    $checkvariantRow = $checkvariantResult['result'][0];
                                    $product_variantid = $validation->db_field_validate($checkvariantRow['variantid']);

                                    $cartResult = $db->view('cartid', 'rb_cart', 'cartid', "and regid = '$regid' and productid = '$productid' and variantid='$product_variantid' and status = 'active'");

                                    $pincode = $_SESSION['pincode'];
                                    if ($pincode != "") {
                                        $pincodeResult = $db->view('pincodeid,pincode', 'rb_pincodes', 'pincodeid', "and pincode = '$pincode' and status = 'active'");
                                    }
                            ?>
                            <div class="product product-2">
                                <figure class="product-media">
                                   <span class="product-label label-sale">Sale</span>
                                    <a  href="<?php echo $product_url;?>" target="<?php echo $product_url_target;?>" >
                                     <?php if ($product_img[0] != "" and file_exists(IMG_MAIN_LOC . $product_img[0])) { ?>
                                        <img src="<?php echo BASE_URL . IMG_MAIN_LOC . $product_img[0]; ?>" alt="<?php echo $validation->db_field_validate($productRow['title']); ?>" title="<?php echo $validation->db_field_validate($productRow['title']); ?>" class="product-image">
                                       
                                         <?php } else { ?>
                                        <img src="<?php echo BASE_URL; ?>images/noimage.jpg" title="<?php echo $validation->db_field_validate($productRow['title']); ?>" class="mx-auto d-block" />
                                    <?php } ?>
                                    </a>

                                    <div class="product-action-vertical">
                                        <?php if ($wishlistResult['num_rows'] >= 1) { ?>
                                            <a href="<?php echo BASE_URL . 'wishlist_delete.php?id=' . $productid; ?>" class="btn-product-icon btn-wishlist btn-expandable" title="Add to wishlist"><span>Remove from wishlist</span></a>
                                        <?php } else { ?>
                                            <a href="<?php echo BASE_URL . 'wishlist_inter.php?id=' . $productid . '&q=' . $full_url; ?>" class="btn-product-icon btn-wishlist btn-expandable" title="Add to wishlist"><span>add to wishlist</span></a>
                                        <?php } ?>
                                    </div><!-- End .product-action -->
                                <form id="product-list-cart<?php echo $slr; ?>" action="<?php echo BASE_URL; ?>product-detail_inter.php?q=cart" method="post"> 
                                    <div class="product-action ">
                                        
                                            <?php if ($checkvariantRow['stock_quantity'] >= 1 and ($pincode != '' ? $pincodeResult['num_rows'] >= 1 : $productid != "")) { ?>
                                                        <input type="hidden" name="id" value="<?php echo $productid; ?>" />
                                                        <input type="hidden" name="redirect_url" value="<?php echo $full_url; ?>" />
                                                        <input type="hidden" name="price" value="<?php echo $productRow['price']; ?>" />
                                                        <input type="hidden" name="variantid" value="<?php echo $product_variantid; ?>" />
                                                        <input type="hidden" name="quantity" value="1" />
                                                        <?php if ($cartResult['num_rows'] >= 1) { ?>
                                                            <a href="<?php echo BASE_URL . "cart" . SUFFIX; ?>" class="btn-product btn-cart"><span>Go to Cart</span></a>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0);" onClick="cart_add('<?php echo $slr; ?>');" class="btn-product btn-cart"><span>add to cart</span></a>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                         <a href="javascript:void(0);" class="btn-product btn-cart bg-danger">Out of Stock <?php if ($pincodeResult['num_rows'] == 0 and $pincode != "") echo "for " . $pincode; ?></a>
                                                    <?php } ?>
                                        
                                    </div><!-- End .product-action -->
                                </figure><!-- End .product-media -->
                                </form>
                                <div class="product-body">
                                    <div class="product-cat">
                                        <a href="javascript:void(0)"><?php echo $validation->db_field_validate($categoryRow['title']); ?></a>
                                    </div><!-- End .product-cat -->
                                    <h3 class="product-title"><a  href="<?php echo $product_url;?>" target="<?php echo $product_url_target;?>"><?php echo $validation->db_field_validate($productRow['title']); ?></a></h3><!-- End .product-title -->
                                    <div class="product-price">
                                       <?php if ($productRow['price'] != '0') { ?>
                                            <?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';
                                            else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($productRow['price'])); ?>
                                        <?php } ?>
                                        <?php if ($productRow['mrp'] != '0') { ?>
                                            <del class='ml-2'><?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';
                                                    else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($productRow['mrp'])); ?></del>
                                        <?php } ?>
                                    </div><!-- End .product-price -->
                                </div><!-- End .product-body -->
                            </div><!-- End .product -->

                            <?php
                                    $slr++;
                                }
                            }
                            ?>
                            

                        </div><!-- End .owl-carousel -->
                    </div><!-- .End .tab-pane -->
                </div><!-- End .tab-content -->
            </div><!-- End .container -->

            <div class="mb-3 mb-xl-2"></div><!-- End .mb-3 -->

            <?php 
            $homeBannerQueryResult = $db->view("*",'rb_dynamic_records',"recordid"," and pageid='2'");
            if($homeBannerQueryResult['num_rows']>=1){
                $homeBannerRow = $homeBannerQueryResult['result'][0];
            ?>

            <div class="trending">
                <a href="#">
                    <img src="<?php echo BASE_URL . IMG_MAIN_LOC . $homeBannerRow['imgName'];?>" alt="Banner">
                </a>
                <div class="banner banner-big d-md-block">
                    <div class="banner-content text-center">
                        <h4 class="banner-subtitle text-white"><?php echo $homeBannerRow['tagline']?></h4><!-- End .banner-subtitle -->
                        <h3 class="banner-title text-white"><?php echo $homeBannerRow['title']?></h3><!-- End .banner-title -->
                        <p class="d-none d-lg-block text-white"><?php echo $homeBannerRow['description'];?></p> 

                        <a href="<?php echo $homeBannerRow['url']; ?>" target="<?php echo $homeBannerRow['url_target']; ?>"  class="btn btn-primary-white"><span>Shop Now</span><i class="icon-long-arrow-right"></i></a>
                    </div><!-- End .banner-content -->
                </div><!-- End .banner -->
            </div>
           <?php  }?>

            <div class="mb-5"></div><!-- End .mb-5 -->

            <div class="container recent-arrivals">
                <div class="heading heading-flex align-items-center mb-3">
                    <h2 class="title title-lg">Our Products</h2><!-- End .title -->
                    
                </div><!-- End .heading -->

                <div class="tab-content">
                    <div class="tab-pane p-0 fade show active" id="recent-all-tab" role="tabpanel" aria-labelledby="recent-all-link">
                        <div class="products">
                            <div class="row justify-content-center">
                                <?php
                            $productResult = $db->view('*', 'rb_products', 'productid', "and priority='1' and status='active'", 'order_custom desc', '20');
                            if ($productResult['num_rows'] >= 1) {
                                foreach ($productResult['result'] as $productRow) {
                                    $productid = $productRow['productid'];

                                    $categoryid = $productRow['categoryid'];
                                    $categoryQueryResult = $db->view("title,title_id", "rb_categories", "categoryid", "and categoryid='{$categoryid}'");
                                    $categoryRow = $categoryQueryResult['result'][0];

                                    $subcategoryid = $productRow['subcategoryid'];
                                    $subcategoryQueryResult = $db->view("title,title_id", "rb_subcategories", "subcategoryid", "and subcategoryid='{$subcategoryid}'");
                                    $subcategoryRow = $subcategoryQueryResult['result'][0];

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
                                        $product_url = BASE_URL . 'products/' . $validation->db_field_validate($productRow['title_id']) . "/";
                                        $product_url_target = $validation->db_field_validate($productRow['url_target']);
                                    }

                                    $product_img = explode(" | ", $productRow['imgName']);

                                    $wishlistResult = $db->view('wishlistid', 'rb_wishlist', 'wishlistid', "and regid = '$regid' and productid = '$productid' and status = 'active'");

                                    $checkvariantResult = $db->view('variantid,stock_quantity', 'rb_products_variants', 'variantid', "and productid = '$productid'", 'variantid asc');
                                    $checkvariantRow = $checkvariantResult['result'][0];
                                    $product_variantid = $validation->db_field_validate($checkvariantRow['variantid']);

                                    $cartResult = $db->view('cartid', 'rb_cart', 'cartid', "and regid = '$regid' and productid = '$productid' and variantid='$product_variantid' and status = 'active'");

                                    $pincode = $_SESSION['pincode'];
                                    if ($pincode != "") {
                                        $pincodeResult = $db->view('pincodeid,pincode', 'rb_pincodes', 'pincodeid', "and pincode = '$pincode' and status = 'active'");
                                    }
                            ?>

                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="product product-2 text-center">
                                        <figure class="product-media">
                                            <?php if($productRow['sale'] == 1) { ?>
                                                <span class="product-label label-sale">Sale</span>
                                            <?php } ?>
                                            <a href="<?php echo $product_url;?>" target="<?php echo $product_url_target;?>">
                                                <?php if ($product_img[0] != "" and file_exists(IMG_MAIN_LOC . $product_img[0])) { ?>
                                                 <img src="<?php echo BASE_URL . IMG_MAIN_LOC . $product_img[0]; ?>" alt="<?php echo $validation->db_field_validate($productRow['title']); ?>" title="<?php echo $validation->db_field_validate($productRow['title']); ?>" class="product-image">
                                                 <?php } else { ?>
                                                    <img src="<?php echo BASE_URL; ?>images/noimage.jpg" title="<?php echo $validation->db_field_validate($productRow['title']); ?>" class="mx-auto d-block" />
                                                <?php } ?>
                                            </a>

                                            <div class="product-action-vertical">
                                                 <?php if ($wishlistResult['num_rows'] >= 1) { ?>
                                                    <a href="<?php echo BASE_URL . 'wishlist_delete.php?id=' . $productid; ?>" class="btn-product-icon btn-wishlist btn-expandable" title="Add to wishlist"><span>Remove from wishlist</span></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo BASE_URL . 'wishlist_inter.php?id=' . $productid . '&q=' . $full_url; ?>" class="btn-product-icon btn-wishlist btn-expandable" title="Add to wishlist"><span>add to wishlist</span></a>
                                                <?php } ?>
                                            </div><!-- End .product-action-vertical -->
                                        <form id="product-list-cart<?php echo $slr; ?>" action="<?php echo BASE_URL; ?>product-detail_inter.php?q=cart" method="post">
                                            <div class="product-action">
                                                <input type="hidden" name="id" value="<?php echo $productid; ?>" />
                                                        <?php if ($checkvariantRow['stock_quantity'] >= 1 and ($pincode != '' ? $pincodeResult['num_rows'] >= 1 : $productid != "")) { ?>
                                                        <input type="hidden" name="id" value="<?php echo $productid; ?>" />
                                                        <input type="hidden" name="redirect_url" value="<?php echo $full_url; ?>" />
                                                        <input type="hidden" name="price" value="<?php echo $productRow['price']; ?>" />
                                                        <input type="hidden" name="variantid" value="<?php echo $product_variantid; ?>" />
                                                        <input type="hidden" name="quantity" value="1" />
                                                        <?php if ($cartResult['num_rows'] >= 1) { ?>
                                                            <a href="<?php echo BASE_URL . "cart" . SUFFIX; ?>" class="btn-product btn-cart"><span>Go to Cart</span></a>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0);" onClick="cart_add('<?php echo $slr; ?>');" class="btn-product btn-cart"><span>add to cart</span></a>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                         <a href="javascript:void(0);" class="btn-product btn-cart bg-danger">Out of Stock <?php if ($pincodeResult['num_rows'] == 0 and $pincode != "") echo "for " . $pincode; ?></a>
                                                    <?php } ?>
                                            </div><!-- End .product-action -->

                                        </figure><!-- End .product-media -->
                                    </form>
                                        <div class="product-body">
                                            <div class="product-cat">
                                                <a href="javascript:void(0)"><?php echo $validation->db_field_validate($categoryRow['title']); ?></a>
                                            </div><!-- End .product-cat -->
                                            <h3 class="product-title"><a href="<?php echo $product_url;?>" target="<?php echo $product_url_target;?>"><?php echo $validation->db_field_validate($productRow['title']); ?></a></h3><!-- End .product-title -->
                                            <div class="product-price">
                                                 <?php if ($productRow['price'] != '0') { ?>
                                                   <span class='new-price'>
                                                        <?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';
                                                            else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($productRow['price'])); ?>
                                                        <?php } ?>
                                                   </span>
                                                <?php if ($productRow['mrp'] != '0') { ?>
                                                    <span class='old-price'><?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';
                                                            else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($productRow['mrp'])); ?></del>
                                                <?php } ?>
                                            </div><!-- End .product-price -->
                                        </div><!-- End .product-body -->
                                    </div><!-- End .product -->
                                </div><!-- End .col-sm-6 col-md-4 col-lg-3 -->
                                <?php
                                    $slr++;
                                }
                            }
                            ?>
                            </div><!-- End .row -->
                        </div><!-- End .products -->
                    </div><!-- .End .tab-pane -->
                </div>

                <div class="more-container text-center mt-3 mb-3">
                    <a href="<?php echo BASE_URL . "products" . SUFFIX;?>" class="btn btn-outline-dark-3 btn-more"><span>View More</span><i class="icon-long-arrow-right"></i></a>
                </div><!-- End .more-container -->
            </div><!-- End .container -->

            <div class="mb-7"></div><!-- End .mb-5 -->
            
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-sm-6">
                        <div class="icon-box icon-box-card text-center">
                            <span class="icon-box-icon">
                                <i class="icon-rocket"></i>
                            </span>
                            <div class="icon-box-content">
                                <h3 class="icon-box-title">Payment & Delivery</h3><!-- End .icon-box-title -->
                                <p>Fast and easy</p>
                            </div><!-- End .icon-box-content -->
                        </div><!-- End .icon-box -->
                    </div><!-- End .col-lg-4 col-sm-6 -->

                   
                    <div class="col-lg-4 col-sm-6">
                        <div class="icon-box icon-box-card text-center">
                            <span class="icon-box-icon">
                                <i class="icon-life-ring"></i>
                            </span>
                            <div class="icon-box-content">
                                <h3 class="icon-box-title">Quality Support</h3><!-- End .icon-box-title -->
                                <p>Alway online feedback 24/7</p>
                            </div><!-- End .icon-box-content -->
                        </div><!-- End .icon-box -->
                    </div><!-- End .col-lg-4 col-sm-6 -->
                </div><!-- End .row -->
            </div><!-- End .container -->
  <?php


$pageResult = $db->view('*', 'rb_dynamic_pages', 'pageid', "and pageid=3 and status='active'", '', '1');
if ($pageResult['num_rows'] == 0) {
    header("Location: {$base_url}error{$suffix}");
    exit();
}

$pageRow = $pageResult['result'][2];
if ($pageRow['url'] != "http://www." and $pageRow['url'] != "https://www." and $pageRow['url'] != "" and $_SESSION['full_url'] != $full_url) {
    if (substr($pageRow['url'], 0, 7) == 'http://' || substr($pageRow['url'], 0, 8) == 'https://') {
        $page_url = $validation->db_field_validate($pageRow['url']);
        $page_url_target = $validation->db_field_validate($pageRow['url_target']);
    } else {
        $page_url = BASE_URL . "" . $validation->db_field_validate($pageRow['url']);
        $page_url_target = $validation->db_field_validate($pageRow['url_target']);
    }

    $_SESSION['full_url'] = $full_url;
    header("Location: {$page_url}");
    exit();
}
$_SESSION['full_url'] = "";

$pageid = 3;
$where_query = "";
if ($pageid != "") {
    $where_query .= " and pageid = '$pageid'";
}
$where_query .= " and status='active'";

$table = "rb_dynamic_records";
$id = "recordid";
$orderby = "order_custom desc";
//$url_parameters = "&id=$title_id";
$url = BASE_URL . "section/{$title_id}/20/";

$data = $pagination3->main($table, "*", $where_query, $id, $orderby, $url);

include_once("inc_files.php");
?>              
            <div class="container instagram">
                <div class="heading text-center">
                    <h2 class="title title-lg">Follow Us On Instagram</h2><!-- End .title -->
                    <p class="title-desc">Wanna share your style with us?</p><!-- End .title-desc -->
                </div><!-- End .heading -->
            </div><!-- End .container -->

            <div class="owl-carousel owl-simple" data-toggle="owl" 
                data-owl-options='{
                    "nav": false, 
                    "dots": false,
                    "items": 6,
                    "margin": 0,
                    "loop": false,
                    "responsive": {
                        "0": {
                            "items":1
                        },
                        "360": {
                            "items":2
                        },
                        "600": {
                            "items":3
                        },
                        "992": {
                            "items":4
                        },
                        "1200": {
                            "items":5
                        },
                        "1500": {
                            "items":6
                        }
                    }
                }'>
                 <?php
    if ($data['num_rows'] >= 1) {
        foreach ($data['result'] as $sectionRow) {
            $gallery = $sectionRow['imgName'];
            $Single_image = explode(" | ", $gallery);
            foreach ($Single_image as $img) {

    ?>
                <div class="instagram-feed">
                    <img src="<?php echo IMG_MAIN_LOC;echo $validation->db_field_validate($img); ?>" alt="img">
                </div><!-- End .instagram-feed -->

            <?php }
        }
    }
    ?>    
            </div><!-- End .owl-carousel -->
        </main><!-- End .main -->
        <?php include_once("inc_footer.php");?>
        
    </div><!-- End .page-wrapper -->
    <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
    <?php include_once("inc_files_bottom.php");?>
</body>
</html>