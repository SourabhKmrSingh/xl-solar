<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "pages";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: page_view.php");
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
	$pageid = $validation->urlstring_validate($_GET['pageid']);
	$pagesQueryResult = $db->view('*', 'rb_pages', 'pageid', "and pageid = '$pageid'");
	$pagesRow = $pagesQueryResult['result'][0];
	
	$userid = $pagesRow['userid'];
	$userQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid}'");
	$userRow = $userQueryResult['result'][0];
	
	$userid_updt = $pagesRow['userid_updt'];
	$userupdtQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid_updt}'");
	$userupdtRow = $userupdtQueryResult['result'][0];
}

if(isset($_GET['q']))
{
	$q = $validation->urlstring_validate($_GET['q']);
	if($q == "imgdel")
	{
		$delresult = $media->filedeletion('rb_pages', 'pageid', $pageid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
		if($delresult)
		{
			$_SESSION['success_msg'] = "Image has been deleted Successfully!!!";
			header("Location: page_form.php?mode=edit&pageid=$pageid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: page_form.php?mode=edit&pageid=$pageid");
			exit();
		}
	}
	
	if($q == "filedel")
	{
		$delresult = $media->filedeletion('rb_pages', 'pageid', $pageid, 'fileName', FILE_LOC);
		if($delresult)
		{
			$_SESSION['success_msg'] = "File has been deleted Successfully!!!";
			header("Location: page_form.php?mode=edit&pageid=$pageid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: page_form.php?mode=edit&pageid=$pageid");
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
function fetch_submenu()
{
	$.ajax({
		type: 'post',
		url: 'fetch_menu.php',
		data:
		{
			main_menu: $("#main_menu").val(),
			sub_menu: "<?php echo $pagesRow['sub_menu']; ?>",
			mode: "<?php echo $mode; ?>"
		},
		success: function(result)
		{
			$("#submenu_area").html(result);
		}
	});
}

$(document).ready(function(){
	fetch_submenu();
});
</script>
</head>
<body>
<div ID="wrapper">
<?php include_once("inc_header.php"); ?>
<div ID="page-wrapper">
<div CLASS="container-fluid">
<div CLASS="row">
	<div CLASS="col-lg-12">
		<h1 CLASS="page-header"><?php if($mode == "insert") echo "Add New"; else echo "Update"; ?> Page</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "page_form_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "page_form_inter.php?mode=$mode&pageid=$pageid";
													break;
													
													default : echo "page_form_inter.php";
												}
												?>" enctype="multipart/form-data">

<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="main_menu">Select Main Menu</label>
		</div>
		<div class="col-sm-9">
			<select NAME="main_menu" CLASS="form-control" ID="main_menu" onChange="fetch_submenu();">
				<option VALUE="">--select--</option>
				<?php
				$mainmenuQueryResult = $db->view('pageid,title', 'rb_pages', 'pageid', "and main_menu='0' and sub_menu='0' and order_custom != '0' and status='active'", 'order_custom asc');
				foreach($mainmenuQueryResult['result'] as $mainmenuRow)
				{
				?>
					<option VALUE="<?php echo $validation->db_field_validate($mainmenuRow['pageid']); ?>" <?php if($mode == 'edit') { if($mainmenuRow['pageid'] == $pagesRow['main_menu']) echo "selected"; } ?>><?php echo $validation->db_field_validate($mainmenuRow['title']); ?></option>
				<?php
				}
				?>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="sub_menu">Select Sub Menu</label>
		</div>
		<div class="col-sm-9">
			<div id="submenu_area">
				<p class="text">No Data Available!</p>
			</div>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="title"><strong>Title *</strong></label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="title" id="title" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['title']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="title_id">Title ID <em>(Optional)</em></label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="title_id" id="title_id" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['title_id']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="tagline">Tagline</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="tagline" id="tagline" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['tagline']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="order_custom">Order</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="order_custom" id="order_custom" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['order_custom']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="url">URL</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="url" id="url" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['url']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="url_target">URL Target</label>
		</div>
		<div class="col-sm-9">
			<select NAME="url_target" ID="url_target" CLASS="form-control">
				<option VALUE="_self" <?php if($mode == 'edit') { if($validation->db_field_validate($pagesRow['url_target']) == "_self") echo "selected"; } ?>>Open in Same Tab</option>
				<option VALUE="_blank" <?php if($mode == 'edit') { if($validation->db_field_validate($pagesRow['url_target']) == "_blank") echo "selected"; } ?>>Open in New Tab</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_title">Meta Title</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_title" id="meta_title" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['meta_title']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_keywords">Meta Keywords</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['meta_keywords']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_description">Meta Description</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_description" id="meta_description" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['meta_description']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-12">
			<label for="description">Description</label>
		</div>
		<div class="col-sm-12">
			<button TYPE="button" CLASS="btn btn-default btn-sm" id="image_model_button" onClick="document.getElementById('image_upper_text').style.display='none'; document.getElementById('userImage').value='';"><i class="fa fa-image" aria-hidden="true"></i> Add Image</button>
			<textarea id="description" name="description" class="tinymce"><?php if($mode == 'edit') echo $pagesRow['description']; ?></textarea>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="imgName">Upload Image</label>
		</div>
		<div class="col-sm-9">
			<input type="file" name="imgName" id="imgName">
			<input type="hidden" name="old_imgName" id="old_imgName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['imgName']); ?>" />
			<?php if($mode == 'edit' and $pagesRow['imgName'] != "") { ?>
				<div class="mt-2 links">
					<img src="<?php echo IMG_THUMB_LOC; echo $validation->db_field_validate($pagesRow['imgName']); ?>" title="<?php echo $validation->db_field_validate($pagesRow['imgName']); ?>" class="img-responsive mh-51" /><br>
					<a href="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($pagesRow['imgName']); ?>" target="_blank">Click to Download</a> | <a href="page_form.php?mode=edit&pageid=<?php echo $pageid; ?>&q=imgdel" onClick="return del();">Delete</a>
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
			<input type="hidden" name="old_fileName" id="old_fileName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['fileName']); ?>" />
			<?php if($mode == 'edit' and $pagesRow['fileName'] != "") { ?>
				<div class="mt-2 links">
					<a href="<?php echo FILE_LOC; echo $validation->db_field_validate($pagesRow['fileName']); ?>" target="_blank">Click to Download</a> | <a href="page_form.php?mode=edit&pageid=<?php echo $pageid; ?>&q=filedel" onClick="return del();">Delete</a>
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
			<input type="checkbox" name="priority" id="priority" <?php if($mode == 'edit') { if($pagesRow['priority'] == "1") echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Status *</label>
		</div>
		<div class="col-sm-9">
			<select name="status" id="status" class="form-control" required >
				<option value="active" <?php if($mode == 'edit') { if($validation->db_field_validate($pagesRow['status']) == "active") echo "selected"; } ?>>Active</option>
				<option value="inactive" <?php if($mode == 'edit') { if($validation->db_field_validate($pagesRow['status']) == "inactive") echo "selected"; } ?>>Inactive</option>
			</select>
		</div>
	</div>
	
	<?php if($mode == 'edit') { ?>
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Author</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><a href="page_view.php?userid=<?php echo $userid; ?>"><?php echo $validation->db_field_validate($userRow['display_name']); ?></a></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Author (Modified By)</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><a href="page_view.php?userid=<?php echo $userid_updt; ?>"><?php echo $validation->db_field_validate($userupdtRow['display_name']); ?></a></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>User's IP Address</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($pagesRow['user_ip']); ?></p>
			<input type="hidden" name="user_ip" value="<?php if($mode == 'edit') echo $validation->db_field_validate($pagesRow['user_ip']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Modification Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($pagesRow['modifydate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($pagesRow['modifydate'])." at ".$validation->time_format_custom($pagesRow['modifytime']); ?></p>
			<?php } ?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Creation Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($pagesRow['createdate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($pagesRow['createdate'])." at ".$validation->time_format_custom($pagesRow['createtime']); ?></p>
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
					<a HREF="page_actions.php?q=del&pageid=<?php echo $pagesRow['pageid']; ?>" class="btn btn-default btn-sm btn_delete" onClick="return del();"><i class="fas fa-trash"></i>&nbsp;&nbsp;Delete</a>
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