<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "purchase";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$refno_custom = $validation->urlstring_validate($_GET['refno_custom']);
	
	$productPurchaseResult = $db->view("productid,variantid,quantity,purchaseid,wallet_money,regid", "rb_purchases", "purchaseid", "and refno_custom='{$refno_custom}'", 'purchaseid desc');
	if($productPurchaseResult['num_rows'] >= 1)
	{
		foreach($productPurchaseResult['result'] as $productPurchaseRow)
		{
			$productid = $productPurchaseRow['productid'];
			$variantid = $productPurchaseRow['variantid'];
			$quantity = $productPurchaseRow['quantity'];
			
			$stockResult = $db->custom("update rb_products set stock_quantity = stock_quantity+{$quantity} where productid='{$productid}'");
			if(!$stockResult)
			{
				echo "Stock is not updated! Consult Administrator";
				exit();
			}
			$stockResult2 = $db->custom("update rb_products_variants set stock_quantity = stock_quantity+{$quantity} where productid='{$productid}' and variantid='{$variantid}'");
			if(!$stockResult2)
			{
				echo "Stock is not updated! Consult Administrator";
				exit();
			}
		}
		
		$checkPurchaseRow = $productPurchaseResult['result'][0];
		$regid = $checkPurchaseRow['regid'];
		$wallet_money = $checkPurchaseRow['wallet_money'];
		$purchaseid = $checkPurchaseRow['purchaseid'];
		
		if($wallet_money != "" and $wallet_money != "0" and $wallet_money != "0.00")
		{
			$fields3 = array('status'=>"declined", 'remarks'=>'Product Cancelled');
			$fields3['modifytime'] = $createtime;
			$fields3['modifydate'] = $createdate;
			
			$ewalletrequestResult = $db->update("mlm_ewallet_requests", $fields3, array('purchaseid'=>$purchaseid));
			if(!$ewalletrequestResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			
			$registerwalletResult = $db->custom("update mlm_registrations set wallet_money = wallet_money+{$wallet_money} where regid='{$regid}'");
			if(!$registerwalletResult)
			{
				echo "Member Wallet is not added! Consult Administrator";
				exit();
			}
		}
	}
	
	$purchaseQueryResult = $db->delete("rb_purchases", array('refno_custom'=>$refno_custom));
	if(!$purchaseQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: purchase_view.php");
		exit();
	}
	
	$_SESSION['success_msg'] = "{$purchaseQueryResult} Record Deleted!";
	header("Location: purchase_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$refno_customs = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: purchase_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			array_push($refno_customs, "$id");
			
			if($bulk_actions == "delete")
			{
				$productPurchaseResult = $db->view("productid,variantid,quantity,purchaseid,regid,wallet_money", "rb_purchases", "purchaseid", "and refno_custom='{$id}'", 'purchaseid desc');
				if($productPurchaseResult['num_rows'] >= 1)
				{
					foreach($productPurchaseResult['result'] as $productPurchaseRow)
					{
						$productid = $productPurchaseRow['productid'];
						$variantid = $productPurchaseRow['variantid'];
						$quantity = $productPurchaseRow['quantity'];
						
						$stockResult = $db->custom("update rb_products set stock_quantity = stock_quantity+{$quantity} where productid='{$productid}'");
						if(!$stockResult)
						{
							echo "Stock is not updated! Consult Administrator";
							exit();
						}
						$stockResult2 = $db->custom("update rb_products_variants set stock_quantity = stock_quantity+{$quantity} where productid='{$productid}' and variantid='{$variantid}'");
						if(!$stockResult2)
						{
							echo "Stock is not updated! Consult Administrator";
							exit();
						}
					}
					
					$checkPurchaseRow = $productPurchaseResult['result'][0];
					$regid = $checkPurchaseRow['regid'];
					$wallet_money = $checkPurchaseRow['wallet_money'];
					$purchaseid = $checkPurchaseRow['purchaseid'];
					
					if($wallet_money != "" and $wallet_money != "0" and $wallet_money != "0.00")
					{
						$fields3 = array('status'=>"declined", 'remarks'=>'Product Cancelled');
						$fields3['modifytime'] = $createtime;
						$fields3['modifydate'] = $createdate;
						
						$ewalletrequestResult = $db->update("mlm_ewallet_requests", $fields3, array('purchaseid'=>$purchaseid));
						if(!$ewalletrequestResult)
						{
							echo mysqli_error($connect);
							exit();
						}
						
						$registerwalletResult = $db->custom("update mlm_registrations set wallet_money = wallet_money+{$wallet_money} where regid='{$regid}'");
						if(!$registerwalletResult)
						{
							echo "Member Wallet is not added! Consult Administrator";
							exit();
						}
					}
				}
			}
		}
		
		$refno_customs = implode(',', $refno_customs);
		
		if($bulk_actions == "delete")
		{
			$purchaseQueryResult = $db->custom("DELETE from rb_purchases where FIND_IN_SET(`refno_custom`, '$refno_customs')");
			if(!$purchaseQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: purchase_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: purchase_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$purchaseQueryResult = $db->custom("UPDATE rb_purchases SET status='$bulk_actions' where FIND_IN_SET(`refno_custom`, '$refno_customs')");
			if(!$purchaseQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: purchase_view.php");
			exit();
		}
	}
}
else
{
	$fields = $_POST;
	
	foreach($fields as $key=>$value)
	{
		$fields_string .= $key.'='.$value.'&';
	}
	rtrim($fields_string, '&');
	$fields_string = str_replace("bulk_actions=&", "", $fields_string);
	$fields_string = substr($fields_string, 0, -1);
	
	header("Location: purchase_view.php?$fields_string");
	exit();
}
?>