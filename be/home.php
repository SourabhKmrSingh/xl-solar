<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "dashboard";

$registerQueryResult = $db->view('regid', 'rb_registrations', 'regid');
$registerCount = $registerQueryResult['num_rows'];

$pageQueryResult = $db->view('pageid', 'rb_pages', 'pageid');
$pageCount = $pageQueryResult['num_rows'];

$productQueryResult = $db->view('productid', 'rb_products', 'productid');
$productCount = $productQueryResult['num_rows'];

$enquiryQueryResult = $db->view('enquiryid', 'rb_enquiries', 'enquiryid');
$enquiryCount = $enquiryQueryResult['num_rows'];
?>
<!DOCTYPE html>
<html LANG="en">
<head>
<?php include_once("inc_title.php"); ?>
<?php include_once("inc_files.php"); ?>
</head>
<body>
<div ID="wrapper">
<?php include_once("inc_header.php"); ?>
<div ID="page-wrapper">
<div CLASS="container-fluid">
	<div CLASS="row">
		<div CLASS="col-lg-12">
			<h1 CLASS="page-header">Dashboard</h1>
		</div>
	</div>
	<br>
	<div CLASS="row">
		<div CLASS="col-lg-3 col-md-6 mb-3 mb-md-0">
			<div CLASS="card card-blue">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fa fa-registered fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge"><?php echo $registerCount; ?></div>
							<div>Registrations!</div>
						</div>
					</div>
				</div>
				<a HREF="register_view.php">
					<div CLASS="card-footer">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div CLASS="col-lg-3 col-md-6 mb-3 mb-md-0">
			<div CLASS="card card-green">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fa fa-clone fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge"><?php echo $pageCount; ?></div>
							<div>Pages!</div>
						</div>
					</div>
				</div>
				<a HREF="page_view.php">
					<div CLASS="card-footer">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div CLASS="col-lg-3 col-md-6 mb-3 mb-md-0">
			<div CLASS="card card-yellow">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fab fa-product-hunt fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge"><?php echo $productCount; ?></div>
							<div>Products!</div>
						</div>
					</div>
				</div>
				<a HREF="product_view.php">
					<div CLASS="card-footer">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div CLASS="col-lg-3 col-md-6 mb-3 mb-md-0">
			<div CLASS="card card-red">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fa fa-envelope fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge"><?php echo $enquiryCount; ?></div>
							<div>Enquiries!</div>
						</div>
					</div>
				</div>
				<a HREF="enquiry_view.php">
					<div CLASS="card-footer">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</body>
</html>