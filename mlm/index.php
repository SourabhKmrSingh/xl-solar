<?php
include_once("inc_config.php");

if($_SESSION['mlm_regid'] != '')
{
	$_SESSION['success_msg'] = "You're Logged In!";
	header("Location: home.php");
	exit();
}
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

	<?php if($_SESSION['register_msg']){?>
		<div class="modal fade" id="exampleModalCenter" style='z-index: 9999;' tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content rounded-0">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">
						<img src="<?php echo BASE_URL . IMG_MAIN_LOC . $configRow['logo'];?>" alt="logo" width='100'>
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p><?php echo $_SESSION['register_msg'];?></p>
				</div>
				
				</div>
			</div>
		</div>
	<?php } 
		unset($_SESSION['register_msg']);
	?>
  <div class="form">
    <form class="login-form" name="login_form" method="post" action="login_check.php">
	<div class="logo" align="center"><?php if($configRow['logo'] != "") { ?><img src="<?php echo FILE_LOC."".$validation->db_field_validate($configRow['logo']); ?>" class="img-responsive mw-100 mb-3" /><?php } ?></div>
	
	<div class="inner-addon left-addon mt-3">
		<i class="fas fa-user"></i>
		<input type="text" name="membership_id" id="membership_id" class="form-control" placeholder="Membership ID" autocomplete="new-password" />
	</div><br>
	<div class="inner-addon left-addon">
		<i class="fas fa-lock"></i>
		<input type="password" name="password" id="password" class="form-control" placeholder="Password" autocomplete="new-password" />
	</div>
	<div class="mb-3 mt-1 text-right">
		<a href="<?php echo BASE_URL; ?>forgot-password<?php echo SUFFIX; ?>" style="font-size:12.5px;">Forgot Password ?</a>
	</div>
	<button type="submit" name="login" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;LOGIN</button>
	
	<div class="mt-3 text-center" style="font-size:13px;">
		Not yet a member? <a href="<?php echo BASE_URL; ?>register<?php echo SUFFIX; ?>" style="font-size:13px;">Register here</a>
	</div>
    </form>
	<br>
	<!--<div class="logo"><h3 style="color: #999999; margin-right:-37px;"><img src="admin/images/logo.png" height="50" class="float-right" /></h3></div>-->
  </div>
</div>
</body>
<script>

$('#exampleModalCenter').modal('show')

</script>
</html>