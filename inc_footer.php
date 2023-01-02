<footer class="footer footer-2">
    <div class="footer-middle">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-6">
                    <div class="widget widget-about">
                        <img src="<?php echo BASE_URL . IMG_MAIN_LOC . $validation->db_field_validate($configRow['logo']); ?>" alt="<?php echo $validation->db_field_validate($configRow['meta_title']); ?>" width="150" height="20">
                        <p>
                        Our technical team has an expertise of 10+ years. We provider all assistance from installation to operational services. XL Solar power is emerging as one of the most reliable and top-notch solar company in India.
                        </p>

                        <div class="">
                            <div class="row">
                               
                                <div class="col-sm-6 col-md-12">
                                    <span class="widget-about-title">Got Question? Call us 24/7</span>
                                    <a href="tel:<?php echo $validation->db_field_validate($configRow['contact_number'])?>">Mobile: <?php echo $validation->db_field_validate($configRow['contact_number'])?></a>
                                    <a class='ml-3' href="mailto:<?php echo $validation->db_field_validate($configRow['email'])?>">Email: <?php echo $validation->db_field_validate($configRow['email'])?></a>
                                </div>
                                <!-- End .col-sm-6 -->
                                <!-- <div class="col-sm-6 col-md-8">
                                    <span class="widget-about-title">Payment Method</span>
                                    <figure class="footer-payments">
                                        <img src="assets/images/payments.png" alt="Payment methods" width="272" height="20">
                                    </figure>--><!-- End .footer-payments -->
                               <!--  </div> --><!-- End .col-sm-6 -->
                            </div><!-- End .row -->
                        </div><!-- End .widget-about-info -->
                    </div><!-- End .widget about-widget -->
                </div><!-- End .col-sm-12 col-lg-3 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget">
                        <h4 class="widget-title">Information</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="<?php echo BASE_URL . 'page/about-us' . SUFFIX;?>">About</a></li>
                            <li><a href="<?php echo BASE_URL . 'contact' . SUFFIX;?>">Contact us</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-4 col-lg-3 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget">
                        <h4 class="widget-title">Customer Service</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="<?php echo BASE_URL . 'page/terms-and-conditions' . SUFFIX;?>">Terms and conditions</a></li>
                            <li><a href="<?php echo BASE_URL . 'page/privacy-policy' . SUFFIX;?>">Privacy Policy</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-4 col-lg-3 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget">
                        <h4 class="widget-title">My Account</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <?php if ($_SESSION['mobile'] != "" and $_SESSION['regid'] != "") { ?>
                                <li><a href="<?php echo BASE_URL . 'logout' . SUFFIX;?>">Logout</a></li>
                            <?php } else { ?>
                                <li><a href="<?php echo BASE_URL . 'login' . SUFFIX;?>">Login</a></li>
                            <?php } ?>     
                            <li><a href="<?php echo BASE_URL . 'cart' . SUFFIX;?>">View Cart</a></li>
                            <li><a href="<?php echo BASE_URL . 'wishlist' . SUFFIX;?>">My Wishlist</a></li>
                            <li><a href="<?php echo BASE_URL . 'orders' . SUFFIX;?>">Track My Order</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-64 col-lg-3 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .footer-middle -->

    <div class="footer-bottom">
        <div class="container-fluid">
            <p class="footer-copyright">Copyright © 2021 XL Solar . All Rights Reserved.</p><!-- End .footer-copyright -->
           
            <div class="social-icons social-icons-color">
                <span class="social-label">Social Media</span>
                <?php if($configRow['facebook']){?>
                    <a href="<?php echo $validation->db_field_validate($configRow['facebook']);?>" class="social-icon social-facebook" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                <?php }if($configRow['twitter']){?>
                    <a href="<?php echo $validation->db_field_validate($configRow['twitter']);?>" class="social-icon social-twitter" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                <?php }if($configRow['instagram']){?>
                    <a href="<?php echo $validation->db_field_validate($configRow['instagram']);?>" class="social-icon social-instagram" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                <?php }if($configRow['youtube']){?>
                    <a href="<?php echo $validation->db_field_validate($configRow['youtube']);?>" class="social-icon social-youtube" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
                <?php }if($configRow['linkedin']){?>
                    <a href="<?php echo $validation->db_field_validate($configRow['linkedin']);?>" class="social-icon social-linkedin" title="linkedin" target="_blank"><i class="icon-linkedin text-info"></i></a>
                <?php }if($configRow['whatsapp']){?>
                    <a href="<?php echo $validation->db_field_validate($configRow['whatsapp']);?>" class="social-icon social-whatsapp" title="whatsapp" target="_blank"><i class="icon-whatsapp text-success"></i></a>
                <?php }?>
            </div><!-- End .soial-icons -->
        </div><!-- End .container -->
    </div><!-- End .footer-bottom -->
</footer><!-- End .footer -->
<?php include_once("inc_mobile_menu.php");?>