<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "pages";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$pageid = $validation->urlstring_validate($_GET['pageid']);
	
	$delresult = $media->filedeletion('rb_pages', 'pageid', $pageid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('rb_pages', 'pageid', $pageid, 'fileName', FILE_LOC);

	$pagesQueryResult = $db->delete("rb_pages", array('pageid'=>$pageid));
	if(!$pagesQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: page_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$pagesQueryResult} Record Deleted!";
	header("Location: page_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$pageids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: page_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($pageids, "$id");
				
				$delresult = $media->filedeletion('rb_pages', 'pageid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('rb_pages', 'pageid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($pageids, "$id");
			}
		}
		
		$pageids = implode(',', $pageids);
		
		if($bulk_actions == "delete")
		{
			$pagesQueryResult = $db->custom("DELETE from rb_pages where FIND_IN_SET(`pageid`, '$pageids')");
			if(!$pagesQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: page_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: page_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$pagesQueryResult = $db->custom("UPDATE rb_pages SET status='$bulk_actions' where FIND_IN_SET(`pageid`, '$pageids')");
			if(!$pagesQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: page_view.php");
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
	
	header("Location: page_view.php?$fields_string");
	exit();
}
?>