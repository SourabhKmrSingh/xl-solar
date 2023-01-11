<?php
include('inc_config.php');
$headerwishlistResult = $db->view('wishlistid', 'rb_wishlist', 'wishlistid', "and regid = '$regid' and status = 'active'");
$headercartResult = $db->view('cartid', 'rb_cart', 'cartid', "and regid = '$regid' and status = 'active'");
?>

<header class="header">
    <div class="row">
            <div class="col-sm-12 mlm-register" style='border-bottom: 1px solid #e8e8e8;'>
                <a href="<?php echo BASE_URL?>mlm/register.php" class='ml-4' target="_blank">
                    <i class="fa fa-external-link-alt mr-2"></i> Become a member 
                </a>
                <a href="<?php echo BASE_URL?>mlm<?php echo SUFFIX;?>" class='ml-4' target="_blank">
                    <i class="fa fa-external-link-alt mr-2"></i> Login as Member 
                </a>
            </div>
        </div>
    <div class="header-bottom sticky-header">
        
        <div class="container-fluid">
       
            <div class="header-left">
                <button class="mobile-menu-toggler">
                    <span class="sr-only">Toggle mobile menu</span>
                    <i class="icon-bars"></i>
                </button>
                
                <a href="<?php echo BASE_URL;?>" class="logo pb-1">
                    <img src="<?php echo BASE_URL . IMG_MAIN_LOC . $validation->db_field_validate($configRow['logo']); ?>" alt="<?php echo $validation->db_field_validate($configRow['meta_title']); ?>" width="150" height="20">
                </a>
            </div>
            <div class="header-center">
                <nav class="main-nav">
                    <ul class="menu sf-arrows">
                         <?php
                        $menuResult = $db->view('pageid,title,title_id,url,url_target,order_custom', 'rb_pages', 'pageid', "and main_menu='0' and sub_menu='0' and order_custom!='0' and status='active'", 'order_custom asc', '6');
                    if ($menuResult['num_rows'] >= 1) {
                        foreach ($menuResult['result'] as $menuRow) {
                            if ($menuRow['url'] == "#") {
                                $menu_url = "#";
                                $menu_url_target = "";
                            } else if ($menuRow['url'] != "http://www." and $menuRow['url'] != "https://www." and $menuRow['url'] != "") {
                                if (substr($menuRow['url'], 0, 7) == 'http://' || substr($menuRow['url'], 0, 8) == 'https://') {
                                    $menu_url = $validation->db_field_validate($menuRow['url']);
                                    $menu_url_target = $validation->db_field_validate($menuRow['url_target']);
                                } else {
                                    $menu_url = BASE_URL . "" . $validation->db_field_validate($menuRow['url']);
                                    $menu_url_target = $validation->db_field_validate($menuRow['url_target']);
                                }
                            } else {
                                $menu_url = BASE_URL . "page/" . $validation->db_field_validate($menuRow['title_id']) . "/";
                                $menu_url_target = $validation->db_field_validate($menuRow['url_target']);
                            }

                            $menuid = $validation->db_field_validate($menuRow['pageid']);
                            $submenuResult = $db->view('pageid,title,title_id,url,url_target,order_custom', 'rb_pages', 'pageid', "and main_menu='$menuid' and sub_menu='0' and order_custom!='0' and status='active'", 'order_custom asc', '8');
                        ?>

                        <li class="megamenu-container">
                            <?php if ($menuRow['order_custom'] == "1") { ?>
                                <a href="<?php echo $menu_url; ?>" target="<?php echo $menu_url_target; ?>" <?php if ($menu_url_target == "_blank") echo "rel='noopener noreferrer'"; ?>><?php echo $validation->db_field_validate($menuRow['title']); ?></a>
                            <?php } else { ?>
                                 <a href="<?php echo $menu_url; ?>" target="<?php echo $menu_url_target; ?>" <?php if ($menu_url_target == "_blank") echo "rel='noopener noreferrer'"; ?>><?php echo $validation->db_field_validate($menuRow['title']); ?></a>
                            <?php } ?>
                        </li>
                        <?php }}?>
                    </ul><!-- End .menu -->
                </nav><!-- End .main-nav -->
            </div><!-- End .header-left -->

            <div class="header-right">
                <div class="header-search">
                    <a href="#" class="search-toggle" role="button"><i class="icon-search"></i></a>
                    <form action="<?= BASE_URL . "products" . SUFFIX;?>" method="get">
                        <div class="header-search-wrapper">
                            <label for="q" class="sr-only">Search</label>
                            <input type="search" class="form-control" name="q" id="q" placeholder="Search in..." required>
                        </div><!-- End .header-search-wrapper -->
                    </form>
                </div><!-- End .header-search -->

                <a href="<?php echo BASE_URL . 'wishlist' . SUFFIX; ?>" class="wishlist-link">
                    <i class="icon-heart-o"></i>
                    <span class="wishlist-count bg-success"><?php echo $headerwishlistResult['num_rows']; ?></span>
                </a>
                 <a href="<?php echo BASE_URL . 'cart' . SUFFIX; ?>" class="wishlist-link">
                    <i class="icon-shopping-cart"></i>
                    <span class="wishlist-count bg-success"><?php echo $headercartResult['num_rows']; ?></span>
                </a>
                <?php if ($_SESSION['mobile'] != "" and $_SESSION['regid'] != "") { ?>
                     <div class='ml-5 mobile-hide'>
                        <a href="<?php echo BASE_URL . 'home' . SUFFIX; ?>">
                            <i class="icon-user"></i>
                            Hi <?php echo $_SESSION['first_name']; ?>!
                        </a>
                        
                       
                    </div> 
                <?php } else { ?>
                    <div class='ml-5 mobile-hide'>
                        <a href="<?php echo BASE_URL. 'login' . SUFFIX;?>" class="">
                            <i class="icon-user"></i>
                            login
                        </a>
                        <a href="<?php echo BASE_URL?>mlm/register.php" class="">
                            / Register
                        </a>   
                    </div> 
               <?php } ?>            
            </div><!-- End .header-right -->
        </div><!-- End .container -->
    </div><!-- End .header-bottom -->
</header><!-- End .header -->