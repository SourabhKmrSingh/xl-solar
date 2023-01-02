<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "category";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$categoryid = $validation->urlstring_validate($_GET['categoryid']);
	
	$delresult = $media->filedeletion('rb_categories', 'categoryid', $categoryid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('rb_categories', 'categoryid', $categoryid, 'fileName', FILE_LOC);

	$categoryQueryResult = $db->delete("rb_categories", array('categoryid'=>$categoryid));
	if(!$categoryQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: category_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$categoryQueryResult} Record Deleted!";
	header("Location: category_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$categoryids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: category_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($categoryids, "$id");
				
				$delresult = $media->filedeletion('rb_categories', 'categoryid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('rb_categories', 'categoryid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($categoryids, "$id");
			}
		}
		
		$categoryids = implode(',', $categoryids);
		
		if($bulk_actions == "delete")
		{
			$categoryQueryResult = $db->custom("DELETE from rb_categories where FIND_IN_SET(`categoryid`, '$categoryids')");
			if(!$categoryQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: category_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: category_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$categoryQueryResult = $db->custom("UPDATE rb_categories SET status='$bulk_actions' where FIND_IN_SET(`categoryid`, '$categoryids')");
			if(!$categoryQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: category_view.php");
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
	
	header("Location: category_view.php?$fields_string");
	exit();
}
?>