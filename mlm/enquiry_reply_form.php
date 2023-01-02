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
	header("Location: enquiry_reply_view.php");
	exit();
}

if($mode == "edit")
{
	$replyid = $validation->urlstring_validate($_GET['replyid']);
	$replyQueryResult = $db->view('*', 'mlm_enquiries_replies', 'replyid', "and regid='$regid' and replyid = '$replyid'");
	$replyRow = $replyQueryResult['result'][0];
	
	$enquiryid = $replyRow['enquiryid'];
	$enquiryQueryResult = $db->view('*', 'mlm_enquiries', 'enquiryid', "and regid='$regid' and enquiryid = '$enquiryid'");
	$enquiryRow = $enquiryQueryResult['result'][0];
}
else
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
		<h1 CLASS="page-header"><?php if($mode == "insert") echo "Add"; else echo "Update"; ?> Reply</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "enquiry_reply_form_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "enquiry_reply_form_inter.php?mode=$mode&replyid=$replyid";
													break;
													
													default : echo "enquiry_reply_form_inter.php";
												}
												?>" enctype="multipart/form-data">

<input type="hidden" name="enquiryid" value="<?php echo $enquiryid; ?>" />
<input type="hidden" name="regid" value="<?php echo $regid; ?>" />
<input type="hidden" name="posted_by" value="Member" />
<input type="hidden" name="user_ip" value="<?php if($mode == 'edit') echo $validation->db_field_validate($replyRow['user_ip']); ?>" />

<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Enquiry</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($enquiryRow['message']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-12">
			<label for="message">Message</label>
		</div>
		<div class="col-sm-12">
			<button TYPE="button" CLASS="btn btn-default btn-sm" id="image_model_button" onClick="document.getElementById('image_upper_text').style.display='none'; document.getElementById('userImage').value='';"><i class="fa fa-image" aria-hidden="true"></i> Add Image</button>
			<textarea id="message" name="message" class="tinymce"><?php if($mode == 'edit') echo $replyRow['message']; ?></textarea>
		</div>
	</div>
	
	<?php if($mode == 'edit') { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Creation Date & Time</label>
			</div>
			<div class="col-sm-9">
				<?php if($replyRow['createdate'] != "") { ?>
					<p class="text"><?php echo $validation->date_format_custom($replyRow['createdate'])." at ".$validation->time_format_custom($replyRow['createtime']); ?></p>
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