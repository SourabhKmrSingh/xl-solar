<?php
include_once("inc_config.php");

if($_SESSION['regid'] == "")
{
	$_SESSION['error_msg_fe'] = "Login to continue!";
	header("Location: {$base_url}login{$suffix}?url={$full_url}");
	exit();
}

$pageid = "change-password";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if($pageResult['num_rows'] == 0)
{
	header("Location: {$base_url}error{$suffix}");
	exit();
}
$pageRow = $pageResult['result'][0];

$registerResult = $db->view("*", "rb_registrations", "regid", "and regid='{$regid}'");
$registerRow = $registerResult['result'][0];

$_SESSION['csrf_token'] = substr(sha1(rand(1, 99999)),0,32);
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
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $validation->db_field_validate($pageRow['title']); ?></li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

		<div class="register-login-section bg-shadow pb-5 spad pt-0">
			<div class="container">
				<div class="row">
					<div class="col-lg-6 offset-lg-3">
						<div class="login-form">
							<div class="mb-5 mt-4 text-center">
								
							</div>
							
							
							
							<form action="<?php echo BASE_URL; ?>change-password_inter.php" method="post" class="form-box">
								<input type="hidden" name="token" value="<?php echo $csrf_token; ?>" />
								<input type="hidden" name="user_ip" value="<?php echo $validation->db_field_validate($registerRow['user_ip']); ?>" />
								
								<div class="col-12">
									<?php if($_SESSION['success_msg_fe'] != "") { ?>
										<div class="alert alert-success text-center mt-0 mb-4">
											<?php
											echo @$_SESSION['success_msg_fe'];
											@$_SESSION['success_msg_fe'] = "";
											?>
										</div>
									<?php } if($_SESSION['error_msg_fe'] != "") { ?>
										<div class="alert alert-danger text-center mt-0 mb-4">
											<?php
											echo @$_SESSION['error_msg_fe'];
											@$_SESSION['error_msg_fe'] = "";
											?>
										</div>
									<?php } ?>
								</div>
								
								<div class="row">
									<div class="col-md-12 group-input">
										<label for="first_name">Old Password</label>
										<input class="form-control" name="old_password" id="old_password" type="password" required />
									</div>
									<div class="col-md-6 group-input">
										<label for="password">New Password</label>
										<input class="form-control" name="password" id="password" type="password" autocomplete="new-password" required />
									</div>
									<div class="col-md-6 group-input">
										<label for="confirm_password">Confirm New Password</label>
										<input class="form-control" name="confirm_password" id="confirm_password" type="password" autocomplete="new-password" required />
									</div>
								</div>
								
								<div class="form-footer">
									<button type="submit" class="btn btn-primary w-100 mt-2 mb-2"><span>UPDATE</span></button>
								</div>
								
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
</body>
</html>