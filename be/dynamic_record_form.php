<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "dynamic_records";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: dynamic_record_view.php");
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
	$recordid = $validation->urlstring_validate($_GET['recordid']);
	$recordQueryResult = $db->view('*', 'rb_dynamic_records', 'recordid', "and recordid = '$recordid'");
	$recordRow = $recordQueryResult['result'][0];
	
	$userid = $recordRow['userid'];
	$userQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid}'");
	$userRow = $userQueryResult['result'][0];
	
	$userid_updt = $recordRow['userid_updt'];
	$userupdtQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid_updt}'");
	$userupdtRow = $userupdtQueryResult['result'][0];
}
else
{
	$max_order = $db->get_maxorder('rb_dynamic_records') + 1;
}

if(isset($_GET['q']))
{
	$q = $validation->urlstring_validate($_GET['q']);
	if($q == "imgdel")
	{
		$imgName = $validation->urlstring_validate($_GET['imgName']);
		$delresult = $media->multiple_filedeletion('rb_dynamic_records', 'recordid', $recordid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC, $imgName);
		if($delresult)
		{
			$_SESSION['success_msg'] = "Image has been deleted Successfully!!!";
			header("Location: dynamic_record_form.php?mode=edit&recordid=$recordid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: dynamic_record_form.php?mode=edit&recordid=$recordid");
			exit();
		}
	}
	
	if($q == "filedel")
	{
		$delresult = $media->filedeletion('rb_dynamic_records', 'recordid', $recordid, 'fileName', FILE_LOC);
		if($delresult)
		{
			$_SESSION['success_msg'] = "File has been deleted Successfully!!!";
			header("Location: dynamic_record_form.php?mode=edit&recordid=$recordid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: dynamic_record_form.php?mode=edit&recordid=$recordid");
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
		<h1 CLASS="page-header"><?php if($mode == "insert") echo "Add New"; else echo "Update"; ?> Dynamic Record</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "dynamic_record_form_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "dynamic_record_form_inter.php?mode=$mode&recordid=$recordid";
													break;
													
													default : echo "dynamic_record_form_inter.php";
												}
												?>" enctype="multipart/form-data">

<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="pageid">Select Page *</label>
		</div>
		<div class="col-sm-9">
			<select NAME="pageid" CLASS="form-control" ID="pageid" required >
				<option VALUE="">--select--</option>
				<?php
				$pageQueryResult = $db->view('pageid,title', 'rb_dynamic_pages', 'pageid', "and status='active'", 'title asc');
				foreach($pageQueryResult['result'] as $pageRow)
				{
				?>
					<option VALUE="<?php echo $validation->db_field_validate($pageRow['pageid']); ?>" <?php if($mode == 'edit') { if($pageRow['pageid'] == $recordRow['pageid']) echo "selected"; } ?>><?php echo $validation->db_field_validate($pageRow['title']); ?></option>
				<?php
				}
				?>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="title"><strong>Title *</strong></label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="title" id="title" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['title']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="title_id">Title ID <em>(Optional)</em></label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="title_id" id="title_id" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['title_id']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="tagline">Tagline</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="tagline" id="tagline" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['tagline']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="price">Price</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="price" id="price" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['price']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="order_custom">Order</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="order_custom" id="order_custom" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['order_custom']); else echo $max_order; ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="url">URL</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="url" id="url" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['url']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="url_target">URL Target</label>
		</div>
		<div class="col-sm-9">
			<select NAME="url_target" ID="url_target" CLASS="form-control">
				<option VALUE="_self" <?php if($mode == 'edit') { if($validation->db_field_validate($recordRow['url_target']) == "_self") echo "selected"; } ?>>Open in Same Tab</option>
				<option VALUE="_blank" <?php if($mode == 'edit') { if($validation->db_field_validate($recordRow['url_target']) == "_blank") echo "selected"; } ?>>Open in New Tab</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_title">Meta Title</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_title" id="meta_title" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['meta_title']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_keywords">Meta Keywords</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['meta_keywords']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_description">Meta Description</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_description" id="meta_description" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['meta_description']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-12">
			<label for="description">Description</label>
		</div>
		<div class="col-sm-12">
			<button TYPE="button" CLASS="btn btn-default btn-sm" id="image_model_button" onClick="document.getElementById('image_upper_text').style.display='none'; document.getElementById('userImage').value='';"><i class="fa fa-image" aria-hidden="true"></i> Add Image</button>
			<textarea id="description" name="description" class="tinymce"><?php if($mode == 'edit') echo $recordRow['description']; ?></textarea>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="imgName">Upload Image(s)</label>
		</div>
		<div class="col-sm-9">
			<input type="file" name="imgName[]" id="imgName" multiple />
			<input type="hidden" name="old_imgName" id="old_imgName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['imgName']); ?>" />
			<?php if($mode == 'edit' and $recordRow['imgName'] != "") { ?>
				<div class="mt-2 links">
					<?php
					$imgName = $recordRow['imgName'];
					$imgName = explode(" | ", $imgName);
					foreach($imgName as $img)
					{
					?>
						<div class="image-preview">
							<a href="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($img); ?>" target="_blank"><img src="<?php echo IMG_THUMB_LOC; echo $validation->db_field_validate($img); ?>" title="<?php echo $validation->db_field_validate($img); ?>" alt="<?php echo $validation->db_field_validate($img); ?>" class="image-preview-img" /></a>
							<br />
							<a href="dynamic_record_form.php?mode=edit&recordid=<?php echo $recordid; ?>&imgName=<?php echo $img; ?>&q=imgdel" class="del_link" onClick="return del();">Delete</a>
						</div>
					<?php
					}
					?>
				</div>
			<?php } ?>
			<em class="d-block mt-1">File should be Image and size under <?php echo $validation->convertToReadableSize($configRow['image_maxsize']); ?><br>Image extension should be .jpg, .jpeg, .png, .gif<br>Hold "Ctrl" key for multi-selection</em>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="fileName">Upload File</label>
		</div>
		<div class="col-sm-9">
			<input type="file" name="fileName" id="fileName">
			<input type="hidden" name="old_fileName" id="old_fileName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['fileName']); ?>" />
			<?php if($mode == 'edit' and $recordRow['fileName'] != "") { ?>
				<div class="mt-2 links">
					<a href="<?php echo FILE_LOC; echo $validation->db_field_validate($recordRow['fileName']); ?>" target="_blank">Click to Download</a> | <a href="dynamic_record_form.php?mode=edit&recordid=<?php echo $recordid; ?>&q=filedel" onClick="return del();">Delete</a>
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
			<input type="checkbox" name="priority" id="priority" <?php if($mode == 'edit') { if($recordRow['priority'] == "1") echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Status *</label>
		</div>
		<div class="col-sm-9">
			<select name="status" id="status" class="form-control" required >
				<option value="active" <?php if($mode == 'edit') { if($validation->db_field_validate($recordRow['status']) == "active") echo "selected"; } ?>>Active</option>
				<option value="inactive" <?php if($mode == 'edit') { if($validation->db_field_validate($recordRow['status']) == "inactive") echo "selected"; } ?>>Inactive</option>
			</select>
		</div>
	</div>
	
	<?php if($mode == 'edit') { ?>
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Author</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><a href="dynamic_record_view.php?userid=<?php echo $userid; ?>"><?php echo $validation->db_field_validate($userRow['display_name']); ?></a></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Author (Modified By)</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><a href="dynamic_record_view.php?userid=<?php echo $userid_updt; ?>"><?php echo $validation->db_field_validate($userupdtRow['display_name']); ?></a></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>User's IP Address</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($recordRow['user_ip']); ?></p>
			<input type="hidden" name="user_ip" value="<?php if($mode == 'edit') echo $validation->db_field_validate($recordRow['user_ip']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Modification Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($recordRow['modifydate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($recordRow['modifydate'])." at ".$validation->time_format_custom($recordRow['modifytime']); ?></p>
			<?php } ?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Creation Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($recordRow['createdate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($recordRow['createdate'])." at ".$validation->time_format_custom($recordRow['createtime']); ?></p>
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
					<a HREF="dynamic_record_actions.php?q=del&recordid=<?php echo $recordRow['recordid']; ?>" class="btn btn-default btn-sm btn_delete" onClick="return del();"><i class="fas fa-trash"></i>&nbsp;&nbsp;Delete</a>
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