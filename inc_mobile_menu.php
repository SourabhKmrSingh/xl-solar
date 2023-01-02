<!--Mobile Menu -->
    <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

    <div class="mobile-menu-container bg-light">
        <div class="mobile-menu-wrapper">
            <span class="mobile-menu-close"><i class="icon-close text-dark"></i></span>

            <form action="#" method="get" class="mobile-search">
                <label for="mobile-search" class="sr-only">Search</label>
                <input type="search" class="form-control" name="mobile-search" id="mobile-search" placeholder="Search in..." required>
                <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
            </form>

            
            <nav class="mobile-nav">
                <ul class="mobile-menu">
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
                    <li>
                        <?php if ($menuRow['order_custom'] == "1") { ?>
                             <a class='text-dark' href="<?php echo $menu_url; ?>" target="<?php echo $menu_url_target; ?>" <?php if ($menu_url_target == "_blank") echo "rel='noopener noreferrer'"; ?>><?php echo $validation->db_field_validate($menuRow['title']); ?></a>
                        <?php } else { ?>
                             <a class='text-dark' href="<?php echo $menu_url; ?>" target="<?php echo $menu_url_target; ?>" <?php if ($menu_url_target == "_blank") echo "rel='noopener noreferrer'"; ?>><?php echo $validation->db_field_validate($menuRow['title']); ?></a>
                        <?php } ?>
                    </li>

                    <?php }}?>
                    
                    <?php if ($_SESSION['mobile'] != "" and $_SESSION['regid'] != "") { ?>
                        <li>
                            <a href="<?php echo BASE_URL. 'logout' . SUFFIX;?>" class="text-dark">logout</a>
                        </li>
                    <?php } else { ?>
                        <li>
                           <a href="<?php echo BASE_URL. 'login' . SUFFIX;?>" class="text-dark">login</a>
                            <a href="<?php echo BASE_URL. 'register' . SUFFIX;?>" class=" text-dark">Register</a>  
                        </li>
                   <?php } ?>
                   
                    <!-- <li>
                        <a href="category.html">Shop</a>
                        <ul>
                            <li><a href="category-list.html">Shop List</a></li>
                            <li><a href="category-2cols.html">Shop Grid 2 Columns</a></li>
                            <li><a href="category.html">Shop Grid 3 Columns</a></li>
                            <li><a href="category-4cols.html">Shop Grid 4 Columns</a></li>
                            <li><a href="category-boxed.html"><span>Shop Boxed No Sidebar<span class="tip tip-hot">Hot</span></span></a></li>
                            <li><a href="category-fullwidth.html">Shop Fullwidth No Sidebar</a></li>
                            <li><a href="product-category-boxed.html">Product Category Boxed</a></li>
                            <li><a href="product-category-fullwidth.html"><span>Product Category Fullwidth<span class="tip tip-new">New</span></span></a></li>
                            <li><a href="cart.html">Cart</a></li>
                            <li><a href="checkout.html">Checkout</a></li>
                            <li><a href="wishlist.html">Wishlist</a></li>
                            <li><a href="#">Lookbook</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="product.html" class="sf-with-ul">Product</a>
                        <ul>
                            <li><a href="product.html">Default</a></li>
                            <li><a href="product-centered.html">Centered</a></li>
                            <li><a href="product-extended.html"><span>Extended Info<span class="tip tip-new">New</span></span></a></li>
                            <li><a href="product-gallery.html">Gallery</a></li>
                            <li><a href="product-sticky.html">Sticky Info</a></li>
                            <li><a href="product-sidebar.html">Boxed With Sidebar</a></li>
                            <li><a href="product-fullwidth.html">Full Width</a></li>
                            <li><a href="product-masonry.html">Masonry Sticky Info</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">Pages</a>
                        <ul>
                            <li>
                                <a href="about.html">About</a>

                                <ul>
                                    <li><a href="about.html">About 01</a></li>
                                    <li><a href="about-2.html">About 02</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="contact.html">Contact</a>

                                <ul>
                                    <li><a href="contact.html">Contact 01</a></li>
                                    <li><a href="contact-2.html">Contact 02</a></li>
                                </ul>
                            </li>
                            <li><a href="login.html">Login</a></li>
                            <li><a href="faq.html">FAQs</a></li>
                            <li><a href="404.html">Error 404</a></li>
                            <li><a href="coming-soon.html">Coming Soon</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="blog.html">Blog</a>

                        <ul>
                            <li><a href="blog.html">Classic</a></li>
                            <li><a href="blog-listing.html">Listing</a></li>
                            <li>
                                <a href="#">Grid</a>
                                <ul>
                                    <li><a href="blog-grid-2cols.html">Grid 2 columns</a></li>
                                    <li><a href="blog-grid-3cols.html">Grid 3 columns</a></li>
                                    <li><a href="blog-grid-4cols.html">Grid 4 columns</a></li>
                                    <li><a href="blog-grid-sidebar.html">Grid sidebar</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Masonry</a>
                                <ul>
                                    <li><a href="blog-masonry-2cols.html">Masonry 2 columns</a></li>
                                    <li><a href="blog-masonry-3cols.html">Masonry 3 columns</a></li>
                                    <li><a href="blog-masonry-4cols.html">Masonry 4 columns</a></li>
                                    <li><a href="blog-masonry-sidebar.html">Masonry sidebar</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Mask</a>
                                <ul>
                                    <li><a href="blog-mask-grid.html">Blog mask grid</a></li>
                                    <li><a href="blog-mask-masonry.html">Blog mask masonry</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Single Post</a>
                                <ul>
                                    <li><a href="single.html">Default with sidebar</a></li>
                                    <li><a href="single-fullwidth.html">Fullwidth no sidebar</a></li>
                                    <li><a href="single-fullwidth-sidebar.html">Fullwidth with sidebar</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="elements-list.html">Elements</a>
                        <ul>
                            <li><a href="elements-products.html">Products</a></li>
                            <li><a href="elements-typography.html">Typography</a></li>
                            <li><a href="elements-titles.html">Titles</a></li>
                            <li><a href="elements-banners.html">Banners</a></li>
                            <li><a href="elements-product-category.html">Product Category</a></li>
                            <li><a href="elements-video-banners.html">Video Banners</a></li>
                            <li><a href="elements-buttons.html">Buttons</a></li>
                            <li><a href="elements-accordions.html">Accordions</a></li>
                            <li><a href="elements-tabs.html">Tabs</a></li>
                            <li><a href="elements-testimonials.html">Testimonials</a></li>
                            <li><a href="elements-blog-posts.html">Blog Posts</a></li>
                            <li><a href="elements-portfolio.html">Portfolio</a></li>
                            <li><a href="elements-cta.html">Call to Action</a></li>
                            <li><a href="elements-icon-boxes.html">Icon Boxes</a></li>
                        </ul>
                    </li> -->
                </ul>
                
            </nav><!-- End .mobile-nav -->

            <div class="social-icons">
                <?php if($configRow['facebook']){?>
                    <a href="<?php echo $validation->db_field_validate($configRow['facebook']);?>" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f text-dark"></i></a>
                <?php }if($configRow['twitter']){ ?>
                    <a href="<?php echo $validation->db_field_validate($configRow['twitter']);?>" class="social-icon" target="_blank" title="Twitter"><i class="icon-twitter text-dark"></i></a>
                <?php }if($configRow['instagram']){ ?>
                    <a href="<?php echo $validation->db_field_validate($configRow['instagram']);?>" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram text-dark"></i></a>
                <?php }if($configRow['youtube']){ ?>
                    <a href="<?php echo $validation->db_field_validate($configRow['youtube']);?>" class="social-icon" target="_blank" title="Instagram"><i class="icon-youtube text-dark"></i></a>
                <?php }if($configRow['linkedin']){ ?>
                    <a href="<?php echo $validation->db_field_validate($configRow['linkedin']);?>" class="social-icon" target="_blank" title="linkedin"><i class="icon-linkedin text-dark"></i></a>
                <?php }if($configRow['whatsapp']){ ?>
                    <a href="<?php echo $validation->db_field_validate($configRow['whatsapp']);?>" class="social-icon" target="_blank" title="whatsapp"><i class="icon-whatsapp text-dark"></i></a>
                <?php }?>
            </div><!-- End .social-icons -->
        </div><!-- End .mobile-menu-wrapper -->
    </div><!-- End .mobile-menu-container