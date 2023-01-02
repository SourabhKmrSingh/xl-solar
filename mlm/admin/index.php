<?php
include_once("inc_config.php");

if($_SESSION['mlm_be_userid'] != '')
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
<meta charset="utf-8" />
<meta HTTP-EQUIV="X-UA-Compatible" CONTENT="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="robots" content="noindex" />
<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
<meta NAME="description" CONTENT="">
<meta NAME="author" CONTENT="">
<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="assets/font-awesome-v5/css/all.min.css">
<script type="text/javascript" src="assets/font-awesome-v5/js/all.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/notify.min.js"></script>

<link rel="stylesheet" href="assets/css/index_style.css">

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
  <div class="form">
    <form class="login-form" name="login_form" method="post" action="login_check.php">
	<div class="logo" align="center">
	<?php if($configRow['logo'] != "") { ?>
		<img src="<?php echo FILE_LOC."".$validation->db_field_validate($configRow['logo']); ?>" class="img-responsive mw-100 mb-3" /><?php } ?></div>
	
	<div class="inner-addon left-addon">
		<select name="type" id="type" class="form-control">
			<option value="admin">Admin</option>
			<option value="user">Sub-Admin</option>
		</select>
	</div><br>
	<div class="inner-addon left-addon">
		<i class="fas fa-user"></i>
		<input type="text" name="username" id="username" class="form-control" placeholder="Username"/>
	</div><br>
	<div class="inner-addon left-addon">
		<i class="fa fa-key"></i>
		<input type="password" name="password" id="password" class="form-control" placeholder="Password"/>
	</div><br>
	<button type="submit" name="login" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;LOGIN</button>
    </form>
	<br>
	<!-- <div class="logo"><h3 style="color: #999999; margin-right:-37px;"><img src="images/logo.png" height="50" class="float-right" /></h3></div> -->
  </div>
</div>
</body>
</html>