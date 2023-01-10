<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "register";


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
		<h1 CLASS="page-header">Send WhatsApp Msg</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="send_msg_inter.php">

<div class="form-rows-custom mt-3">
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="members">Users</label>
		</div>
		<div class="col-sm-9">
			<select name="members[]" id="members" class="form-control chosen" multiple>
                <?php
                    $memberResult = $db->view("*", "rb_registrations", 'regid', " and mobile", "regid asc");

					if($memberResult['num_rows'] >= 1){
						foreach($memberResult['result'] as $memberRow){
					?>
					<option value="<?= $memberRow['regid'];?>"><?= $memberRow['membership_id'] .  " - "  . $memberRow['first_name'];?></option>
				<?php } }?>
            </select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="remarks">Message</label>
		</div>
		<div class="col-sm-9">
			<textarea name="remarks" id="remarks" class="form-control"><?php if($mode == 'edit') echo $validation->db_field_validate($registerRow['remarks']); ?></textarea>
		</div>
	</div>
	
	
	
    <button type="submit" class="btn btn-default btn-sm mr-2 btn_submit"><i class="fa fa-arrow-circle-right"></i>&nbsp;&nbsp;Add</button>
    <button type="reset" class="btn btn-default btn-sm btn_delete"><i class="fas fa-sync-alt"></i>&nbsp;&nbsp;Reset</button>
	
</div>
</form>
</div>
</div>
</div>

</body>
</html>