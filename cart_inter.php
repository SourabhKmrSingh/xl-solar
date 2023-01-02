<?php
include_once("inc_config.php");

if($_SESSION['regid'] == "")
{
	$_SESSION['error_msg_fe'] = "Login to continue!";
	header("Location: {$base_url}login{$suffix}");
	exit();
}

if(isset($_GET['token']) && $_GET['token'] === $_SESSION['csrf_token'])
{
	if($_GET['qty'] != "")
	{
		$productid = $validation->input_validate($_GET['id']);
		$variantid = $validation->input_validate($_GET['id2']);
		$quantity = $validation->input_validate($_GET['qty']);
		
		if($productid == "" || $quantity == "" || $quantity == "0")
		{
			$_SESSION['error_msg_fe'] = "Please select valid parameters to continue!";
			header("Location: {$base_url}cart{$suffix}");
			exit();
		}
		
		if($quantity == 0 || $quantity <= 0)
		{
			$_SESSION['error_msg_fe'] = "Please atleast one quantity to continue!";
			header("Location: {$base_url}cart{$suffix}");
			exit();
		}
		
		$variantResult = $db->view('stock_quantity', 'rb_products_variants', 'variantid', "and productid = '$productid' and variantid='$variantid'", 'variantid asc');
		$variantRow = $variantResult['result'][0];
		$product_stock_quantity = $validation->db_field_validate($variantRow['stock_quantity']);
		
		if($quantity > $product_stock_quantity)
		{
			$_SESSION['error_msg_fe'] = "We have maximum of only <strong>{$product_stock_quantity}</strong> in stock!";
			header("Location: {$base_url}cart{$suffix}");
			exit();
		}

		$checkResult = $db->view('*', 'rb_cart', 'cartid', "and regid = '$regid' and productid = '$productid' and status = 'active'");

		$fields = array('quantity'=>$quantity, 'user_ip'=>$user_ip);
		$fields['modifytime'] = $createtime;
		$fields['modifydate'] = $createdate;

		if($checkResult['num_rows'] >= 1)
		{
			$cartResult = $db->update("rb_cart", $fields, array('regid'=>$regid, 'productid'=>$productid, 'status'=>'active'));
			if(!$cartResult)
			{
				echo mysqli_error($connect);
				exit();
			}
		}
		
		$_SESSION['coupon_error_msg_fe'] = "";
		$_SESSION['coupon_success_msg_fe'] = "";
		$_SESSION['coupon_discount'] = "";
		$_SESSION['success_msg_fe'] = "Quantity Updated!";
		header("Location: {$base_url}cart{$suffix}");
		exit();
	}
	else if($_GET['cartid'] != "")
	{
		$productid = $validation->input_validate($_GET['id']);
		$cartid = $validation->input_validate($_GET['cartid']);
		
		$deletecartResult = $db->delete("rb_cart", array('cartid'=>$cartid, 'regid'=>$regid, 'productid'=>$productid));
		$_SESSION['cart_remove_msg_succ'] = 'Item is removed from the cart!';
		header("Location: {$base_url}cart{$suffix}");
		exit();
	}
	else if(isset($_POST['checkout']))
	{
		$cartids = $validation->input_validate($_POST['cartids']);
		$variantids = $validation->input_validate($_POST['variantids']);
		$productids = $validation->input_validate($_POST['productids']);
		$price_detail = $validation->input_validate($_POST['price_detail']);
		$total_price = $validation->input_validate($_POST['total_price']);
		$coupon_code = $validation->input_validate($_POST['coupon_code']);
		$coupon_discount = $validation->input_validate($_POST['coupon_discount']);
		$shipping_total = $validation->input_validate($_POST['shipping_total']);
		$shipping_detail = $validation->input_validate($_POST['shipping_detail']);
		$taxamount_total = $validation->input_validate($_POST['taxamount_total']);
		$taxamount_detail = $validation->input_validate($_POST['taxamount_detail']);
		$tax_detail = $validation->input_validate($_POST['tax_detail']);
		$taxinformation_detail = $validation->input_validate($_POST['taxinformation_detail']);
		$taxtype_detail = $validation->input_validate($_POST['taxtype_detail']);
		$final_price = $validation->input_validate($_POST['final_price']);
		$refno = 'XLS-C-'.$random_no;
		$_SESSION['cart_refno'] = $refno;
		
		$orderCountResult = $db->view('purchaseid', 'rb_purchases', 'purchaseid', "and regid='$regid' and tracking_status='delivered' and status='active'");
		if($orderCountResult['num_rows'] >= 1)
		{
			$minimum_cart = 0;
		}
		else
		{
			$minimum_cart = $configRow['minimum_cart'];
		}
			
		if($final_price < $minimum_cart)
		{
			$_SESSION['error_msg_fe'] = "Minimum cart value should be ₹{$minimum_cart}";
			header("Location: {$base_url}cart{$suffix}");
			exit();
		}
		
		$fields = array('regid'=>$regid, 'refno'=>$refno, 'cartids'=>$cartids, 'variantids'=>$variantids, 'productids'=>$productids, 'price_detail'=>$price_detail, 'total_price'=>$total_price, 'coupon_code'=>$coupon_code, 'coupon_discount'=>$coupon_discount, 'shipping_total'=>$shipping_total, 'shipping_detail'=>$shipping_detail, 'taxamount_total'=>$taxamount_total, 'taxamount_detail'=>$taxamount_detail, 'tax_detail'=>$tax_detail, 'taxinformation_detail'=>$taxinformation_detail, 'taxtype_detail'=>$taxtype_detail, 'final_price'=>$final_price, 'user_ip'=>$user_ip);
		$fields['createtime'] = $createtime;
		$fields['createdate'] = $createdate;
		$purchasetempResult = $db->insert("rb_purchases_temp", $fields);
		
		$cartResult = $db->custom("UPDATE rb_cart set regid='$regid', refno='$refno', user_ip='$user_ip', modifytime='$createtime', modifydate='$createdate' where FIND_IN_SET(`cartid`, '$cartids')");
		
		header("Location: {$base_url}checkout{$suffix}");
		exit();
	}
	else if($_POST['coupon_code'] != "")
	{
		$coupon_code = strtoupper($validation->input_validate($_POST['coupon_code']));
		$price = $validation->input_validate($_POST['price']);
		
		$couponResult = $db->view('*', 'rb_coupons', 'couponid', "and coupon_code = '$coupon_code' and status = 'active'");
		$couponCount = $couponResult['num_rows'];
		$couponRow = $couponResult['result'][0];
		
		$_SESSION['coupon_code'] = $coupon_code;
		
		if($couponCount == 0)
		{
			$_SESSION['coupon_error_msg_fe'] = "Please enter a valid Coupon Code";
			$_SESSION['coupon_success_msg_fe'] = "";
			$_SESSION['coupon_discount'] = "";
		}
		else if($createdate >= $couponRow['expiry_date'])
		{
			$_SESSION['coupon_error_msg_fe'] = "The Coupon Code is expired";
			$_SESSION['coupon_success_msg_fe'] = "";
			$_SESSION['coupon_discount'] = "";
		}
		else if($price < $couponRow['min_price'])
		{
			$_SESSION['coupon_error_msg_fe'] = "Minimum cart value should be ".$couponRow['min_price'];
			$_SESSION['coupon_success_msg_fe'] = "";
			$_SESSION['coupon_discount'] = "";
		}
		else if($couponCount >= 1)
		{
			$coupon_discount = ($couponRow['discount'] * $price) / 100;
			
			if($coupon_discount > $couponRow['max_discount'])
			{
				$coupon_discount = $couponRow['max_discount'];
			}
			else
			{
				$coupon_discount = $coupon_discount;
			}
			
			$_SESSION['coupon_error_msg_fe'] = "";
			$_SESSION['coupon_success_msg_fe'] = "Coupon applied successfully";
			$_SESSION['coupon_discount'] = $coupon_discount;
		}
		
		header("Location: {$base_url}cart{$suffix}#coupon");
		exit();
	}
}
else
{
	$_SESSION['error_msg_fe'] = "Error Occurred! Please try again.";
	header("Location: {$base_url}cart{$suffix}");
	exit();
}

header("Location: {$base_url}cart{$suffix}");
exit();
?>