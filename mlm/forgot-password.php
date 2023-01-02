<?php
include_once("inc_config.php");

if($_SESSION['mlm_regid'] != '')
{
	$_SESSION['success_msg'] = "You're Logged In!";
	header("Location: home.php");
	exit();
}

$_SESSION['csrf_token'] = substr(sha1(rand(1, 99999)),0,32);
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html>
<head>
<?php include_once("inc_title.php"); ?>
<?php include_once("inc_files.php"); ?>
<link rel="stylesheet" href="admin/assets/css/index_style.css">
<script>
$(window).bind("load", function(){
	$.notify("<?php echo @$_SESSION['success_msg']; ?>", { className: 'success', autoHide: true, autoHideDelay: 8000 });
	$.notify("<?php echo @$_SESSION['error_msg']; ?>", { className: 'error', autoHide: true, autoHideDelay: 8000 });
});
</script>
<?php
@$_SESSION['success_msg'] = "";
@$_SESSION['error_msg'] = "";
?>
</head>
<body>
<div class="login-page">
	<br />
	<div class="row">
	<div class="col-md-4 offset-md-4">
	<div class="w-100">
		<form class="row form-box" action="<?php echo BASE_URL; ?>forgot-password_inter.php" method="post">
			<input type="hidden" name="token" value="<?php echo $csrf_token; ?>" />
			
			<div class="col-md-6 form-group">
				<h2 class="form-heading">Forgot your Password ?</h2>
				<hr class="form-heading-line" />
			</div>
			<div class="col-md-6 form-group">
				<div class="logo d-flex justify-content-end"><?php if($configRow['logo'] != "") { ?><a href="<?php echo BASE_URL; ?>"><img src="<?php echo FILE_LOC."".$validation->db_field_validate($configRow['logo']); ?>" class="img-responsive mb-3" /></a><?php } ?></div>
			</div>
			
			<p class="text-center">Please fill in the email that you used to register. You will be sent an email with instructions on how to reset your password.</p>
			
			<div class="col-md-12 form-group">
				<input class="form-control" name="email" id="email" type="email" placeholder="Enter Email Address" required />
			</div>
			
			<div class="col-md-12 form-group mt-1 d-flex justify-content-center">
				<button type="submit" class="btn btn-primary btn-block w-50 btn_submit">Send Recovery Email</button>
			</div>
			<div class="col-md-12 form-group mt-3 text-center">
				Remember your password? <a href="<?php echo BASE_URL; ?>">Log in</a>
			</div>
			<br>
			<!--<div class="col-md-12">
				<div class="float-right"><img src="admin/images/logo.png" height="50" /></div>
			</div>-->
		</form>
	</div>
	</div>
	</div>
	<br />
</div>
</body>
</html>