<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "product";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$productid = $validation->urlstring_validate($_GET['productid']);
	
	$delresult = $media->multiple_filedeletion('rb_products', 'productid', $productid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('rb_products', 'productid', $productid, 'fileName', FILE_LOC);

	$productQueryResult = $db->delete("rb_products", array('productid'=>$productid));
	if(!$productQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: product_view.php");
		exit();
	}
	
	$productvariantsQueryResult = $db->delete("rb_products_variants", array('productid'=>$productid));
	if(!$productvariantsQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: product_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$productQueryResult} Record Deleted!";
	header("Location: product_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$productids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: product_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($productids, "$id");
				
				$delresult = $media->filedeletion('rb_products', 'productid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('rb_products', 'productid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($productids, "$id");
			}
		}
		
		$productids = implode(',', $productids);
		
		if($bulk_actions == "delete")
		{
			$productQueryResult = $db->custom("DELETE from rb_products where FIND_IN_SET(`productid`, '$productids')");
			if(!$productQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: product_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$productvariantsQueryResult = $db->custom("DELETE from rb_products_variants where FIND_IN_SET(`productid`, '$productids')");;
			if(!$productvariantsQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: product_view.php");
				exit();
			}
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: product_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$productQueryResult = $db->custom("UPDATE rb_products SET status='$bulk_actions' where FIND_IN_SET(`productid`, '$productids')");
			if(!$productQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: product_view.php");
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
	
	header("Location: product_view.php?$fields_string");
	exit();
}
?>