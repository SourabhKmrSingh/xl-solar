<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "";

$passwordQueryResult = $db->view('*', 'rb_users', 'userid', "and userid = '$userid'");
$passwordRow = $passwordQueryResult['result'][0];
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
		<h1 CLASS="page-header">Change Password</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="user_password_inter.php" enctype="multipart/form-data">
<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="username"><strong>Username</strong></label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="username" id="username" class="form-control" value="<?php echo $validation->db_field_validate($passwordRow['username']); ?>" readonly />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="password">Old Password *</label>
		</div>
		<div class="col-sm-9">
			<input type="password" name="password" id="password" class="form-control" autocomplete="new-password" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="new_password">New Password *</label>
		</div>
		<div class="col-sm-9">
			<input type="password" name="new_password" id="new_password" class="form-control" autocomplete="new-password" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="confirm_new_password">Confirm New Password *</label>
		</div>
		<div class="col-sm-9">
			<input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" autocomplete="new-password" required />
		</div>
	</div>
	
	<div class="row mt-4 mb-4">
		<div class="col-sm-12">
			<button type="submit" name="submit" class="btn btn-default btn-sm mr-2 btn_submit"><i class="fas fa-save"></i>&nbsp;&nbsp;Update</button>
		</div>
	</div>
</div>
</form>
</div>
</div>
</div>
</body>
</html>