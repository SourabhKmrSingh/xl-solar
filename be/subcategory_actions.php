<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "subcategory";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$subcategoryid = $validation->urlstring_validate($_GET['subcategoryid']);
	
	$delresult = $media->filedeletion('rb_subcategories', 'subcategoryid', $subcategoryid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('rb_subcategories', 'subcategoryid', $subcategoryid, 'fileName', FILE_LOC);
	
	$subcategoryQueryResult = $db->delete("rb_subcategories", array('subcategoryid'=>$subcategoryid));
	if(!$subcategoryQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: subcategory_view.php");
		exit();
	}
	
	$_SESSION['success_msg'] = "{$subcategoryQueryResult} Record Deleted!";
	header("Location: subcategory_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$subcategoryids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: subcategory_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($subcategoryids, "$id");
				
				$delresult = $media->filedeletion('rb_subcategories', 'subcategoryid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('rb_subcategories', 'subcategoryid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($subcategoryids, "$id");
			}
		}
		
		$subcategoryids = implode(',', $subcategoryids);
		
		if($bulk_actions == "delete")
		{
			$subcategoryQueryResult = $db->custom("DELETE from rb_subcategories where FIND_IN_SET(`subcategoryid`, '$subcategoryids')");
			if(!$subcategoryQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: subcategory_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: subcategory_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$subcategoryQueryResult = $db->custom("UPDATE rb_subcategories SET status='$bulk_actions' where FIND_IN_SET(`subcategoryid`, '$subcategoryids')");
			if(!$subcategoryQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: subcategory_view.php");
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
	
	header("Location: subcategory_view.php?$fields_string");
	exit();
}
?>