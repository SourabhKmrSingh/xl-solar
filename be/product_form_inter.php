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
	if(isset($_GET['productid']))
	{
		$productid = $validation->urlstring_validate($_GET['productid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: product_view.php");
		exit();
	}
}

$categoryid = $validation->input_validate($_POST['categoryid']);
if($categoryid=='')
{
	$categoryid = 0;
}
$subcategoryid = $validation->input_validate($_POST['subcategoryid']);
if($subcategoryid=='')
{
	$subcategoryid = 0;
}

$title = $validation->input_validate($_POST['title']);
$title_id = $validation->input_validate($_POST['title_id']);
if($title_id == "")
{
	$title_id = $title;
}
$title_id = $validation->friendlyURL($title_id);
$tagline = $validation->input_validate($_POST['tagline']);
$order_custom = $validation->input_validate($_POST['order_custom']);
if($order_custom=='')
{
	$order_custom = 0;
}
$product_code = $validation->input_validate($_POST['product_code']);
$product_code_value = $validation->input_validate($_POST['product_code_value']);
if($product_code_value=='')
{
	$product_code_value = 0;
}
$currency_code = $validation->input_validate($_POST['currency_code']);

$variantid_array = $_POST['variantid'];
$variant_array = $_POST['variant'];
$sku_array = $_POST['sku'];
$price_array = $_POST['price'];
$mrp_array = $_POST['mrp'];
$stock_quantity_array = $_POST['stock_quantity'];

$variant = $validation->input_validate($variant_array[0]);
$sku = $validation->input_validate($sku_array[0]);
$price = $price_array[0];
$mrp = $mrp_array[0];
if($mrp == "")
{
	$mrp = 0;
}
$stock_quantity = $stock_quantity_array[0];

$shipping = $validation->input_validate($_POST['shipping']);
if($shipping=='')
{
	$shipping = 0;
}
$tax_information = $validation->input_validate($_POST['tax_information']);
$tax_type = $validation->input_validate($_POST['tax_type']);
$tax = $validation->input_validate($_POST['tax']);
if($tax=='')
{
	$tax = 0;
}
$cod = $validation->input_validate($_POST['cod']);
if(isset($_POST['sale']))
{
	$sale = 1;
}
else
{
	$sale = 0;
}
$url = $validation->input_validate($_POST['url']);
$url_target = $validation->input_validate($_POST['url_target']);
$meta_title = $validation->input_validate($_POST['meta_title']);
$meta_keywords = $validation->input_validate($_POST['meta_keywords']);
$meta_description = $validation->input_validate($_POST['meta_description']);
$description = mysqli_real_escape_string($connect, $_POST['description']);
$specification = mysqli_real_escape_string($connect, $_POST['specification']);
if(isset($_POST['priority']))
{
	$priority = 1;
}
else
{
	$priority = 0;
}
$status = $validation->input_validate($_POST['status']);
$old_imgName = $validation->input_validate($_POST['old_imgName']);
$old_fileName = $validation->input_validate($_POST['old_fileName']);

$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

$dupresult = $db->check_duplicates('rb_products', 'productid', $productid, 'title_id', strtolower($title_id), $mode);
if($dupresult >= 1)
{
	$_SESSION['error_msg'] = "Title ID already exists!";
	header("Location: product_view.php");
	exit();
}

$imgTName = $_FILES['imgName']['name'][0];
if($imgTName != "")
{
	$files = array();
	foreach($_FILES['imgName'] as $k => $l)
	{
		foreach ($l as $i => $v)
		{
			if(!array_key_exists($i, $files))
			$files[$i] = array();
			$files[$i][$k] = $v;
		}
	}
	
	$imgName = array();
	
	foreach ($files as $file)
	{
		$handle = new Upload($file);
		if($handle->uploaded)
		{
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if($configRow['large_width'] != "0" and $configRow['large_height'] != "0")
			{
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['large_width']);
				$handle->image_y = $validation->db_field_validate($configRow['large_height']);
				$handle->image_no_enlarging = ($configRow['large_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['large_ratio'] === "false") ? false : true;
			}
			
			$handle->process(IMG_MAIN_LOC);
			if($handle->processed)
			{
				array_push($imgName, $handle->file_dst_name);
			}
			else
			{
				$_SESSION['error_msg'] = $handle->error.'!';
				header("Location: product_view.php");
				exit();
			}
			
			// Thumbnail Image
			$handle->file_force_extension = true;
			$handle->file_max_size = $validation->db_field_validate($configRow['image_maxsize']);
			$handle->allowed = array('image/*');
			if($configRow['thumb_width'] != "0" and $configRow['thumb_height'] != "0")
			{
				$handle->image_resize = true;
				$handle->image_x = $validation->db_field_validate($configRow['thumb_width']);
				$handle->image_y = $validation->db_field_validate($configRow['thumb_height']);
				$handle->image_no_enlarging = ($configRow['thumb_ratio'] === "false") ? false : true;
				$handle->image_ratio = ($configRow['thumb_ratio'] === "false") ? false : true;
			}
			
			$handle->process(IMG_THUMB_LOC);
			if($handle->processed)
			{
			}
			else
			{
				$_SESSION['error_msg'] = $handle->error.'!';
				header("Location: product_view.php");
				exit();
			}
			
			$handle-> clean();
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: product_view.php");
			exit();
		}
	}
}

$imgName = implode(" | ", $imgName);
if($old_imgName != "")
{
	if($imgName != "")
	{
		$imgName = $imgName.' | '.$old_imgName;
	}
	else
	{
		$imgName = $old_imgName;
	}
}

$fileTName = $_FILES['fileName']['name'];
if($fileTName != "")
{	
	$handle = new Upload($_FILES['fileName']);
    if($handle->uploaded)
	{
		$handle->file_force_extension = true;
		$handle->file_max_size = $validation->db_field_validate($configRow['file_maxsize']);
		$handle->allowed = array('application/*', 'text/csv', 'application/zip');
		
		$handle->process(FILE_LOC);
		if($handle->processed)
		{
			$fileName = $handle->file_dst_name;
		}
		else
		{
			$_SESSION['error_msg'] = $handle->error.'!';
			header("Location: product_view.php");
			exit();
		}
		
		$handle-> clean();
	}
	else
	{
		$_SESSION['error_msg'] = $handle->error.'!';
		header("Location: product_view.php");
		exit();
    }
	
	if($mode == "edit")
	{
		$delresult = $media->filedeletion('rb_products', 'productid', $productid, 'fileName', FILE_LOC);
	}
}

if($imgName == "")
{
	$imgName = $old_imgName;
}
if($fileName == "")
{
	$fileName = $old_fileName;
}

$fields = array('categoryid'=>$categoryid, 'subcategoryid'=>$subcategoryid, 'title'=>$title, 'title_id'=>$title_id, 'tagline'=>$tagline, 'order_custom'=>$order_custom, 'product_code'=>$product_code, 'product_code_value'=>$product_code_value, 'currency_code'=>$currency_code, 'variant'=>$validation->input_validate($variant), 'sku'=>$sku, 'price'=>$price, 'mrp'=>$mrp, 'stock_quantity'=>$stock_quantity, 'shipping'=>$shipping, 'tax_information'=>$tax_information, 'tax_type'=>$tax_type, 'tax'=>$tax, 'cod'=>$cod, 'sale'=>$sale, 'url'=>$url, 'url_target'=>$url_target, 'meta_title'=>$meta_title, 'meta_keywords'=>$meta_keywords, 'meta_description'=>$meta_description, 'description'=>$description, 'specification'=>$specification, 'imgName'=>$imgName, 'fileName'=>$fileName, 'priority'=>$priority, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$productQueryResult = $db->insert("rb_products", $fields);
	if(!$productQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	$productid = $productQueryResult;
	
	$slr = 0;
	foreach($price_array as $price)
	{
		$mrp_value = $mrp_array[$slr];
		if($mrp_value == "")
		{
			$mrp_value = "0.00";
		}
		
		$fields2 = array('productid'=>$productid, 'variant'=>$validation->input_validate($variant_array[$slr]), 'sku'=>$sku_array[$slr], 'price'=>$price, 'mrp'=>$mrp_value, 'stock_quantity'=>$stock_quantity_array[$slr], 'status'=>$status, 'user_ip'=>$user_ip);
		$fields2['userid'] = $userid;
		$fields2['createtime'] = $createtime;
		$fields2['createdate'] = $createdate;
		
		$variantQueryResult = $db->insert("rb_products_variants", $fields2);
		if(!$variantQueryResult)
		{
			echo mysqli_error($connect);
			exit();
		}
		$slr++;
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: product_view.php");
	exit();
}
else if($mode == "edit")
{
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$productQueryResult = $db->update("rb_products", $fields, array('productid'=>$productid));
	if(!$productQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$slr = 0;
	$variantids = "";
	foreach($price_array as $price)
	{
		$mrp_value = $mrp_array[$slr];
		if($mrp_value == "")
		{
			$mrp_value = "0.00";
		}
		
		$fields2 = array('productid'=>$productid, 'variant'=>$validation->input_validate($variant_array[$slr]), 'sku'=>$sku_array[$slr], 'price'=>$price, 'mrp'=>$mrp_value, 'stock_quantity'=>$stock_quantity_array[$slr], 'status'=>$status, 'user_ip'=>$user_ip);
		
		$checkvariantResult = $db->view('variantid', 'rb_products_variants', 'variantid', "and productid = '$productid' and variantid='{$variantid_array[$slr]}'");
		echo $checkvariantResult['num_rows'];
		if($checkvariantResult['num_rows'] >= 1)
		{
			$fields2['userid_updt'] = $userid;
			$fields2['modifytime'] = $createtime;
			$fields2['modifydate'] = $createdate;
			
			$variantQueryResult = $db->update("rb_products_variants", $fields2, array('variantid'=>$variantid_array[$slr]));
			if(!$variantQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
		}
		else
		{
			$fields2['userid'] = $userid;
			$fields2['createtime'] = $createtime;
			$fields2['createdate'] = $createdate;
			
			$variantQueryResult = $db->insert("rb_products_variants", $fields2);
			if(!$variantQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$newvariantid = $variantQueryResult;
		}
		
		$variantids .= "and variantid != '".$variantid_array[$slr]."' ";
		$slr++;
	}
	
	$variantids = substr($variantids, 0, -1);
	if($variantids == "and variantid != ")
	{
		$variantids = "";
	}
	$delvariantQueryResult = $db->custom("delete from rb_products_variants where variantid != '$newvariantid' {$variantids} and productid = '$productid'");
	if(!$delvariantQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: product_view.php$search_filter");
	exit();
}
?>