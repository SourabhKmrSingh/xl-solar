<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "";

echo $validation->admin_permission();

if($configRow['configid'] != "")
{
	$mode = "edit";
	$configid = $validation->input_validate($configRow['configid']);
}
else
{
	$mode = "insert";
}

if($mode == "edit")
{
	echo $validation->update_permission();
}
else
{
	echo $validation->write_permission();
}

if(isset($_GET['q']))
{
	$q = $validation->urlstring_validate($_GET['q']);
	if($q == "imgdel")
	{
		$delresult = $media->filedeletion('rb_config', 'configid', $configid, 'logo', IMG_MAIN_LOC);
		if($delresult)
		{
			$_SESSION['success_msg'] = "Image has been deleted Successfully!!!";
			header("Location: config.php?mode=edit&configid=$configid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: config.php?mode=edit&configid=$configid");
			exit();
		}
	}
	
	if($q == "imgdel2")
	{
		$delresult = $media->filedeletion('rb_config', 'configid', $configid, 'favicon', IMG_MAIN_LOC);
		if($delresult)
		{
			$_SESSION['success_msg'] = "Image has been deleted Successfully!!!";
			header("Location: config.php?mode=edit&configid=$configid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: config.php?mode=edit&configid=$configid");
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
		<h1 CLASS="page-header">Configuration</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "config_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "config_inter.php?mode=$mode&configid=$configid";
													break;
													
													default : echo "config_inter.php";
												}
												?>" enctype="multipart/form-data">

<div class="form-rows-custom mt-3">
	<h5 class="mb-4">General</h5>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="cms_title">CMS Title *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="cms_title" id="cms_title" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['cms_title']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="cms_url">CMS Address (URL) *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="cms_url" id="cms_url" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['cms_url']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_title">Site Title *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_title" id="meta_title" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['meta_title']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_keywords">Site Keywords</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['meta_keywords']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_description">Site Description</label>
		</div>
		<div class="col-sm-9">
			<textarea name="meta_description" id="meta_description" class="form-control"><?php if($mode == 'edit') echo $validation->db_field_validate($configRow['meta_description']); ?></textarea>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="site_url">Site Address (URL) *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="site_url" id="site_url" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['site_url']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="site_url_extension">Site URL Extension *</label>
		</div>
		<div class="col-sm-9">
			<select NAME="site_url_extension" ID="site_url_extension" CLASS="form-control" required>
				<option VALUE="" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['site_url_extension']) == "") echo "selected"; } ?>>--select--</option>
				<option VALUE=".php" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['site_url_extension']) == ".php") echo "selected"; } ?>>.php</option>
				<option VALUE="/" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['site_url_extension']) == "/") echo "selected"; } ?>>/</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="script">Script</label>
		</div>
		<div class="col-sm-9">
			<textarea name="script" id="script" class="form-control"><?php if($mode == 'edit') echo $validation->db_field_validate($configRow['script']); ?></textarea>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="style">CSS Style</label>
		</div>
		<div class="col-sm-9">
			<textarea name="style" id="style" class="form-control"><?php if($mode == 'edit') echo $validation->db_field_validate($configRow['style']); ?></textarea>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="logo">Logo</label>
		</div>
		<div class="col-sm-9">
			<input type="file" name="logo" id="logo">
			<input type="hidden" name="old_logo" id="old_logo" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['logo']); ?>" />
			<?php if($mode == 'edit' and $configRow['logo'] != "") { ?>
				<div class="mt-2 links">
					<img src="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($configRow['logo']); ?>" title="<?php echo $validation->db_field_validate($configRow['logo']); ?>" class="img-responsive mh-51" /><br>
					<a href="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($configRow['logo']); ?>" target="_blank">Click to Download</a> | <a href="config.php?mode=edit&configid=<?php echo $configid; ?>&q=imgdel" onClick="return del();">Delete</a>
				</div>
			<?php } ?>
			<em class="d-block mt-1">File should be Image and size under <?php echo $validation->convertToReadableSize($configRow['image_maxsize']); ?><br>Image extension should be .jpg, .jpeg, .png, .gif</em>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="favicon">Favicon</label>
		</div>
		<div class="col-sm-9">
			<input type="file" name="favicon" id="favicon">
			<input type="hidden" name="old_favicon" id="old_favicon" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['favicon']); ?>" />
			<?php if($mode == 'edit' and $configRow['favicon'] != "") { ?>
				<div class="mt-2 links">
					<img src="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($configRow['favicon']); ?>" title="<?php echo $validation->db_field_validate($configRow['favicon']); ?>" class="img-responsive mh-51" /><br>
					<a href="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($configRow['favicon']); ?>" target="_blank">Click to Download</a> | <a href="config.php?mode=edit&configid=<?php echo $configid; ?>&q=imgdel2" onClick="return del();">Delete</a>
				</div>
			<?php } ?>
			<em class="d-block mt-1">File should be Image and size under <?php echo $validation->convertToReadableSize($configRow['image_maxsize']); ?><br>Image extension should be .ico format only</em>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="timezone">Timezone *</label>
		</div>
		<div class="col-sm-9">
			<?php echo $validation->timezone(($mode == 'edit') ? $validation->db_field_validate($configRow['timezone']) : ''); ?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="date_format">Date Format *</label>
		</div>
		<div class="col-sm-9">
			<select NAME="date_format" ID="date_format" CLASS="form-control" required >
				<option VALUE="" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "") echo "selected"; } ?>>--select--</option>
				<option VALUE="d/m/Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "d/m/Y") echo "selected"; } ?>>d/m/Y - <?php echo date('d/m/Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="m/d/Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "m/d/Y") echo "selected"; } ?>>m/d/Y - <?php echo date('m/d/Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="Y/m/d" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "Y/m/d") echo "selected"; } ?>>Y/m/d - <?php echo date('Y/m/d', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="d-m-Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "d-m-Y") echo "selected"; } ?>>d-m-Y - <?php echo date('d-m-Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="m-d-Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "m-d-Y") echo "selected"; } ?>>m-d-Y - <?php echo date('m-d-Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="Y-m-d" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "Y-m-d") echo "selected"; } ?>>Y-m-d - <?php echo date('Y-m-d', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="F j, Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "F j, Y") echo "selected"; } ?>>F j, Y - <?php echo date('F j, Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="j F, Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "j F, Y") echo "selected"; } ?>>j F, Y - <?php echo date('j F, Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="j-F-Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "j-F-Y") echo "selected"; } ?>>j-F-Y - <?php echo date('j-F-Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="F d, Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "F d, Y") echo "selected"; } ?>>F d, Y - <?php echo date('F d, Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="d F, Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "d F, Y") echo "selected"; } ?>>d F, Y - <?php echo date('d F, Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="d-F-Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "d-F-Y") echo "selected"; } ?>>d-F-Y - <?php echo date('d-F-Y', strtotime(date('Y-m-d'))); ?></option>
				<option VALUE="d-M-Y" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['date_format']) == "d-M-Y") echo "selected"; } ?>>d-M-Y - <?php echo date('d-M-Y', strtotime(date('Y-m-d'))); ?></option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="time_format">Time Format *</label>
		</div>
		<div class="col-sm-9">
			<select NAME="time_format" ID="time_format" CLASS="form-control" required >
				<option VALUE="" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['time_format']) == "") echo "selected"; } ?>>--select--</option>
				<option VALUE="H:i:s" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['time_format']) == "H:i:s") echo "selected"; } ?>>H:i:s - <?php echo date('H:i:s', strtotime(date('H:i:s'))); ?></option>
				<option VALUE="H:i" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['time_format']) == "H:i") echo "selected"; } ?>>H:i - <?php echo date('H:i', strtotime(date('H:i:s'))); ?></option>
				<option VALUE="g:i a" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['time_format']) == "g:i a") echo "selected"; } ?>>g:i a - <?php echo date('g:i a', strtotime(date('H:i:s'))); ?></option>
				<option VALUE="g:i A" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['time_format']) == "g:i A") echo "selected"; } ?>>g:i A - <?php echo date('g:i A', strtotime(date('H:i:s'))); ?></option>
				<option VALUE="h:i a" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['time_format']) == "h:i a") echo "selected"; } ?>>h:i a - <?php echo date('h:i a', strtotime(date('H:i:s'))); ?></option>
				<option VALUE="h:i A" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['time_format']) == "h:i A") echo "selected"; } ?>>h:i A - <?php echo date('h:i A', strtotime(date('H:i:s'))); ?></option>
				<option VALUE="g:i:s a" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['time_format']) == "g:i:s a") echo "selected"; } ?>>g:i:s a - <?php echo date('g:i:s a', strtotime(date('H:i:s'))); ?></option>
				<option VALUE="g:i:s A" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['time_format']) == "g:i:s A") echo "selected"; } ?>>g:i:s A - <?php echo date('g:i:s A', strtotime(date('H:i:s'))); ?></option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="records_perpage">Number of records per page *</label>
		</div>
		<div class="col-sm-9">
			<select NAME="records_perpage" ID="records_perpage" CLASS="form-control" required >
				<option VALUE="" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['records_perpage']) == "") echo "selected"; } ?>>--select--</option>
				<option VALUE="50" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['records_perpage']) == "50") echo "selected"; } ?>>50</option>
				<option VALUE="100" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['records_perpage']) == "100") echo "selected"; } ?>>100</option>
				<option VALUE="200" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['records_perpage']) == "200") echo "selected"; } ?>>200</option>
				<option VALUE="500" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['records_perpage']) == "500") echo "selected"; } ?>>500</option>
				<option VALUE="1000" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['records_perpage']) == "1000") echo "selected"; } ?>>1000</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="thumb_ratio">Remove the site from Google indexing</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="google_indexing" id="google_indexing" <?php if($mode == 'edit') { if($configRow['google_indexing'] == "1") echo "checked"; } ?> /> 
		</div>
	</div>
	
	<h5 class="mb-4 mt-5">Product Delivery Settings</h5>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="expected_delivery">Expected Delivery Details</label>
		</div>
		<div class="col-sm-9">
			<textarea name="expected_delivery" id="expected_delivery" class="form-control"><?php if($mode == 'edit') echo $validation->db_field_validate($configRow['expected_delivery']); ?></textarea>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="minimum_cart">Minimum Cart Value</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="minimum_cart" id="minimum_cart" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['minimum_cart']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="cart_shipping">Cart Shipping Charges</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="cart_shipping" id="cart_shipping" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['cart_shipping']); ?>" />
		</div>
	</div>
	
	<h5 class="mb-4 mt-5">Payment Settings</h5>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="min_wallet_amount">Minimum Wallet Amount</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="min_wallet_amount" id="min_wallet_amount" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['min_wallet_amount']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="redeemable_amount">Maximum Redeemable Amount</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="redeemable_amount" id="redeemable_amount" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['redeemable_amount']); ?>" />
			<em class="d-block mt-1">In Percentage</em>
		</div>
	</div>
	
	<h5 class="mb-4 mt-5">Basic Contact Information</h5>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="title">Title</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="title" id="title" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['title']); ?>" />
			<em class="d-block mt-1">For Email Title, Footer etc</em>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="email">Email-ID</label>
		</div>
		<div class="col-sm-9">
			<input type="email" name="email" id="email" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['email']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="contact_number">Contact Number(s)</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="contact_number" id="contact_number" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['contact_number']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="address">Address</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="address" id="address" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['address']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="facebook">Facebook</label>
		</div>
		<div class="col-sm-9 input-group mb-1">
			<span class="input-group-text" id="addon1"><i class="fab fa-facebook-f"></i></span>
			<input type="text" name="facebook" id="facebook" class="form-control" aria-describedby="addon1" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['facebook']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="twitter">Twitter</label>
		</div>
		<div class="col-sm-9 input-group mb-1">
			<span class="input-group-text" id="addon1"><i class="fab fa-twitter"></i></span>
			<input type="text" name="twitter" id="twitter" class="form-control" aria-describedby="addon1" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['twitter']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="youtube">YouTube</label>
		</div>
		<div class="col-sm-9 input-group mb-1">
			<span class="input-group-text" id="addon1"><i class="fab fa-youtube"></i></span>
			<input type="text" name="youtube" id="youtube" class="form-control" aria-describedby="addon1" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['youtube']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="instagram">Instagram</label>
		</div>
		<div class="col-sm-9 input-group mb-1">
			<span class="input-group-text" id="addon1"><i class="fab fa-instagram"></i></span>
			<input type="text" name="instagram" id="instagram" class="form-control" aria-describedby="addon1" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['instagram']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="linkedin">LinkedIn</label>
		</div>
		<div class="col-sm-9 input-group mb-1">
			<span class="input-group-text" id="addon1"><i class="fab fa-linkedin-in"></i></span>
			<input type="text" name="linkedin" id="linkedin" class="form-control" aria-describedby="addon1" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['linkedin']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="whatsapp">WhatsApp</label>
		</div>
		<div class="col-sm-9 input-group mb-1">
			<span class="input-group-text" id="addon1"><i class="fab fa-whatsapp"></i></span>
			<input type="text" name="whatsapp" id="whatsapp" class="form-control" aria-describedby="addon1" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['whatsapp']); ?>" />
		</div>
	</div>
	
	<h5 class="mb-4 mt-5">Email Settings</h5>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="mail_server">Mail Server</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="mail_server" id="mail_server" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['mail_server']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="mail_port">Port Number</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="mail_port" id="mail_port" class="form-control mw-120px" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['mail_port']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="mail_encryption">Encryption Type</label>
		</div>
		<div class="col-sm-9">
			<select NAME="mail_encryption" ID="mail_encryption" CLASS="form-control" >
				<option VALUE="" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['mail_encryption']) == "") echo "selected"; } ?>>--select--</option>
				<option VALUE="none" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['mail_encryption']) == "none") echo "selected"; } ?>>none</option>
				<option VALUE="tls" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['mail_encryption']) == "tls") echo "selected"; } ?>>tls</option>
				<option VALUE="ssl" <?php if($mode == 'edit') { if($validation->db_field_validate($configRow['mail_encryption']) == "ssl") echo "selected"; } ?>>ssl</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="mail_name">Name</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="mail_name" id="mail_name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['mail_name']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="mail_email">Email</label>
		</div>
		<div class="col-sm-9">
			<input type="email" name="mail_email" id="mail_email" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['mail_email']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="mail_password">Password</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="mail_password" id="mail_password" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['mail_password']); ?>" />
		</div>
	</div>
	
	<h5 class="mb-4 mt-5">Media Settings</h5>
	
	<h6 class="mb-4 mt-4">Image sizes</h6>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="thumb_width">Thumbnail size - Width</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="thumb_width" id="thumb_width" class="form-control mw-120px" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['thumb_width']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="thumb_height">Thumbnail size - Height</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="thumb_height" id="thumb_height" class="form-control mw-120px" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['thumb_height']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="thumb_ratio">Crop thumbnail to exact dimensions</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="thumb_ratio" id="thumb_ratio" <?php if($mode == 'edit') { if($configRow['thumb_ratio'] == "false") echo "checked"; } ?> /> 
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="large_width">Large size - Width</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="large_width" id="large_width" class="form-control mw-120px" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['large_width']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="large_height">Large size - Height</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="large_height" id="large_height" class="form-control mw-120px" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['large_height']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="large_ratio">Crop large image to exact dimensions</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="large_ratio" id="large_ratio" <?php if($mode == 'edit') { if($configRow['large_ratio'] == "false") echo "checked"; } ?> /> 
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="image_maxsize">Maximum Image Size <em>(in bytes)</em> *</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="image_maxsize" id="image_maxsize" class="form-control mw-120px" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['image_maxsize']); ?>" required />
		</div>
	</div>
	
	<h6 class="mb-4 mt-4">File sizes</h6>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="file_maxsize">Maximum File Size <em>(in bytes)</em> *</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="file_maxsize" id="file_maxsize" class="form-control mw-120px" value="<?php if($mode == 'edit') echo $validation->db_field_validate($configRow['file_maxsize']); ?>" required />
		</div>
	</div>
	
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