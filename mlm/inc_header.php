<nav class="navbar navbar-expand-lg navbar-dark fixed-top scrolling-navbar nav-top-bar" role="navigation">
	<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-ex1-collapse" aria-controls="navbar-ex1-collapse" aria-expanded="false" aria-label="Toggle navigation">
		<span CLASS="icon-bar"></span>
		<span CLASS="icon-bar"></span>
		<span CLASS="icon-bar"></span>
	</button>
	
	<ul CLASS="nav top-nav ml-auto">
		<a CLASS="navbar-brand pr-0 mr-0" HREF="<?php echo BASE_URL; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Back to Shopping"><i CLASS="fa fa-home top_home"></i> <?php if($configRow['logo'] != "") { ?>&nbsp;<img src="<?php echo FILE_LOC.''.$validation->db_field_validate($configRow['logo']); ?>" height="27" alt="Back to Shopping" title="<?php echo $validation->db_field_validate($configRow['meta_title']); ?>" class="top_logoimg" /><?php } ?></a>
		<li CLASS="nav-item dropdown">
			<a HREF="#" CLASS="nav-link dropdown-toggle" id="topbar_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php if($_SESSION['mlm_imgName'] != "") { ?><img src="<?php echo FILE_LOC.''.$_SESSION['mlm_imgName']; ?>" class="rounded-circle" height="15" /><?php } else { ?><i CLASS="fa fa-user"></i><?php } ?> &nbsp;<?php echo $validation->getplaintext($_SESSION['mlm_first_name'],10) . " ( " .$_SESSION['mlm_membership_id'] . " )"; ?> <b CLASS="caret"></b></a>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="topbar_dropdown">
				<a class="dropdown-item" HREF="user_password.php"><i CLASS="fa fa-fw fa-key"></i> Password</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" HREF="logout.php"><i CLASS="fa fa-fw fa-power-off"></i> Log Out</a>
			</div>
		</li>
	</ul>
	
	<div CLASS="collapse navbar-collapse navbar-ex1-collapse ml-auto">
		<ul CLASS="nav navbar-nav side-nav mr-auto" ID="SideNav">
			<li class="nav-item"><a HREF="home.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "dashboard") echo "active"; ?>"><i CLASS="fas fa-fw fa-tachometer-alt"></i>&nbsp; Dashboard</a></li>
			<li class="nav-item"><a HREF="profile.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "profile") echo "active"; ?>"><i class="fas fa-users"></i>&nbsp; Profile</a></li>
			<li class="nav-item"><a HREF="direct_member_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "direct_member") echo "active"; ?>"><i class="fas fa-network-wired"></i>&nbsp; My Direct</a></li>
			<li class="nav-item"><a HREF="downline_member_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "downline_member") echo "active"; ?>"><i class="fas fa-network-wired"></i>&nbsp; Downline Members</a></li>
			<li class="nav-item"><a HREF="genealogy.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "genealogy") echo "active"; ?>"><i class="fas fa-network-wired"></i>&nbsp; Genealogy (Team)</a></li>
			<li class="nav-item"><a HREF="ewallet_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "ewallet") echo "active"; ?>"><i class="fas fa-wallet"></i>&nbsp; Statements</a></li>
			<li class="nav-item"><a HREF="ewallet_request_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "ewallet_request") echo "active"; ?>"><i class="fas fa-money-check"></i>&nbsp; Withdrawn</a></li>
			<li class="nav-item"><a HREF="level_income_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "level_income_view") echo "active"; ?>"><i class="fas fa-money-check"></i>&nbsp; Level Income</a></li>
			<li class="nav-item"><a HREF="wallet_transfer_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "transfer") echo "active"; ?>"><i class="fas fa-money-check"></i>&nbsp; Transfer Money</a></li>
			<li class="nav-item"><a HREF="enquiry_view.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "enquiry") echo "active"; ?>"><i CLASS="fa fa-envelope"></i>&nbsp; Enquiries/Tickets</a></li>
			<li class="nav-item"><a HREF="user_password.php" class="nav-link <?php if(@$_SESSION['active_menu'] == "user_password") echo "active"; ?>"><i class="fa fa-key"></i>&nbsp; Change Password</a></li>
			<li class="nav-item"><a HREF="logout.php" class="nav-link"><i class="fa fa-power-off"></i>&nbsp; Log Out</a></li>
		</ul>
	</div>
</nav>