<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "coupon";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: coupon_view.php");
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
	$couponid = $validation->urlstring_validate($_GET['couponid']);
	$couponQueryResult = $db->view('*', 'rb_coupons', 'couponid', "and couponid = '$couponid'");
	$couponRow = $couponQueryResult['result'][0];
	
	$userid = $couponRow['userid'];
	$userQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid}'");
	$userRow = $userQueryResult['result'][0];
	
	$userid_updt = $couponRow['userid_updt'];
	$userupdtQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid_updt}'");
	$userupdtRow = $userupdtQueryResult['result'][0];
}
else
{
	$max_order = $db->get_maxorder('rb_coupons') + 1;
}

if(isset($_GET['q']))
{
	$q = $validation->urlstring_validate($_GET['q']);
	if($q == "imgdel")
	{
		$delresult = $media->filedeletion('rb_coupons', 'couponid', $couponid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
		if($delresult)
		{
			$_SESSION['success_msg'] = "Image has been deleted Successfully!!!";
			header("Location: coupon_form.php?mode=edit&couponid=$couponid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: coupon_form.php?mode=edit&couponid=$couponid");
			exit();
		}
	}
	
	if($q == "filedel")
	{
		$delresult = $media->filedeletion('rb_coupons', 'couponid', $couponid, 'fileName', FILE_LOC);
		if($delresult)
		{
			$_SESSION['success_msg'] = "File has been deleted Successfully!!!";
			header("Location: coupon_form.php?mode=edit&couponid=$couponid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: coupon_form.php?mode=edit&couponid=$couponid");
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
</head>
<body>
<div ID="wrapper">
<?php include_once("inc_header.php"); ?>
<div ID="page-wrapper">
<div CLASS="container-fluid">
<div CLASS="row">
	<div CLASS="col-lg-12">
		<h1 CLASS="page-header"><?php if($mode == "insert") echo "Add New"; else echo "Update"; ?> Coupon</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "coupon_form_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "coupon_form_inter.php?mode=$mode&couponid=$couponid";
													break;
													
													default : echo "coupon_form_inter.php";
												}
												?>" enctype="multipart/form-data">

<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="coupon_code"><strong>Coupon Code *</strong></label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="coupon_code" id="coupon_code" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['coupon_code']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="discount">Discount *</label>
		</div>
		<div class="col-sm-9">
			<div class="form-inline">
				<input type="number" name="discount" id="discount" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['discount']); ?>" required /> %
			</div>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="min_price">Minimum Price *</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="min_price" id="min_price" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['min_price']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="max_discount">Maximum Discount *</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="max_discount" id="max_discount" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['max_discount']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="expiry_date">Expiry Date *</label>
		</div>
		<div class="col-sm-9">
			<input type="date" name="expiry_date" id="expiry_date" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['expiry_date']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="currency_code">Currency Code *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="currency_code" id="currency_code" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['currency_code']); else echo "INR"; ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="order_custom">Order</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="order_custom" id="order_custom" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['order_custom']); else echo $max_order; ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-12">
			<label for="description">Terms & Conditions</label>
		</div>
		<div class="col-sm-12">
			<button TYPE="button" CLASS="btn btn-default btn-sm" id="image_model_button" onClick="document.getElementById('image_upper_text').style.display='none'; document.getElementById('userImage').value='';"><i class="fa fa-image" aria-hidden="true"></i> Add Image</button>
			<textarea id="description" name="description" class="tinymce"><?php if($mode == 'edit') echo $couponRow['description']; ?></textarea>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="imgName">Upload Image</label>
		</div>
		<div class="col-sm-9">
			<input type="file" name="imgName" id="imgName">
			<input type="hidden" name="old_imgName" id="old_imgName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['imgName']); ?>" />
			<?php if($mode == 'edit' and $couponRow['imgName'] != "") { ?>
				<div class="mt-2 links">
					<img src="<?php echo IMG_THUMB_LOC; echo $validation->db_field_validate($couponRow['imgName']); ?>" title="<?php echo $validation->db_field_validate($couponRow['imgName']); ?>" class="img-responsive mh-51" /><br>
					<a href="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($couponRow['imgName']); ?>" target="_blank">Click to Download</a> | <a href="coupon_form.php?mode=edit&couponid=<?php echo $couponid; ?>&q=imgdel" onClick="return del();">Delete</a>
				</div>
			<?php } ?>
			<em class="d-block mt-1">File should be Image and size under <?php echo $validation->convertToReadableSize($configRow['image_maxsize']); ?><br>Image extension should be .jpg, .jpeg, .png, .gif</em>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="fileName">Upload File</label>
		</div>
		<div class="col-sm-9">
			<input type="file" name="fileName" id="fileName">
			<input type="hidden" name="old_fileName" id="old_fileName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['fileName']); ?>" />
			<?php if($mode == 'edit' and $couponRow['fileName'] != "") { ?>
				<div class="mt-2 links">
					<a href="<?php echo FILE_LOC; echo $validation->db_field_validate($couponRow['fileName']); ?>" target="_blank">Click to Download</a> | <a href="coupon_form.php?mode=edit&couponid=<?php echo $couponid; ?>&q=filedel" onClick="return del();">Delete</a>
				</div>
			<?php } ?>
			<em class="d-block mt-1">File size under <?php echo $validation->convertToReadableSize($configRow['file_maxsize']); ?><br>File extension should be .pdf, .docx, .doc, .xlsx, .csv, .zip</em>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="priority">Priority ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="priority" id="priority" <?php if($mode == 'edit') { if($couponRow['priority'] == "1") echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Status *</label>
		</div>
		<div class="col-sm-9">
			<select name="status" id="status" class="form-control" required >
				<option value="active" <?php if($mode == 'edit') { if($validation->db_field_validate($couponRow['status']) == "active") echo "selected"; } ?>>Active</option>
				<option value="inactive" <?php if($mode == 'edit') { if($validation->db_field_validate($couponRow['status']) == "inactive") echo "selected"; } ?>>Inactive</option>
			</select>
		</div>
	</div>
	
	<?php if($mode == 'edit') { ?>
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Author</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><a href="coupon_view.php?userid=<?php echo $userid; ?>"><?php echo $validation->db_field_validate($userRow['display_name']); ?></a></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Author (Modified By)</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><a href="coupon_view.php?userid=<?php echo $userid_updt; ?>"><?php echo $validation->db_field_validate($userupdtRow['display_name']); ?></a></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>User's IP Address</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($couponRow['user_ip']); ?></p>
			<input type="hidden" name="user_ip" value="<?php if($mode == 'edit') echo $validation->db_field_validate($couponRow['user_ip']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Modification Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($couponRow['modifydate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($couponRow['modifydate'])." at ".$validation->time_format_custom($couponRow['modifytime']); ?></p>
			<?php } ?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Creation Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($couponRow['createdate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($couponRow['createdate'])." at ".$validation->time_format_custom($couponRow['createtime']); ?></p>
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
					<a HREF="coupon_actions.php?q=del&couponid=<?php echo $couponRow['couponid']; ?>" class="btn btn-default btn-sm btn_delete" onClick="return del();"><i class="fas fa-trash"></i>&nbsp;&nbsp;Delete</a>
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

<div ID="image_model" CLASS="modal">
	<div CLASS="modal-content">
		<div class="row">
			<div class="col-10">
				<div class="image_modal_heading"><i class="fa fa-image" aria-hidden="true"></i> Upload Image</div>
			</div>
			<div class="col-2">
				<div CLASS="image_close_button">&times;</div>
			</div>
		</div>
		<div STYLE="background:; padding:3%;">
			<p align="center">Select/Upload files from your local machine to server.</p>
			<div ID="drop-area"><p CLASS="drop-text" STYLE="margin-top:50px;">
				<p class="image_upper_text" id="image_upper_text"><i class="fas fa-check" aria-hidden="true" style="color: #0BC414;"></i> Your Image has been Uploaded. Upload more pictures!!!</p>
				<img src="images/Loading_icon.gif" class="image_model_loader" style="display:none;" />
				<p class="image_lower_text"><form name="uploadForm" id="uploadForm">
				<input type="file" name="userImage" class="d-none" onChange="uploadimage(this);" id="userImage">
				<label for="userImage" class="file_design"><i class="fa fa-image" aria-hidden="true"></i> Select File</label>&nbsp; or Drag it Here
				</form></p>
			</p></div>
			<br>
			<button TYPE="BUTTON" ID="image_close" CLASS="btn btn-success btn-sm">Done</button>
		</div>
	</div>
</div>

</body>
</html>