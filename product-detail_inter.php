<?php
include_once("inc_config.php");

if($_SESSION['detail_productid'] != "")
{
	$productid = $_SESSION['detail_productid'];
}
else
{
	$productid = $validation->input_validate($_POST['id']);
}
if($_SESSION['detail_quantity'] != "")
{
	$quantity = $_SESSION['detail_quantity'];
}
else
{
	$quantity_custom = $validation->input_validate($_POST['quantity_custom']);
	if($quantity_custom != "" and $quantity_custom != "0")
	{
		$quantity = $quantity_custom;
	}
	else
	{
		$quantity = $validation->input_validate($_POST['quantity']);
	}
}
if($_SESSION['detail_price'] != "")
{
	$price = $_SESSION['detail_price'];
}
else
{
	$price = $validation->input_validate($_POST['price']);
}
if($_SESSION['detail_variantid'] != "")
{
	$variantid = $_SESSION['detail_variantid'];
}
else
{
	$variantid = $validation->input_validate($_POST['variantid']);
}
$redirect_url = $validation->input_validate($_POST['redirect_url']);
$q = $validation->input_validate($_GET['q']);
$status = "active";
if($productid == "" || $quantity == "" || $price == "" || $variantid == "")
{
	$_SESSION['error_msg_fe'] = "Please select valid parameters to continue!";
	header("Location: {$redirect_url}");
	exit();
}
if($quantity == "0" || $price == "0")
{
	$_SESSION['error_msg_fe'] = "Please select valid parameters to continue!";
	header("Location: {$redirect_url}");
	exit();
}

if($quantity == 0 || $quantity <= 0)
{
	$_SESSION['error_msg_fe'] = "Please atleast one quantity to continue!";
	header("Location: {$redirect_url}");
	exit();
}

if($regid == "")
{
	$_SESSION['detail_productid'] = $productid;
	$_SESSION['detail_quantity'] = $quantity;
	$_SESSION['detail_price'] = $price;
	$_SESSION['detail_variantid'] = $variantid;
	$_SESSION['error_msg_fe'] = "Please login first to continue!";
	if(isset($_POST['add_to_cart']))
	{
		header("Location: {$base_url}login{$suffix}?q=cart");
	}
	else if(isset($_POST['buy_now']))
	{
		header("Location: {$base_url}login{$suffix}?q=buy");
	}
	else
	{
		header("Location: {$base_url}login{$suffix}?q=cart");
	}
	exit();
}
else
{
	if(isset($_POST['add_to_cart']))
	{
		$q = "cart";
	}
	else if(isset($_POST['buy_now']))
	{
		$q = "buy";
	}
}

$variantResult = $db->view('stock_quantity', 'rb_products_variants', 'variantid', "and productid = '$productid' and variantid='$variantid'", 'variantid asc');
$variantRow = $variantResult['result'][0];
$product_stock_quantity = $validation->db_field_validate($variantRow['stock_quantity']);

if($quantity > $product_stock_quantity)
{
	$_SESSION['notify_error_msg_fe'] = "We have maximum of only {$product_stock_quantity} in stock!";
	header("Location: {$redirect_url}");
	exit();
}

$checkResult = $db->view('*', 'rb_cart', 'cartid', "and regid = '$regid' and productid = '$productid' and variantid='$variantid' and status = 'active'");

$fields = array('regid'=>$regid, 'productid'=>$productid, 'quantity'=>$quantity, 'price'=>$price, 'variantid'=>$variantid, 'status'=>$status, 'user_ip'=>$user_ip);

if($checkResult['num_rows'] == 0)
{
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$cartResult = $db->insert("rb_cart", $fields);
	if(!$cartResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	$cartids = $cartResult;
}
else
{
	$cartRow = $checkResult['result'][0];
	
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$cartResult = $db->update("rb_cart", $fields, array('regid'=>$regid, 'productid'=>$productid, 'status'=>'active'));
	if(!$cartResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	$cartids = $cartRow['cartid'];
}

if($q == "buy")
{
	$cartids = $cartids;
	$variantids = $variantid;
	$productids = $productid;
	
	$price_detail = $price*$quantity;
	$total_price = $price*$quantity;
	$coupon_code = "";
	$coupon_discount = "";
	$shipping_total = $validation->input_validate($_POST['shipping']);
	$shipping_detail = $validation->input_validate($_POST['shipping']);
	$taxamount_total = $validation->input_validate($_POST['taxamount']);
	$taxamount_detail = $validation->input_validate($_POST['taxamount']);
	$tax_detail = $validation->input_validate($_POST['tax']);
	$final_price = $validation->input_validate($_POST['final_price'])*$quantity;
	$refno = 'BT-C-'.$random_no;
	$_SESSION['cart_refno'] = $refno;
	
	$fields = array('regid'=>$regid, 'refno'=>$refno, 'cartids'=>$cartids, 'variantids'=>$variantids, 'productids'=>$productids, 'price_detail'=>$price_detail, 'total_price'=>$total_price, 'coupon_code'=>$coupon_code, 'coupon_discount'=>$coupon_discount, 'shipping_total'=>$shipping_total, 'shipping_detail'=>$shipping_detail, 'taxamount_total'=>$taxamount_total, 'taxamount_detail'=>$taxamount_detail, 'tax_detail'=>$tax_detail, 'final_price'=>$final_price, 'user_ip'=>$user_ip);
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	$purchasetempResult = $db->insert("rb_purchases_temp", $fields);
	
	$cartResult = $db->custom("UPDATE rb_cart set regid='$regid', refno='$refno', user_ip='$user_ip', modifytime='$createtime', modifydate='$createdate' where FIND_IN_SET(`cartid`, '$cartids')");
}

$_SESSION['detail_productid'] = "";
$_SESSION['detail_quantity'] = "";
$_SESSION['detail_price'] = "";
$_SESSION['detail_variantid'] = "";

if($q == "cart")
{
	if($redirect_url != "")
	{
		$_SESSION['notify_success_msg_fe'] = "Item is successfully added into the Cart";
		header("Location: {$redirect_url}");
	}
	else
	{
		$_SESSION['success_msg_fe'] = "";
		header("Location: {$base_url}cart{$suffix}");
	}
	exit();
}
else if($q == "buy")
{
	$_SESSION['success_msg_fe'] = "";
	header("Location: {$base_url}checkout{$suffix}");
	exit();
}
?>