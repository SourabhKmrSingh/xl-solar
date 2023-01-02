<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "slider";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$sliderid = $validation->urlstring_validate($_GET['sliderid']);
	
	$delresult = $media->filedeletion('rb_sliders', 'sliderid', $sliderid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('rb_sliders', 'sliderid', $sliderid, 'fileName', FILE_LOC);

	$sliderQueryResult = $db->delete("rb_sliders", array('sliderid'=>$sliderid));
	if(!$sliderQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: slider_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$sliderQueryResult} Record Deleted!";
	header("Location: slider_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$sliderids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: slider_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($sliderids, "$id");
				
				$delresult = $media->filedeletion('rb_sliders', 'sliderid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('rb_sliders', 'sliderid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($sliderids, "$id");
			}
		}
		
		$sliderids = implode(',', $sliderids);
		
		if($bulk_actions == "delete")
		{
			$sliderQueryResult = $db->custom("DELETE from rb_sliders where FIND_IN_SET(`sliderid`, '$sliderids')");
			if(!$sliderQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: slider_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: slider_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$sliderQueryResult = $db->custom("UPDATE rb_sliders SET status='$bulk_actions' where FIND_IN_SET(`sliderid`, '$sliderids')");
			if(!$sliderQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: slider_view.php");
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
	
	header("Location: slider_view.php?$fields_string");
	exit();
}
?>