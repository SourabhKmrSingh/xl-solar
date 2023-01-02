<?php
include_once("inc_config.php");

if($_SESSION['mlm_regid'] != '')
{
	$_SESSION['success_msg'] = "You're Logged In!";
	header("Location: home.php");
	exit();
}

$email = $validation->input_validate($_GET['email']);
if(!filter_var($email, FILTER_VALIDATE_EMAIL))
{
	$_SESSION['error_msg_fe'] = "Please enter a valid Email-ID!";
	header("Location: {$base_url_mlm}forgot-password{$suffix}");
	exit();
}
$membership_id = $validation->input_validate($_GET['q']);

$nowdatetime =  date('Y-m-d H:i:s');
$checkResult = $db->view("regid", "mlm_registrations", "regid", "and membership_id='{$membership_id}' and email='{$email}' and expirydatetime >= '{$nowdatetime}' and status='active'");
if($checkResult['num_rows'] == 0)
{
	$_SESSION['error_msg_fe'] = "The link has expired. Please generate a new link!";
	header("Location: {$base_url_mlm}forgot-password{$suffix}");
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
		<form class="row form-box" action="<?php echo BASE_URL; ?>forgot-password-complete_inter.php" method="post">
			<input type="hidden" name="token" value="<?php echo $csrf_token; ?>" />
			<input type="hidden" name="membership_id" value="<?php echo $membership_id; ?>" />
			<input type="hidden" name="email" value="<?php echo $email; ?>" />
			
			<div class="col-md-6 form-group">
				<h2 class="form-heading">Create a new Password</h2>
				<hr class="form-heading-line" />
			</div>
			<div class="col-md-6 form-group">
				<div class="logo d-flex justify-content-end"><?php if($configRow['logo'] != "") { ?><a href="<?php echo BASE_URL; ?>"><img src="<?php echo FILE_LOC."".$validation->db_field_validate($configRow['logo']); ?>" class="img-responsive mb-3" /></a><?php } ?></div>
			</div>
			
			<div class="col-md-12 form-group">
				<label for="email">Password *</label>
				<input class="form-control" name="password" id="password" type="password" autocomplete="new-password" required />
			</div>
			<div class="col-md-12 form-group">
				<label for="email">Confirm Password *</label>
				<input class="form-control" name="confirm_password" id="confirm_password" type="password" autocomplete="new-password" required />
			</div>
			<div class="col-md-12 form-group mt-1 d-flex justify-content-center">
				<button type="submit" class="btn btn-primary btn-block w-50 btn_submit">Reset Password</button>
			</div>
			
			<div class="col-md-12 form-group mt-3 text-center">
				Remember your password? <a href="<?php echo BASE_URL; ?>">Log In</a>
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