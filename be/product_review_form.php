<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "product_reviews";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: product_review_view.php");
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
	$reviewid = $validation->urlstring_validate($_GET['reviewid']);
	$reviewQueryResult = $db->view('*', 'rb_products_reviews', 'reviewid', "and reviewid = '$reviewid'");
	$reviewRow = $reviewQueryResult['result'][0];
	
	$regid = $reviewRow['regid'];
	$registerQueryResult = $db->view("first_name,last_name", "rb_registrations", "regid", "and regid='{$regid}'");
	$registerRow = $registerQueryResult['result'][0];
	
	$userid = $reviewRow['userid'];
	$userQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid}'");
	$userRow = $userQueryResult['result'][0];
	
	$productid = $reviewRow['productid'];
	$productQueryResult = $db->view("productid,title", "rb_products", "productid", "and productid='{$productid}'");
	$productRow = $productQueryResult['result'][0];
	
	if($reviewRow['read_check'] == 0)
	{
		$update = $db->update("rb_products_reviews", array('read_check'=>1), array('reviewid'=>$reviewid));
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
		<h1 CLASS="page-header"><?php if($mode == "insert") echo "Add New"; else echo "Update"; ?> Product Review</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "product_review_form_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "product_review_form_inter.php?mode=$mode&reviewid=$reviewid";
													break;
													
													default : echo "product_review_form_inter.php";
												}
												?>" enctype="multipart/form-data">

<div class="form-rows-custom mt-3">
	<?php if($reviewRow['productid'] != "" and $reviewRow['productid'] != "0") { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Product</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><a href="product_form.php?mode=edit&productid=<?php echo $reviewRow['productid']; ?>"><?php echo $validation->db_field_validate($productRow['title']); ?></a></p>
			</div>
		</div>
	<?php } ?>
	<?php if($reviewRow['regid'] != "" and $reviewRow['regid'] != "0") { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>User</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><a href="register_form.php?mode=edit&regid=<?php echo $reviewRow['regid']; ?>"><?php echo $validation->db_field_validate($registerRow['first_name'].' '.$registerRow['last_name']); ?></a></p>
			</div>
		</div>
	<?php } ?>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="name">Name *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="name" id="name" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($reviewRow['name']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="email">Email *</label>
		</div>
		<div class="col-sm-9">
			<input type="email" name="email" id="email" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($reviewRow['email']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="email">Ratings *</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="ratings" id="ratings" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($reviewRow['ratings']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="message">Message</label>
		</div>
		<div class="col-sm-9">
			<textarea name="message" id="message" class="form-control"><?php if($mode == 'edit') echo $validation->db_field_validate($reviewRow['message']); ?></textarea>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Status *</label>
		</div>
		<div class="col-sm-9">
			<select name="status" id="status" class="form-control" required >
				<option value="active" <?php if($mode == 'edit') { if($validation->db_field_validate($reviewRow['status']) == "active") echo "selected"; } ?>>Active</option>
				<option value="inactive" <?php if($mode == 'edit') { if($validation->db_field_validate($reviewRow['status']) == "inactive") echo "selected"; } ?>>Inactive</option>
			</select>
		</div>
	</div>
	
	<?php if($mode == 'edit') { ?>
	<?php if($reviewRow['userid'] != "" and $reviewRow['userid'] != "0") { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Modified By</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><a href="product_review_view.php?userid=<?php echo $userid; ?>"><?php echo $validation->db_field_validate($userRow['display_name']); ?></a></p>
			</div>
		</div>
	<?php } ?>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>User's IP Address</label>
		</div>
		<div class="col-sm-9">
			<p class="text"><?php echo $validation->db_field_validate($reviewRow['user_ip']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Modification Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($reviewRow['modifydate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($reviewRow['modifydate'])." at ".$validation->time_format_custom($reviewRow['modifytime']); ?></p>
			<?php } ?>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Creation Date & Time</label>
		</div>
		<div class="col-sm-9">
			<?php if($reviewRow['createdate'] != "") { ?>
				<p class="text"><?php echo $validation->date_format_custom($reviewRow['createdate'])." at ".$validation->time_format_custom($reviewRow['createtime']); ?></p>
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
					<a HREF="product_review_actions.php?q=del&reviewid=<?php echo $reviewRow['reviewid']; ?>" class="btn btn-default btn-sm btn_delete" onClick="return del();"><i class="fas fa-trash"></i>&nbsp;&nbsp;Delete</a>
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