<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "";

echo $validation->admin_permission();

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: user_master_view.php");
	exit();
}

if($mode == "edit")
{
	echo $validation->update_permission();
}
else
{
	echo $validation->write_permission();
}

if($mode == "edit")
{
	$userid = $validation->urlstring_validate($_GET['userid']);
	$userQueryResult = $db->view('*', 'rb_users', 'userid', "and userid = '$userid'");
	$userRow = $userQueryResult['result'][0];
}

if(isset($_GET['q']))
{
	$q = $validation->urlstring_validate($_GET['q']);
	if($q == "imgdel")
	{
		$delresult = $media->filedeletion('rb_users', 'userid', $userid, 'imgName', IMG_MAIN_LOC);
		if($delresult)
		{
			$_SESSION['success_msg'] = "Image has been deleted Successfully!!!";
			header("Location: user_master_form.php?mode=edit&userid=$userid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: user_master_form.php?mode=edit&userid=$userid");
			exit();
		}
	}
}
?>
<!DOCTYPE html>
<html LANG="en">
<head>
<?php include_once("inc_title.php"); ?>
<?php include_once("inc_files.php"); ?>
<script>
function passwordmatch()
{
	if($("#password").val() != $("#confirm_password").val())
	{
		alert("Password and Confirm Password should be Same!");
		$("#password").val("");
		$("#confirm_password").val("");
	}
}
</script>
</head>
<body>
<div ID="wrapper">
<?php include_once("inc_header.php"); ?>
<div ID="page-wrapper">
<div CLASS="container-fluid">
<div CLASS="row">
	<div CLASS="col-lg-12">
		<h1 CLASS="page-header"><?php if($mode == "insert") echo "Add New"; else echo "Update"; ?> User</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "user_master_form_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "user_master_form_inter.php?mode=$mode&userid=$userid";
													break;
													
													default : echo "user_master_form_inter.php";
												}
												?>" enctype="multipart/form-data">

<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="type">Type *</label>
		</div>
		<div class="col-sm-9">
			<select NAME="type" CLASS="form-control" ID="type" required >
				<option VALUE="" <?php if($mode == 'edit') { if($userRow['type'] == "") echo "selected"; } ?>>--select--</option>
				<option value="admin" <?php if($mode == 'edit') { if($userRow['type'] == "admin") echo "selected"; } ?>>Admin</option>
				<option value="user" <?php if($mode == 'edit') { if($userRow['type'] == "user") echo "selected"; } ?>>User</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="username"><strong>Username *</strong></label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="username" id="username" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($userRow['username']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="password">Password *</label>
		</div>
		<div class="col-sm-9">
			<input type="password" name="password" id="password" class="form-control" autocomplete="new-password" />
			<input type="hidden" name="old_password" id="old_password" value="<?php if($mode == 'edit') echo $validation->db_field_validate($userRow['password']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="confirm_password">Confirm Password *</label>
		</div>
		<div class="col-sm-9">
			<input type="password" name="confirm_password" id="confirm_password" class="form-control" autocomplete="new-password" onBlur="passwordmatch();" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="display_name">Display Name *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="display_name" id="display_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($userRow['display_name']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="email">Email</label>
		</div>
		<div class="col-sm-9">
			<input type="email" name="email" id="email" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($userRow['email']); ?>">
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="imgName">Profile Picture</label>
		</div>
		<div class="col-sm-9">
			<input type="file" name="imgName" id="imgName">
			<input type="hidden" name="old_imgName" id="old_imgName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($userRow['imgName']); ?>" />
			<?php if($mode == 'edit' and $userRow['imgName'] != "") { ?>
				<div class="mt-2 links">
					<img src="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($userRow['imgName']); ?>" title="<?php echo $validation->db_field_validate($userRow['imgName']); ?>" class="img-responsive mh-51" /><br>
					<a href="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($userRow['imgName']); ?>" target="_blank">Click to Download</a> | <a href="user_master_form.php?mode=edit&userid=<?php echo $userid; ?>&q=imgdel" onClick="return del();">Delete</a>
				</div>
			<?php } ?>
			<em class="d-block mt-1">File should be Image and size under <?php echo $validation->convertToReadableSize($configRow['image_maxsize']); ?><br>Image extension should be .jpg, .jpeg, .png, .gif</em>
		</div>
	</div>
	
	<h6 class="mb-4 mt-4">User Permissions</h6>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="per_read">Read</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="per_read" id="per_read" <?php if($mode == 'edit') { if($userRow['per_read'] == "1") echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="per_write">Write</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="per_write" id="per_write" <?php if($mode == 'edit') { if($userRow['per_write'] == "1") echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="per_update">Update</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="per_update" id="per_update" <?php if($mode == 'edit') { if($userRow['per_update'] == "1") echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-4">
		<div class="col-sm-3">
			<label for="per_delete">Delete</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="per_delete" id="per_delete" <?php if($mode == 'edit') { if($userRow['per_delete'] == "1") echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Status</label>
		</div>
		<div class="col-sm-9">
			<select name="status" id="status" class="form-control">
				<option value="active" <?php if($mode == 'edit') { if($validation->db_field_validate($userRow['status']) == "active") echo "selected"; } ?>>Active</option>
				<option value="inactive" <?php if($mode == 'edit') { if($validation->db_field_validate($userRow['status']) == "inactive") echo "selected"; } ?>>Inactive</option>
			</select>
		</div>
	</div>
	
	<?php if($mode == 'edit') { ?>
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>User's IP Address</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($userRow['user_ip']); ?></p>
			<input type="hidden" name="user_ip" value="<?php if($mode == 'edit') echo $validation->db_field_validate($userRow['user_ip']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Modification Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($userRow['modifydate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($userRow['modifydate'])." at ".$validation->time_format_custom($userRow['modifytime']); ?></p>
			<?php } ?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Creation Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($userRow['createdate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($userRow['createdate'])." at ".$validation->time_format_custom($userRow['createtime']); ?></p>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
	
	<div class="row mt-4 mb-4">
		<div class="col-sm-12">
			<?php
			if($mode == "insert")
			{
			?>
				<button type="submit" class="btn btn-default btn-sm mr-2 btn_submit"><i class="fa fa-arrow-circle-right"></i>&nbsp;&nbsp;Add</button>
				<button type="reset" class="btn btn-default btn-sm btn_delete"><i class="fas fa-sync-alt"></i>&nbsp;&nbsp;Reset</button>
			<?php
			}
			elseif($mode == "edit")
			{
			?>
				<button type="submit" name="submit" class="btn btn-default btn-sm mr-2 btn_submit"><i class="fas fa-save"></i>&nbsp;&nbsp;Update</button>
				<?php if($_SESSION['per_delete'] == "1") { ?>
					<a HREF="user_master_actions.php?q=del&userid=<?php echo $userRow['userid']; ?>" class="btn btn-default btn-sm btn_delete" onClick="return del();"><i class="fas fa-trash"></i>&nbsp;&nbsp;Delete</a>
				<?php } ?>
			<?php
			}
			?>
		</div>
	</div>
</div>
</form>
</div>
</div>
</div>
</body>
</html>