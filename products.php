<?php
include_once("inc_config.php");

$page_name = "products";
$pageid = "our-products";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if($pageResult['num_rows'] == 0)
{
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageRow = $pageResult['result'][0];


@$q = strtolower($validation->urlstring_validate($_GET['q']));
@$cat = $validation->urlstring_validate($_GET['cat']);
@$subcat = $validation->urlstring_validate($_GET['subcat']);
@$min = $validation->urlstring_validate($_GET['min']);
@$max = $validation->urlstring_validate($_GET['max']);
@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);
@$pagesize = $validation->input_validate($_GET['pagesize']);

$where_query = "";
$where_query_price = "";
if($q != "")
{
    $keys = explode(" ",$q);
    $where_query .= " and (LOWER(title) LIKE '%$q%'";
    $where_query_price .= " and (LOWER(title) LIKE '%$q%'";
    foreach($keys as $k)
    {
        $where_query .= " OR LOWER(title) LIKE '%$k%'";
        $where_query_price .= " OR LOWER(title) LIKE '%$k%'";
    }
    $where_query .= ")";
    $where_query_price .= ")";
}
if($cat != "")
{
    $where_query .= " and categoryid IN (select categoryid from rb_categories where title_id='$cat')";
    $where_query_price .= " and categoryid IN (select categoryid from rb_categories where title_id='$cat')";
}
if($subcat != "")
{
    $where_query .= " and subcategoryid IN (select subcategoryid from rb_subcategories where title_id='$subcat')";
    $where_query_price .= " and subcategoryid IN (select subcategoryid from rb_subcategories where title_id='$subcat')";
}
if($min != "" and $max != "")
{
    $where_query .= " and price between '$min' and '$max'";
}
$where_query .= " and status='active'";
$where_query_price .= " and status='active'";

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
    $orderby_final = "order_custom desc";
}

$table = "rb_products";
$id = "productid";
$url_parameters = "&q=$q&cat=$cat&subcat=$subcat&min=$min&max=$max&orderby=$orderby&order=$order&pagesize=$pagesize";
$url_parameters_order = "&q=$q&cat=$cat&subcat=$subcat&min=$min&max=$max&pagesize=$pagesize";
$url_parameters_price = "&q=$q&cat=$cat&subcat=$subcat&orderby=$orderby&order=$order&pagesize=$pagesize";
$url_parameters_pagesize = "&q=$q&cat=$cat&subcat=$subcat&min=$min&max=$max&orderby=$orderby&order=$order";

$data = $pagination2->main($table, $url_parameters, $where_query, $id, $orderby_final);

if($cat != "")
{
    $maincategoryQueryResult = $db->view("title,title_id,meta_title,meta_keywords,meta_description,description", "rb_categories", "categoryid", "and title_id='{$cat}'");
    $maincategoryRow = $maincategoryQueryResult['result'][0];
}
if($subcat != "")
{
    $mainsubcategoryQueryResult = $db->view("title,title_id,meta_title,meta_keywords,meta_description,description", "rb_subcategories", "subcategoryid", "and title_id='{$subcat}'");
    $mainsubcategoryRow = $mainsubcategoryQueryResult['result'][0];
}

if($mainsubcategoryRow['title'] != "")
{
    $page_title = $validation->db_field_validate($mainsubcategoryRow['title']);
    $page_description = $validation->db_field_validate($mainsubcategoryRow['description']);
    $page_tagline = $validation->db_field_validate($mainsubcategoryRow['tagline']);
}
else if($maincategoryRow['title'] != "")
{
    $page_title = $validation->db_field_validate($maincategoryRow['title']);
    $page_description = $validation->db_field_validate($maincategoryRow['description']);
    $page_tagline = $validation->db_field_validate($maincategoryRow['tagline']);
}
else
{
    $page_title = $validation->db_field_validate($pageRow['title']);
    $page_description = $validation->db_field_validate($pageRow['description']);
    $page_tagline = $validation->db_field_validate($pageRow['tagline']);
}

$maxpriceResult = $db->view("MAX(price) as max_price", $table, $id, $where_query_price, $orderby_final);
$maxpriceRow = $maxpriceResult['result'][0];
$max_price = $maxpriceRow['max_price'];

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
                <div class="container-fluid mt-3">
                	<div class="row">
                		<div class="col-lg-9">
                			<div class="toolbox">
                				<div class="toolbox-left">
                					<div class="toolbox-info">
                						<?php echo $data['content'];?>
                					</div><!-- End .toolbox-info -->
                				</div><!-- End .toolbox-left -->

                				<div class="toolbox-right">
                					<div class="toolbox-sort">
                						<label for="sortby">Display Product:</label>
                						<div class="select-custom">
											<select name="sortby" onChange="gotoURL(this.value);" id="sortby" class="form-control">
												<option value="<?php echo BASE_URL.''.$page_name.''.SUFFIX."?pagesize=24".$url_parameters_pagesize; ?>" <?php if($pagesize == "24") echo "selected"; ?>>24 per page</option>
												<option value="<?php echo BASE_URL.''.$page_name.''.SUFFIX."?pagesize=36".$url_parameters_pagesize; ?>" <?php if($pagesize == "36") echo "selected"; ?>>36 per page</option>
												<option value="<?php echo BASE_URL.''.$page_name.''.SUFFIX."?pagesize=48".$url_parameters_pagesize; ?>" <?php if($pagesize == "48") echo "selected"; ?>>48 per page</option>
											</select>
										</div>
                					</div><!-- End .toolbox-sort -->
                				</div><!-- End .toolbox-right -->
                			</div><!-- End .toolbox -->

                            <div class="products mb-3">
                                <div class="row ">

                                    <?php
                                    $slr = 1;
                                    if($data['num_rows'] >= 1)
                                    {
                                        foreach($data['result'] as $productRow)
                                        {
                                            $productid = $productRow['productid'];
                                            
                                            $categoryid = $productRow['categoryid'];
                                            $categoryQueryResult = $db->view("title,title_id", "rb_categories", "categoryid", "and categoryid='{$categoryid}'");
                                            $categoryRow = $categoryQueryResult['result'][0];
                                            
                                            $subcategoryid = $productRow['subcategoryid'];
                                            $subcategoryQueryResult = $db->view("title,title_id", "rb_subcategories", "subcategoryid", "and subcategoryid='{$subcategoryid}'");
                                            $subcategoryRow = $subcategoryQueryResult['result'][0];
                                            
                                            if($productRow['url'] == "#")
                                            {
                                                $product_url = "#";
                                                $product_url_target = "";
                                            }
                                            else if($productRow['url'] != "http://www." and $productRow['url'] != "https://www." and $productRow['url'] != "")
                                            {
                                                if(substr($productRow['url'], 0, 7) == 'http://' || substr($productRow['url'], 0, 8) == 'https://')
                                                {
                                                    $product_url = $validation->db_field_validate($productRow['url']);
                                                    $product_url_target = $validation->db_field_validate($productRow['url_target']);
                                                }
                                                else
                                                {
                                                    $product_url = BASE_URL."".$validation->db_field_validate($productRow['url']);
                                                    $product_url_target = $validation->db_field_validate($productRow['url_target']);
                                                }
                                            }
                                            else
                                            {
                                                $product_url = BASE_URL.'products/'.$validation->db_field_validate($productRow['title_id'])."/";
                                                $product_url_target = $validation->db_field_validate($productRow['url_target']);
                                            }
                                            
                                            $product_img = explode(" | ", $productRow['imgName']);
                                            
                                            $wishlistResult = $db->view('wishlistid', 'rb_wishlist', 'wishlistid', "and regid = '$regid' and productid = '$productid' and status = 'active'");
                                            
                                            $checkvariantResult = $db->view('variantid,stock_quantity', 'rb_products_variants', 'variantid', "and productid = '$productid'", 'variantid asc');
                                            $checkvariantRow = $checkvariantResult['result'][0];
                                            $product_variantid = $validation->db_field_validate($checkvariantRow['variantid']);
                                            
                                            $cartResult = $db->view('cartid', 'rb_cart', 'cartid', "and regid = '$regid' and productid = '$productid' and variantid='$product_variantid' and status = 'active'");
                                            
                                            $pincode = $_SESSION['pincode'];
                                            $pincodeResult = $db->view('pincodeid,pincode', 'rb_pincodes', 'pincodeid', "and pincode = '$pincode' and status = 'active'");
                                    ?>
                                    <div class="col-6 col-md-4 col-lg-4">
                                        <div class="product product-7 text-center">
                                             <form id="product-list-cart<?php echo $slr; ?>" action="<?php echo BASE_URL; ?>product-detail_inter.php?q=cart" method="post">
                                            <figure class="product-media">
                                            <?php if($productRow['sale'] == 1) { ?>
                                                <span class="product-label label-sale">Sale</span>
                                            <?php } ?>
                                                
                                                <a href="<?php echo $product_url; ?>">
                                                    <?php if($product_img[0] != "" and file_exists(IMG_MAIN_LOC.$product_img[0])) { ?>
                                                    <img src="<?php echo BASE_URL.IMG_MAIN_LOC.$product_img[0]; ?>"  alt="<?php echo $validation->db_field_validate($productRow['title']); ?>" title="<?php echo $validation->db_field_validate($productRow['title']); ?>" class="product-image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo BASE_URL; ?>images/noimage.jpg" title="<?php echo $validation->db_field_validate($productRow['title']); ?>" class="product-image" />
                                                    <?php } ?>
                                                </a>

                                                <div class="product-action-vertical">
                                                    <?php if($wishlistResult['num_rows'] >= 1) { ?>
                                                        <a href="<?php echo BASE_URL.'wishlist_delete.php?id='.$productid; ?>" title="Remove from wishlist" class="btn-product-icon btn-wishlist bg-success text-light"></a>
                                                    <?php } else { ?>
                                                        <a href="<?php echo BASE_URL.'wishlist_inter.php?id='.$productid.'&q='.$full_url; ?>" title="Add to wishlist" class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                                                    <?php } ?>
                                                    
                                                </div><!-- End .product-action-vertical -->

                                                <div class="product-action">
                                                        <?php if($checkvariantRow['stock_quantity'] >= 1  and ($pincode != '' ? $pincodeResult['num_rows'] >= 1 : $productid != "")) { ?>
                                                        <input type="hidden" name="id" value="<?php echo $productid; ?>" />
                                                        <input type="hidden" name="redirect_url" value="<?php echo $full_url; ?>" />
                                                        <input type="hidden" name="price" value="<?php echo $productRow['price']; ?>" />
                                                        <input type="hidden" name="variantid" value="<?php echo $product_variantid; ?>" />
                                                        <input type="hidden" name="quantity" value="1" />
                                                        <?php if($cartResult['num_rows'] >= 1) { ?>
                                                        <a href="<?php echo BASE_URL."cart".SUFFIX; ?>" class="btn-product btn-cart"><span>Go to Cart</span></a>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0);" onClick="cart_add('<?php echo $slr; ?>');" class="btn-product btn-cart"><span>add to cart</span></a>
                                                        <?php } ?>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0)" class="btn-product btn-cart"><span>Out of Stock <?php if($pincodeResult['num_rows'] == 0 and $pincode != "") echo "for ".$pincode; ?></span></a>
                                                        <?php } ?>

                                                </div><!-- End .product-action -->
                                            </figure><!-- End .product-media -->
                                            </form>
                                            <div class="product-body">
                                                <div class="product-cat">
                                                    <a href="javscript:void(0)"><?php echo $validation->db_field_validate($categoryRow['title']); ?></a>
                                                </div><!-- End .product-cat -->
                                                <h3 class="product-title"><a href="<?php echo $product_url; ?>" target="<?php echo $product_url_target; ?>"><?php echo $validation->db_field_validate($productRow['title']); ?></a></h3><!-- End .product-title -->
                                                <div class="product-price">
                                                    <?php if($productRow['price'] != '0') { ?>
                                                    <?php if($productRow['currency_code'] == 'INR') echo '<i class="fa fa-inr" style="font-size:17.5px;" aria-hidden="true"></i>'; else $validation->db_field_validate($productRow['currency_code']); ?> &#8377;<?php echo $validation->db_field_validate($validation->price_format($productRow['price'])); ?>
                                                    <?php } ?>
                                                    <?php if($productRow['mrp'] != '0') { ?>
                                                    <del class="ml-2"><?php if($productRow['currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($productRow['mrp'])); ?>
                                                    <?php } ?></del>
                                                </div><!-- End .product-price -->
                                            </div><!-- End .product-body -->
                                        </div><!-- End .product -->
                                    </div><!-- End .col-sm-6 col-lg-4 -->

                                   <?php
                                $slr++;
                            }
                        }
                        else
                        {
                        ?>
                            <h4 class="text-center mt-5 w-100">No Product Found!</h4>
                        <?php
                        }
                        ?>
                                </div><!-- End .row -->
                            </div><!-- End .products -->

            			<nav aria-label="Page navigation">
						    <?php echo $data['pagination']; ?>
						</nav>
                		</div><!-- End .col-lg-9 -->
                		<?php include_once("inc_filters.php");?>
                	</div><!-- End .row -->
                </div><!-- End .container -->
            </div><!-- End .page-content -->
 </main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>



<script type="text/javascript">
     
    $(document).ready(function(){
    var rangeSlider = $(".price-range"),
    minamount = $("#minamount"),
    maxamount = $("#maxamount"),
    minPrice = rangeSlider.data('min'),
    maxPrice = rangeSlider.data('max');
    rangeSlider.slider({
        range: true,
        min: 0,
        max: <?php echo $max_price; ?>,
        values: [ <?php if($min != "") echo $min; else echo "0"; ?>, <?php if($max != "") echo $max; else echo $max_price; ?> ],
        slide: function (event, ui) {
            $(".min_value_class").html(ui.values[0]);
            $(".max_value_class").html(ui.values[1]);
            //$(".min_value_class").html(number_format($(".min_value_class").html()));
            //$(".max_value_class").html(number_format($(".max_value_class").html()));
        },
        change : function (event, ui)
        {
            location.replace("<?php echo BASE_URL.''.$page_name.''.SUFFIX; ?>?min=" + ui.values[0] + "&max=" + ui.values[1] + "<?php echo $url_parameters_price; ?>");
        }
    });
    minamount.val(rangeSlider.slider("values", 0));
    maxamount.val(rangeSlider.slider("values", 1));
});

   
</script>
</body>
</html>