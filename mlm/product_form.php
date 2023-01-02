<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "product";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: product_view.php");
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
	$productid = $validation->urlstring_validate($_GET['productid']);
	$productQueryResult = $db->view('*', 'rb_products', 'productid', "and productid = '$productid'");
	$productRow = $productQueryResult['result'][0];
	
	$variantQueryResult = $db->view('*', 'rb_products_variants', 'variantid', "and productid = '$productid'", 'variantid asc');
	
	$userid = $productRow['userid'];
	$userQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid}'");
	$userRow = $userQueryResult['result'][0];
	
	$userid_updt = $productRow['userid_updt'];
	$userupdtQueryResult = $db->view("display_name", "rb_users", "userid", "and userid='{$userid_updt}'");
	$userupdtRow = $userupdtQueryResult['result'][0];
	
	if($productRow['currency_code'] == 'INR') $currency = '&#8377;'; else $currency = $validation->db_field_validate($productRow['currency_code']);
}
else
{
	$max_order = $db->get_maxorder('rb_products') + 1;
}

if(isset($_GET['q']))
{
	$q = $validation->urlstring_validate($_GET['q']);
	if($q == "imgdel")
	{
		$imgName = $validation->urlstring_validate($_GET['imgName']);
		$delresult = $media->multiple_filedeletion('rb_products', 'productid', $productid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC, $imgName);
		if($delresult)
		{
			$_SESSION['success_msg'] = "Image has been deleted Successfully!!!";
			header("Location: product_form.php?mode=edit&productid=$productid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: product_form.php?mode=edit&productid=$productid");
			exit();
		}
	}

	if($q == "variant_imgdel")
	{
		$imgName = $validation->urlstring_validate($_GET['variant_imgName']);
		$variantid = $validation->urlstring_validate($_GET['variantid']);
		$delresult = $media->multiple_filedeletion('rb_products_variants', 'variantid', $variantid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC, $imgName);
		if($delresult)
		{
			$_SESSION['success_msg'] = "Image has been deleted Successfully!!!";
			header("Location: product_form.php?mode=edit&productid=$productid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: product_form.php?mode=edit&productid=$productid");
			exit();
		}
	}
	
	if($q == "filedel")
	{
		$delresult = $media->filedeletion('rb_products', 'productid', $productid, 'fileName', FILE_LOC);
		if($delresult)
		{
			$_SESSION['success_msg'] = "File has been deleted Successfully!!!";
			header("Location: product_form.php?mode=edit&productid=$productid");
			exit();
		}
		else
		{
			$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
			header("Location: product_form.php?mode=edit&productid=$productid");
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
function fetch_subcategory()
{
	var categoryids = $("#categoryid").map(function(){
		return $(this).val();
	}).get();

	var categoryids = categoryids.toString();
	
	$.ajax({
		type: 'post',
		url: 'fetch_subcategory.php',
		data:
		{
			categoryid: categoryids,
			subcategoryid: "<?php echo $productRow['subcategoryid']; ?>",
			mode: "<?php echo $mode; ?>"
		},
		success: function(result)
		{
			$("#subcategory_area").html(result);
		}
	});
}

function fetch_subsubcategory()
{
	var categoryids = $("#categoryid").map(function(){
		return $(this).val();
	}).get();

	var subcategoryids = $("#subcategoryid").map(function(){
		return $(this).val();
	}).get();

	var categoryids = categoryids.toString();
	var subcategoryids = subcategoryids.toString();
	
	$.ajax({
		type: 'post',
		url: 'fetch_subsubcategory.php',
		data:
		{
			categoryid: categoryids,
			subcategoryid: subcategoryids,
			subsubcategoryid: "<?php echo $productRow['subsubcategoryid']; ?>",
			mode: "<?php echo $mode; ?>"
		},
		success: function(result)
		{
			$("#subsubcategory_area").html(result);
		}
	});
}

function fetch_brand()
{
	var categoryids = $("#categoryid").map(function(){
		return $(this).val();
	}).get();

	var categoryids = categoryids.toString();
	
	$.ajax({
		type: 'post',
		url: 'fetch_brand.php',
		data:
		{
			categoryid: categoryids,
			brandid: "<?php echo $productRow['brandid']; ?>",
			mode: "<?php echo $mode; ?>"
		},
		success: function(result)
		{
			$("#brand_area").html(result);
		}
	});
}

function fetch_frametype()
{
	var categoryids = $("#categoryid").map(function(){
		return $(this).val();
	}).get();

	var categoryids = categoryids.toString();
	
	$.ajax({
		type: 'post',
		url: 'fetch_frametype.php',
		data:
		{
			categoryid: categoryids,
			frametypeid: "<?php echo $productRow['frametypeid']; ?>",
			mode: "<?php echo $mode; ?>"
		},
		success: function(result)
		{
			$("#frametype_area").html(result);
		}
	});
}

function fetch_frameshape()
{
	var categoryids = $("#categoryid").map(function(){
		return $(this).val();
	}).get();

	var categoryids = categoryids.toString();
	
	$.ajax({
		type: 'post',
		url: 'fetch_frameshape.php',
		data:
		{
			categoryid: categoryids,
			frameshapeid: "<?php echo $productRow['frameshapeid']; ?>",
			mode: "<?php echo $mode; ?>"
		},
		success: function(result)
		{
			$("#frameshape_area").html(result);
		}
	});
}

function fetch_framesize()
{
	var categoryids = $("#categoryid").map(function(){
		return $(this).val();
	}).get();

	var categoryids = categoryids.toString();
	
	$.ajax({
		type: 'post',
		url: 'fetch_framesize.php',
		data:
		{
			categoryid: categoryids,
			framesizeid: "<?php echo $productRow['framesizeid']; ?>",
			mode: "<?php echo $mode; ?>"
		},
		success: function(result)
		{
			$("#framesize_area").html(result);
		}
	});
}

function fetch_framematerial()
{
	var categoryids = $("#categoryid").map(function(){
		return $(this).val();
	}).get();

	var categoryids = categoryids.toString();
	
	$.ajax({
		type: 'post',
		url: 'fetch_framematerial.php',
		data:
		{
			categoryid: categoryids,
			framematerialid: "<?php echo $productRow['framematerialid']; ?>",
			mode: "<?php echo $mode; ?>"
		},
		success: function(result)
		{
			$("#framematerial_area").html(result);
		}
	});
}

$(document).ready(function(){
	fetch_subcategory();
	setTimeout(function(){
		fetch_subsubcategory();
	}, 1000);
	fetch_brand();
	fetch_frametype();
	fetch_frameshape();
	fetch_framesize();
	fetch_framematerial();
});

function add_variant()
{
	var rowCount = $('#variants_table tbody tr').length;
	if(rowCount <= 0)
	{
		$("#variants_table tbody").html('<tr><td><input type="text" name="variant[]" id="variant" class="form-control" /></td><td><input type="text" name="sku[]" id="sku" class="form-control" /></td><td><input type="text" name="color[]" id="color" class="form-control" /></td><td><input type="text" name="width[]" id="width" class="form-control" /><em>[140 mm]</em></td><td><input type="text" name="measure[]" id="measure" class="form-control" /><em>[53-19-148]</em></td><td><input type="number" name="price[]" id="price" class="form-control" required /></td><td><input type="number" name="mrp[]" id="mrp" class="form-control" /></td><td><input type="number" name="stock_quantity[]" id="stock_quantity" class="form-control" required /></td><td><input type="file" name="variant_imgName' + rowCount + '[]" id="variant_imgName" class="w-150" multiple /></td><td class="text-center align-baseline"><a href="javascript:void(0);" class="delete_variant" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-times" aria-hidden="true"></i></a></td></tr>');
	}
	else
	{
		$("#variants_table tbody tr:last").after('<tr><td><input type="text" name="variant[]" id="variant" class="form-control" /></td><td><input type="text" name="sku[]" id="sku" class="form-control" /></td><td><input type="text" name="color[]" id="color" class="form-control" /></td><td><input type="text" name="width[]" id="width" class="form-control" /><em>[140 mm]</em></td><td><input type="text" name="measure[]" id="measure" class="form-control" /><em>[53-19-148]</em></td><td><input type="number" name="price[]" id="price" class="form-control" required /></td><td><input type="number" name="mrp[]" id="mrp" class="form-control" /></td><td><input type="number" name="stock_quantity[]" id="stock_quantity" class="form-control" required /></td><td><input type="file" name="variant_imgName' + rowCount + '[]" id="variant_imgName" class="w-150" multiple /></td><td class="text-center align-baseline"><a href="javascript:void(0);" class="delete_variant" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-times" aria-hidden="true"></i></a></td></tr>');
	}
}
function delete_variant()
{
	$("#variants_table tbody tr:last").remove();
}

$(document).ready(function(){
	$("#variants_table").on('click','.delete_variant',function(){
		$(this).parent().parent().remove();
	});
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
		<h1 CLASS="page-header"><?php if($mode == "insert") echo "Add New"; else echo "Update"; ?> Product</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="<?php 
												switch($mode)
												{
													case "insert" : echo "product_form_inter.php?mode=$mode";
													break;
													
													case "edit" : echo "product_form_inter.php?mode=$mode&productid=$productid";
													break;
													
													default : echo "product_form_inter.php";
												}
												?>" enctype="multipart/form-data">

<input type="hidden" name="old_type" value="<?php echo $validation->db_field_validate($productRow['type']); ?>" />

<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="categoryid">Select Category</label>
		</div>
		<div class="col-sm-9">
			<select NAME="categoryid[]" CLASS="form-control chosen" ID="categoryid" multiple onChange="fetch_subcategory(); fetch_subsubcategory(); fetch_brand(); fetch_frametype(); fetch_frameshape(); fetch_framesize(); fetch_framematerial();" >
				<?php
				$categoryids = explode(',', $productRow['categoryid']);
				$categoryQueryResult = $db->view('categoryid,title', 'rb_categories', 'categoryid', "and status='active'", 'title asc');
				foreach($categoryQueryResult['result'] as $categoryRow)
				{
				?>
					<option VALUE="<?php echo $validation->db_field_validate($categoryRow['categoryid']); ?>" <?php if($mode == 'edit') { if(in_array($categoryRow['categoryid'], $categoryids)) echo "selected"; } ?>><?php echo $validation->db_field_validate($categoryRow['title']); ?></option>
				<?php
				}
				?>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="subcategoryid">Select Sub-Category</label>
		</div>
		<div class="col-sm-9">
			<div id="subcategory_area">
				<p class="text">No Data Available!</p>
			</div>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="subsubcategoryid">Select Sub-Sub-Category</label>
		</div>
		<div class="col-sm-9">
			<div id="subsubcategory_area">
				<p class="text">No Data Available!</p>
			</div>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="brandid">Select Brand</label>
		</div>
		<div class="col-sm-9">
			<div id="brand_area">
				<p class="text">No Data Available!</p>
			</div>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="frametypeid">Select Frame Type</label>
		</div>
		<div class="col-sm-9">
			<div id="frametype_area">
				<p class="text">No Data Available!</p>
			</div>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="frameshapeid">Select Frame Shape</label>
		</div>
		<div class="col-sm-9">
			<div id="frameshape_area">
				<p class="text">No Data Available!</p>
			</div>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="framesizeid">Select Frame Size</label>
		</div>
		<div class="col-sm-9">
			<div id="framesize_area">
				<p class="text">No Data Available!</p>
			</div>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="framematerialid">Select Frame Material</label>
		</div>
		<div class="col-sm-9">
			<div id="framematerial_area">
				<p class="text">No Data Available!</p>
			</div>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="title"><strong>Title *</strong></label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="title" id="title" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['title']); ?>" required />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="title_id">Title ID <em>(Optional)</em></label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="title_id" id="title_id" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['title_id']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="tagline">Tagline</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="tagline" id="tagline" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['tagline']); ?>" />
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="order_custom">Order</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="order_custom" id="order_custom" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['order_custom']); else echo $max_order; ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="product_code">Product Code</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="product_code" id="product_code" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['product_code']); else echo 'PC-'.$max_order; ?>" />
			<input type="hidden" name="product_code_value" id="product_code_value" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['product_code_value']); else echo $max_order; ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="currency_code">Currency Code *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="currency_code" id="currency_code" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['currency_code']); else echo "INR"; ?>" required />
		</div>
	</div>

	<fieldset class="scheduler-border">
		<legend class="scheduler-border">Variants</legend>
		
		<table class="table variants" id="variants_table">
			<thead>
				<tr>
					<th>Title</th>
					<th>SKU</th>
					<th>Color</th>
					<th>Frame Width</th>
					<th>Measure</th>
					<th>Sale Price *</th>
					<th>MRP</th>
					<th>Stock Quantity *</th>
					<th>Image(s)</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php if($mode == 'edit') { ?>
					<?php
					if($variantQueryResult['num_rows'] >= 1)
					{
						$slr = 0;
						foreach($variantQueryResult['result'] as $variantRow)
						{
					?>
						<input type="hidden" name="variantid[]" id="variantid" class="form-control" value="<?php echo $validation->db_field_validate($variantRow['variantid']); ?>" />
						<tr>
							<td><input type="text" name="variant[]" id="variant" class="form-control" value="<?php echo $validation->db_field_validate($variantRow['variant']); ?>" /></td>
							<td><input type="text" name="sku[]" id="sku" class="form-control" value="<?php echo $validation->db_field_validate($variantRow['sku']); ?>" /></td>
							<td><input type="text" name="color[]" id="color" class="form-control" value="<?php echo $validation->db_field_validate($variantRow['color']); ?>" /></td>
							<td><input type="text" name="width[]" id="width" class="form-control" value="<?php echo $validation->db_field_validate($variantRow['width']); ?>" /><em>[140 mm]</em></td>
							<td><input type="text" name="measure[]" id="measure" class="form-control" value="<?php echo $validation->db_field_validate($variantRow['measure']); ?>" /><em>[53-19-148]</em></td>
							<td>
								<input type="number" name="price[]" id="price" class="form-control" value="<?php echo $validation->db_field_validate($variantRow['price']); ?>" required />
							</td>
							<td><input type="number" name="mrp[]" id="mrp" class="form-control" value="<?php echo $validation->db_field_validate($variantRow['mrp']); ?>" /></td>
							<td><input type="number" name="stock_quantity[]" id="stock_quantity" class="form-control" value="<?php echo $validation->db_field_validate($variantRow['stock_quantity']); ?>" required /></td>
							<td>
								<input type="file" name="variant_imgName<?php echo $slr; ?>[]" id="variant_imgName" class="w-150" multiple />
								<input type="hidden" name="old_variant_imgName<?php echo $slr; ?>" id="old_variant_imgName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($variantRow['imgName']); ?>" />
								<?php if($mode == 'edit' and $variantRow['imgName'] != "") { ?>
									<div class="mt-2 links">
										<?php
										$variant_imgName = $variantRow['imgName'];
										$variant_imgName = explode(" | ", $variant_imgName);
										foreach($variant_imgName as $variant_img)
										{
										?>
											<div class="image-preview">
												<a href="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($variant_img); ?>" target="_blank"><img src="<?php echo IMG_THUMB_LOC; echo $validation->db_field_validate($variant_img); ?>" title="<?php echo $validation->db_field_validate($variant_img); ?>" alt="<?php echo $validation->db_field_validate($variant_img); ?>" class="image-preview-img variant_img" /></a>
												<br />
												<a href="product_form.php?mode=edit&productid=<?php echo $productid; ?>&variantid=<?php echo $variantRow['variantid']; ?>&variant_imgName=<?php echo $variant_img; ?>&q=variant_imgdel" class="del_link" onClick="return del();">Delete</a>
											</div>
										<?php
										}
										?>
									</div>
								<?php } ?>
							</td>
							<td class="text-center align-baseline"><a href="javascript:void(0);" class="delete_variant" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-times" aria-hidden="true"></i></a></td>
						</tr>
					<?php
							$slr++;
						}
					}
					else
					{
					?>
						<tr>
							<td><input type="text" name="variant[]" id="variant" class="form-control" /></td>
							<td><input type="text" name="sku[]" id="sku" class="form-control" /></td>
							<td><input type="text" name="color[]" id="color" class="form-control" /></td>
							<td><input type="text" name="width[]" id="width" class="form-control" /><em>[140 mm]</em></td>
							<td><input type="text" name="measure[]" id="measure" class="form-control" /><em>[53-19-148]</em></td>
							<td><input type="number" name="price[]" id="price" class="form-control" required /></td>
							<td><input type="number" name="mrp[]" id="mrp" class="form-control" /></td>
							<td><input type="number" name="stock_quantity[]" id="stock_quantity" class="form-control" required /></td>
							<td><input type="file" name="variant_imgName0[]" id="variant_imgName" class="w-150" multiple /></td>
							<td class="text-center align-baseline"></td>
						</tr>
					<?php
					}
					?>
				<?php } else { ?>
					<tr>
						<td><input type="text" name="variant[]" id="variant" class="form-control" /></td>
						<td><input type="text" name="sku[]" id="sku" class="form-control" /></td>
						<td><input type="text" name="color[]" id="color" class="form-control" /></td>
						<td><input type="text" name="width[]" id="width" class="form-control" /><em>[140 mm]</em></td>
						<td><input type="text" name="measure[]" id="measure" class="form-control" /><em>[53-19-148]</em></td>
						<td><input type="number" name="price[]" id="price" class="form-control" required /></td>
						<td><input type="number" name="mrp[]" id="mrp" class="form-control" /></td>
						<td><input type="number" name="stock_quantity[]" id="stock_quantity" class="form-control" required /></td>
						<td><input type="file" name="variant_imgName0[]" id="variant_imgName" class="w-150" multiple /></td>
						<td class="text-center align-baseline"></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<a href="javascript:void(0);" onClick="add_variant();">Add Row</a>
		<a href="javascript:void(0);" class="float-right" onClick="delete_variant();">Delete Last Row</a>
		
		<em class="d-block mt-3">All files should be Image and size under <?php echo $validation->convertToReadableSize($configRow['image_maxsize']); ?>. Image extension should be .jpg, .jpeg, .png, .gif. Hold "Ctrl" key for multi-selection</em>
	</fieldset>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="shipping">Shipping Charges</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="shipping" id="shipping" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['shipping']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="tax_information">Tax Information</label>
		</div>
		<div class="col-sm-9">
			<select name="tax_information" id="tax_information" class="form-control" >
				<option value="" <?php if($mode == 'edit') { if($validation->db_field_validate($productRow['tax_information']) == "") echo "selected"; } ?>>--select--</option>
				<option value="included" <?php if($mode == 'edit') { if($productRow['tax_information'] == "included") echo "selected"; } else { echo "selected"; } ?>>Included</option>
				<option value="excluded" <?php if($mode == 'edit') { if($productRow['tax_information'] == "excluded") echo "selected"; } ?>>Excluded</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="tax_type">Tax Type</label>
		</div>
		<div class="col-sm-9">
			<select name="tax_type" id="tax_type" class="form-control" >
				<option value="" <?php if($mode == 'edit') { if($validation->db_field_validate($productRow['tax_type']) == "") echo "selected"; } ?>>--select--</option>
				<option value="CGST/SGST" <?php if($mode == 'edit') { if($productRow['tax_type'] == "CGST/SGST") echo "selected"; } ?>>CGST/SGST</option>
				<option value="UTGST" <?php if($mode == 'edit') { if($productRow['tax_type'] == "UTGST") echo "selected"; } ?>>UTGST</option>
				<option value="IGST" <?php if($mode == 'edit') { if($productRow['tax_type'] == "IGST") echo "selected"; } else { echo "selected"; } ?>>IGST</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="tax">Tax</label>
		</div>
		<div class="col-sm-9">
			<div class="form-inline">
				<input type="number" name="tax" id="tax" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['tax']); ?>" /> %
			</div>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="cod">COD *</label>
		</div>
		<div class="col-sm-9">
			<select name="cod" id="cod" class="form-control" required >
				<option value="" <?php if($mode == 'edit') { if($validation->db_field_validate($productRow['cod']) == "") echo "selected"; } ?>>--select--</option>
				<option value="yes" <?php if($mode == 'edit') { if($validation->db_field_validate($productRow['cod']) == "yes") echo "selected"; } ?>>Yes</option>
				<option value="no" <?php if($mode == 'edit') { if($validation->db_field_validate($productRow['cod']) == "no") echo "selected"; } ?>>No</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="cancellation_policy">Cancellation Policy ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="cancellation_policy" id="cancellation_policy" <?php if($mode == 'edit') { if($productRow['cancellation_policy'] == "1") echo "checked"; } else { echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="return_policy">Return Policy ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="return_policy" id="return_policy" <?php if($mode == 'edit') { if($productRow['return_policy'] == "1") echo "checked"; } else { echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="return_timeperiod">Return Time Period</label>
		</div>
		<div class="col-sm-9">
			<input type="number" name="return_timeperiod" id="return_timeperiod" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['return_timeperiod']); else echo "10"; ?>" />
			<em>In Days only</em>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="highlight_text">Highlight Text ?</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="highlight_text" id="highlight_text" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['highlight_text']); ?>" />
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="advance_cart">Advance cart system with power and lenses ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="advance_cart" id="advance_cart" <?php if($mode == 'edit') { if($productRow['advance_cart'] == "1") echo "checked"; } ?> />
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="frame_only">Display Frame only option ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="frame_only" id="frame_only" <?php if($mode == 'edit') { if($productRow['frame_only'] == "1") echo "checked"; } ?> />
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="frame_shape_guide">Frame Shape guide ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="frame_shape_guide" id="frame_shape_guide" <?php if($mode == 'edit') { if($productRow['frame_shape_guide'] == "1") echo "checked"; } else { echo "checked"; } ?> />
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="size_guide">Size guide ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="size_guide" id="size_guide" <?php if($mode == 'edit') { if($productRow['size_guide'] == "1") echo "checked"; } else { echo "checked"; } ?> />
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="lens_guide">Lens guide ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="lens_guide" id="lens_guide" <?php if($mode == 'edit') { if($productRow['lens_guide'] == "1") echo "checked"; } ?> />
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="try_on">Try-on ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="try_on" id="try_on" <?php if($mode == 'edit') { if($productRow['try_on'] == "1") echo "checked"; } else { echo "checked"; } ?> />
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="prescription">Enable Prescription ?</label>
		</div>
		<div class="col-sm-9">
			<input type="checkbox" name="prescription" id="prescription" <?php if($mode == 'edit') { if($productRow['prescription'] == "1") echo "checked"; } else { echo "checked"; } ?> />
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="url">URL</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="url" id="url" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['url']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="url_target">URL Target</label>
		</div>
		<div class="col-sm-9">
			<select NAME="url_target" ID="url_target" CLASS="form-control">
				<option VALUE="_self" <?php if($mode == 'edit') { if($validation->db_field_validate($productRow['url_target']) == "_self") echo "selected"; } ?>>Open in Same Tab</option>
				<option VALUE="_blank" <?php if($mode == 'edit') { if($validation->db_field_validate($productRow['url_target']) == "_blank") echo "selected"; } ?>>Open in New Tab</option>
			</select>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_title">Meta Title</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_title" id="meta_title" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['meta_title']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_keywords">Meta Keywords</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['meta_keywords']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="meta_description">Meta Description</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="meta_description" id="meta_description" class="form-control" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['meta_description']); ?>" />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-12">
			<label for="features">Features</label>
		</div>
		<div class="col-sm-12">
			<textarea id="features" name="features" class="tinymce"><?php if($mode == 'edit') echo $productRow['features']; ?></textarea>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-12">
			<label for="description">Description</label>
		</div>
		<div class="col-sm-12">
			<button TYPE="button" CLASS="btn btn-default btn-sm" id="image_model_button" onClick="document.getElementById('image_upper_text').style.display='none'; document.getElementById('userImage').value='';"><i class="fa fa-image" aria-hidden="true"></i> Add Image</button>
			<textarea id="description" name="description" class="tinymce"><?php if($mode == 'edit') echo $productRow['description']; ?></textarea>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-12">
			<label for="specification">Specification</label>
		</div>
		<div class="col-sm-12">
			<textarea id="specification" name="specification" class="tinymce"><?php if($mode == 'edit') echo $productRow['specification']; ?></textarea>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-12">
			<label for="advertisement">Advertisement Box</label>
		</div>
		<div class="col-sm-12">
			<textarea id="advertisement" name="advertisement" class="tinymce"><?php if($mode == 'edit') echo $categoryRow['advertisement']; ?></textarea>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="imgName">Upload Image(s)</label>
		</div>
		<div class="col-sm-9">
			<input type="file" name="imgName[]" id="imgName" multiple />
			<input type="hidden" name="old_imgName" id="old_imgName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['imgName']); ?>" />
			<?php if($mode == 'edit' and $productRow['imgName'] != "") { ?>
				<div class="mt-2 links">
					<?php
					$imgName = $productRow['imgName'];
					$imgName = explode(" | ", $imgName);
					foreach($imgName as $img)
					{
					?>
						<div class="image-preview">
							<a href="<?php echo IMG_MAIN_LOC; echo $validation->db_field_validate($img); ?>" target="_blank"><img src="<?php echo IMG_THUMB_LOC; echo $validation->db_field_validate($img); ?>" title="<?php echo $validation->db_field_validate($img); ?>" alt="<?php echo $validation->db_field_validate($img); ?>" class="image-preview-img" /></a>
							<br />
							<a href="product_form.php?mode=edit&productid=<?php echo $productid; ?>&imgName=<?php echo $img; ?>&q=imgdel" class="del_link" onClick="return del();">Delete</a>
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
			<input type="hidden" name="old_fileName" id="old_fileName" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['fileName']); ?>" />
			<?php if($mode == 'edit' and $productRow['fileName'] != "") { ?>
				<div class="mt-2 links">
					<a href="<?php echo FILE_LOC; echo $validation->db_field_validate($productRow['fileName']); ?>" target="_blank">Click to Download</a> | <a href="product_form.php?mode=edit&productid=<?php echo $productid; ?>&q=filedel" onClick="return del();">Delete</a>
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
			<input type="checkbox" name="priority" id="priority" <?php if($mode == 'edit') { if($productRow['priority'] == "1") echo "checked"; } ?> />
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="status">Status *</label>
		</div>
		<div class="col-sm-9">
			<select name="status" id="status" class="form-control" required >
				<option value="active" <?php if($mode == 'edit') { if($validation->db_field_validate($productRow['status']) == "active") echo "selected"; } ?>>Active</option>
				<option value="inactive" <?php if($mode == 'edit') { if($validation->db_field_validate($productRow['status']) == "inactive") echo "selected"; } ?>>Inactive</option>
			</select>
		</div>
	</div>

	<?php if($mode == 'edit') { ?>
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Views</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><?php echo $validation->db_field_validate($productRow['views']); ?></p>
			</div>
		</div>
		
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Author</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><a href="product_view.php?userid=<?php echo $userid; ?>"><?php echo $validation->db_field_validate($userRow['display_name']); ?></a></p>
			</div>
		</div>
		
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Author (Modified By)</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><a href="product_view.php?userid=<?php echo $userid_updt; ?>"><?php echo $validation->db_field_validate($userupdtRow['display_name']); ?></a></p>
			</div>
		</div>
		
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>User's IP Address</label>
			</div>
			<div class="col-sm-9">
				<p class="text"><?php echo $validation->db_field_validate($productRow['user_ip']); ?></p>
				<input type="hidden" name="user_ip" value="<?php if($mode == 'edit') echo $validation->db_field_validate($productRow['user_ip']); ?>" />
			</div>
		</div>
		
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Modification Date & Time</label>
			</div>
			<div class="col-sm-9">
				<?php if($productRow['modifydate'] != "") { ?>
					<p class="text"><?php echo $validation->date_format_custom($productRow['modifydate'])." at ".$validation->time_format_custom($productRow['modifytime']); ?></p>
				<?php } ?>
			</div>
		</div>
		
		<div class="row mb-3">
			<div class="col-sm-3">
				<label>Creation Date & Time</label>
			</div>
			<div class="col-sm-9">
				<?php if($productRow['createdate'] != "") { ?>
					<p class="text"><?php echo $validation->date_format_custom($productRow['createdate'])." at ".$validation->time_format_custom($productRow['createtime']); ?></p>
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
					<a HREF="product_actions.php?q=del&productid=<?php echo $productRow['productid']; ?>" class="btn btn-default btn-sm btn_delete" onClick="return del();"><i class="fas fa-trash"></i>&nbsp;&nbsp;Delete</a>
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
		<div STYLE="background:none; padding:3%;">
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