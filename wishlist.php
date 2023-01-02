<?php
include_once("inc_config.php");

if ($_SESSION['regid'] == "") {
    $_SESSION['error_msg_fe'] = "Login to continue!";
    header("Location: {$base_url}login{$suffix}?url={$full_url}");
    exit();
}

$page_name = "wishlist";
$pageid = "wishlist";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if ($pageResult['num_rows'] == 0) {
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageRow = $pageResult['result'][0];

// if($pageRow['url'] != "http://www." and $pageRow['url'] != "https://www." and $pageRow['url'] != "" and $_SESSION['full_url'] != $full_url)
// {
// if(substr($pageRow['url'], 0, 7) == 'http://' || substr($pageRow['url'], 0, 8) == 'https://')
// {
// $page_url = $validation->db_field_validate($pageRow['url']);
// $page_url_target = $validation->db_field_validate($pageRow['url_target']);
// }
// else
// {
// $page_url = BASE_URL."".$validation->db_field_validate($pageRow['url']);
// $page_url_target = $validation->db_field_validate($pageRow['url_target']);
// }

// $_SESSION['full_url'] = $full_url;
// header("Location: {$page_url}");
// exit();
// }
// $_SESSION['full_url'] = "";

@$q = strtolower($validation->urlstring_validate($_GET['q']));
@$min = $validation->urlstring_validate($_GET['min']);
@$max = $validation->urlstring_validate($_GET['max']);
@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

$where_query = "";
$where_query_price = "";
if ($q != "") {
    $where_query .= " and LOWER(title) LIKE '%$q%'";
    $where_query_price .= " and LOWER(title) LIKE '%$q%'";
}
if ($min != "" and $max != "") {
    $where_query .= " and price between '$min' and '$max'";
}
$where_query .= " and productid IN (select productid from rb_wishlist where regid='$regid') and status='active'";
$where_query_price .= " and productid IN (select productid from rb_wishlist where regid='$regid') and status='active'";

if ($orderby != "" and $order != "") {
    $orderby_final = "{$orderby} {$order}";
    if ($orderby == "createdate") {
        $orderby_final .= ", createtime {$order}";
    }
} else {
    $orderby_final = "order_custom desc";
}

$table = "rb_products";
$id = "productid";
$url_parameters = "&q=$q&min=$min&max=$max&orderby=$orderby&order=$order";
$url_parameters_order = "&q=$q&min=$min&max=$max";
$url_parameters_price = "&q=$q&orderby=$orderby&order=$order";

$data = $pagination2->main($table, $url_parameters, $where_query, $id, $orderby_final);

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
            	<div class="container">
					<table class="table table-wishlist table-mobile">
						<thead>
							<tr>
								<th>Product</th>
								<th>Price</th>
								<th>Stock Status</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<tbody>
                        <?php
                            $slr = 1;
                            if ($data['num_rows'] >= 1) {
                                foreach ($data['result'] as $productRow) {
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

                                    $wishlistResult = $db->view('*', 'rb_wishlist', 'wishlistid', "and regid = '$regid' and productid = '$productid' and status = 'active'");
                            ?>
							
							<tr>
								<td class="product-col">
									<div class="product">
										<figure class="product-media">
											<a href="#">
                                                <?php if ($product_img[0] != "" and file_exists(IMG_MAIN_LOC . $product_img[0])) { ?>
												<img src="<?php echo BASE_URL . IMG_MAIN_LOC . $product_img[0]; ?>" alt="<?php echo $validation->db_field_validate($productRow['title']); ?>" title="<?php echo $validation->db_field_validate($productRow['title']); ?>">
                                                <?php } else { ?>
                                                    <img src="<?php echo BASE_URL; ?>images/noimage.jpg" title="<?php echo $validation->db_field_validate($productRow['title']); ?>" />
                                                <?php } ?>
											</a>
										</figure>

										<h3 class="product-title">
											<a href="<?php echo $product_url?>" target="<?php echo $product_url_target;?>"><?php echo $validation->db_field_validate($productRow['title']); ?></a>
										</h3><!-- End .product-title -->
									</div><!-- End .product -->
								</td>
								<td class="price-col">
                                    
                                    <?php if ($productRow['price'] != '0') { ?>
                                        <span><?php if ($productRow['currency_code'] == 'INR') echo '&#8377;';
                                                else $validation->db_field_validate($productRow['currency_code']); ?> <?php echo $validation->db_field_validate($validation->price_format($productRow['price'])); ?></span>
                                    <?php } ?>
                                </td>
								<td class="stock-col"><span class="in-stock">
                                    <?php if($productRow['stock_quantity'] == 0){ ?>
                                        <p class='text-danger'>Out of stock</p>
                                    <?php }else{?> 
                                         <p class='text-success'>In stock</p>
                                    <?php }?>                       
                                </span></td>
                                 
								<td class="action-col">
									<a href="<?php echo $product_url?>" target="<?php echo $product_url_target;?>" class="btn btn-block btn-outline-primary-2">
                                        Go to Product</a>
								</td>
								<td class="remove-col">
                                    <a class="product-list-heart" href="<?php echo BASE_URL . 'wishlist_delete.php?id=' . $productid; ?>" class="btn-remove"><i class="icon-close"></i></a></td>
							</tr>
							<?php
                                    $slr++;
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="6">No Product Found!</td>
                                </tr>
                            <?php
                            }
                            ?>
						</tbody>
					</table><!-- End .table table-wishlist -->
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