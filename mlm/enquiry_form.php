<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "enquiry";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: enquiry_view.php");
	exit();
}

if($mode == "edit")
{
	$enquiryid = $validation->urlstring_validate($_GET['enquiryid']);
	$enquiryQueryResult = $db->view('*', 'mlm_enquiries', 'enquiryid', "and regid='$regid' and enquiryid = '$enquiryid'");
	$enquiryRow = $enquiryQueryResult['result'][0];
}
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
		<h1 CLASS="page-header"><?php if($mode == "insert") echo "Add New"; else echo "Update"; ?> Enquiry <?php if($mode == 'edit') { ?>(<a href="enquiry_reply_view.php?enquiryid=<?php echo $validation->db_field_validate($enquiryRow['enquiryid']); ?>" style="font-size:23px;">Check Replies</a>)<?php } ?></h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "enquiry_form_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "enquiry_form_inter.php?mode=$mode&enquiryid=$enquiryid";
													break;
													
													default : echo "enquiry_form_inter.php";
												}
												?>" enctype="multipart/form-data">

<input type="hidden" name="user_ip" value="<?php if($mode == 'edit') echo $validation->db_field_validate($enquiryRow['user_ip']); ?>" />

<div class="form-rows-custom mt-3">
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="first_name">First Name *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="first_name" id="first_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($enquiryRow['first_name']); else echo $_SESSION['mlm_first_name']; ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="last_name">Last Name</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="last_name" id="last_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($enquiryRow['last_name']); else echo $_SESSION['mlm_last_name']; ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="email">Email *</label>
		</div>
		<div class="col-sm-9">
			<input type="email" name="email" id="email" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($enquiryRow['email']); else echo $_SESSION['mlm_email']; ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="mobile">Mobile No.</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="mobile" id="mobile" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($enquiryRow['mobile']); else echo $_SESSION['mlm_mobile']; ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="subject">Subject</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="subject" id="subject" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($enquiryRow['subject']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="message">Message</label>
		</div>
		<div class="col-sm-9">
			<textarea name="message" id="message" class="form-control"><?php if($mode == 'edit') echo $validation->db_field_validate($enquiryRow['message']); ?></textarea>
		</div>
	</div>
	
	<!--<div class="row mb-3">
		<div class="col-sm-3">
			<label for="remarks">Remarks</label>
		</div>
		<div class="col-sm-9">
			<textarea name="remarks" id="remarks" class="form-control"><?php if($mode == 'edit') echo $validation->db_field_validate($enquiryRow['remarks']); ?></textarea>
		</div>
	</div>-->
	
	<?php if($mode == 'edit') { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Creation Date & Time</label>
			</div>
			<div class="col-sm-9">
				<?php if($enquiryRow['createdate'] != "") { ?>
					<p class="text"><?php echo $validation->date_format_custom($enquiryRow['createdate'])." at ".$validation->time_format_custom($enquiryRow['createtime']); ?></p>
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