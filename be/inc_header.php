<nav class="navbar navbar-expand-lg navbar-dark fixed-top scrolling-navbar nav-top-bar" role="navigation">
	<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-ex1-collapse" aria-controls="navbar-ex1-collapse" aria-expanded="false" aria-label="Toggle navigation">
		<span CLASS="icon-bar"></span>
		<span CLASS="icon-bar"></span>
		<span CLASS="icon-bar"></span>
	</button>
	
	<ul CLASS="nav top-nav ml-auto">
		<a CLASS="navbar-brand" HREF="<?php echo BASE_URL; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Visit Website"><i CLASS="fa fa-home top_home"></i> <?php if($configRow['logo'] != "") { ?>&nbsp;<img src="<?php echo IMG_MAIN_LOC.''.$validation->db_field_validate($configRow['logo']); ?>" height="33" alt="Visit Website" title="<?php echo $validation->db_field_validate($configRow['meta_title']); ?>" class="top_logoimg" /><?php } ?></a>
		<li CLASS="nav-item dropdown">
			<a HREF="#" CLASS="nav-link dropdown-toggle" id="topbar_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php if($_SESSION['be_imgName'] != "") { ?><img src="<?php echo FILE_LOC.''.$_SESSION['be_imgName']; ?>" class="rounded-circle" height="15" /><?php } else { ?><i CLASS="fa fa-user"></i><?php } ?> &nbsp;<?php echo $_SESSION['be_display_name']; ?> <b CLASS="caret"></b></a>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="topbar_dropdown">
				<?php if($_SESSION['be_type'] == "admin") { ?>
					<a class="dropdown-item" HREF="user_master_view.php"><i CLASS="fa fa-fw fa-user"></i> User Master</a>
					<a class="dropdown-item" HREF="config.php"><i class="fas fa-user-cog"></i> Configurations</a>
				<?php } ?>
				<a class="dropdown-item" HREF="user_password.php"><i CLASS="fa fa-fw fa-key"></i> Password</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" HREF="logout.php"><i CLASS="fa fa-fw fa-power-off"></i> Log Out</a>
			</div>
		</li>
	</ul>
	
	<div CLASS="collapse navbar-collapse navbar-ex1-collapse ml-auto">
		<ul CLASS="nav navbar-nav side-nav mr-auto" ID="SideNav">
			<?php if($_SESSION['per_read'] == "1") { ?>
				<li class="nav-item"><a HREF="home.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "dashboard") echo "active"; ?>"><i CLASS="fas fa-fw fa-tachometer-alt"></i>&nbsp; Dashboard</a></li>
				<li class="nav-item"><a HREF="page_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "pages") echo "active"; ?>"><i CLASS="fa fa-clone"></i>&nbsp;&nbsp; Menu/Pages</a></li>
				<li class="nav-item"><a HREF="slider_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "slider") echo "active"; ?>"><i CLASS="fa fa-image"></i>&nbsp;&nbsp; Sliders</a></li>
				<li class="nav-item"><a HREF="category_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "category") echo "active"; ?>"><i CLASS="fa fa-edit"></i>&nbsp; Categories</a></li>
				<li class="nav-item"><a HREF="subcategory_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "subcategory") echo "active"; ?>"><i CLASS="fa fa-edit"></i>&nbsp; Sub-Categories</a></li>
				<li class="nav-item"><a HREF="product_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "product") echo "active"; ?>"><i class="fab fa-product-hunt"></i>&nbsp; Products</a></li>
				<li class="nav-item"><a HREF="product_review_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "product_reviews") echo "active"; ?>"><i class="fas fa-comments"></i>&nbsp; Product Reviews</a></li>
				<li class="nav-item"><a HREF="coupon_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "coupon") echo "active"; ?>"><i class="fa fa-tag"></i>&nbsp; Coupons</a></li>
				<li class="nav-item"><a HREF="purchase_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "purchase") echo "active"; ?>"><i class="fa fa-shopping-cart"></i>&nbsp; Orders</a></li>
				<li class="nav-item"><a HREF="dynamic_page_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "dynamic_pages") echo "active"; ?>"><i CLASS="fa fa-clone"></i>&nbsp; Dynamic Pages</a></li>
				<li class="nav-item"><a HREF="dynamic_record_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "dynamic_records") echo "active"; ?>"><i CLASS="fa fa-list"></i>&nbsp; Dynamic Records</a></li>
				<!--<li class="nav-item"><a HREF="testimonial_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "testimonial") echo "active"; ?>"><i CLASS="fa fa-quote-left"></i>&nbsp; Testimonials</a></li>-->
				<li class="nav-item"><a HREF="pincode_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "pincode") echo "active"; ?>"><i class="fas fa-map-marker-alt"></i>&nbsp; Pincodes Master</a></li>
				<!-- <li class="nav-item"><a HREF="faq_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "faq") echo "active"; ?>"><i CLASS="fa fa-question-circle"></i>&nbsp; FAQs</a></li> -->
				<li class="nav-item"><a HREF="badlink_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "badlink") echo "active"; ?>"><i CLASS="fa fa-link"></i>&nbsp; Bad Links</a></li>
				<li class="nav-item"><a HREF="media_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "media") echo "active"; ?>"><i CLASS="fa fa-upload"></i>&nbsp; Media</a></li>
				<!-- <li class="nav-item"><a HREF="newsletter_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "newsletter") echo "active"; ?>"><i CLASS="fa fa-envelope"></i>&nbsp; Newsletters</a></li> -->
				<li class="nav-item"><a HREF="enquiry_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "enquiry") echo "active"; ?>"><i CLASS="fa fa-envelope"></i>&nbsp; Enquiries</a></li>
				<li class="nav-item"><a HREF="register_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "register") echo "active"; ?>"><i CLASS="fa fa-registered"></i>&nbsp; Registrations</a></li>
			<?php } ?>
		</ul>
	</div>
</nav>