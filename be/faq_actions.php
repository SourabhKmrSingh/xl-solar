<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "faq";

$q = $validation->urlstring_validate($_GET['q']);
if($q == "del")
{
	echo $validation->delete_permission();
	
	$faqid = $validation->urlstring_validate($_GET['faqid']);
	
	$delresult = $media->filedeletion('rb_faqs', 'faqid', $faqid, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
	$delresult2 = $media->filedeletion('rb_faqs', 'faqid', $faqid, 'fileName', FILE_LOC);

	$faqQueryResult = $db->delete("rb_faqs", array('faqid'=>$faqid));
	if(!$faqQueryResult)
	{
		$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
		header("Location: faq_view.php");
		exit();
	}

	$_SESSION['success_msg'] = "{$faqQueryResult} Record Deleted!";
	header("Location: faq_view.php");
	exit();
}

if(isset($_POST['bulk_actions']) and $_POST['bulk_actions'] != "")
{
	$bulk_actions = $validation->urlstring_validate($_POST['bulk_actions']);
	$del_items = $_POST['del_items'];
	$faqids = array();
	if(empty($del_items))
	{
		$_SESSION['error_msg'] = "Please select atleast one row to perform action!";
		header("Location: faq_view.php");
		exit();
	}
	if(isset($del_items) and $del_items != "")
	{
		foreach($del_items as $id)
		{
			if($bulk_actions == "delete")
			{
				array_push($faqids, "$id");
				
				$delresult = $media->filedeletion('rb_faqs', 'faqid', $id, 'imgName', IMG_MAIN_LOC, IMG_THUMB_LOC);
				$delresult2 = $media->filedeletion('rb_faqs', 'faqid', $id, 'fileName', FILE_LOC);
			}
			else if($bulk_actions == "active" || $bulk_actions == "inactive")
			{
				array_push($faqids, "$id");
			}
		}
		
		$faqids = implode(',', $faqids);
		
		if($bulk_actions == "delete")
		{
			$faqQueryResult = $db->custom("DELETE from rb_faqs where FIND_IN_SET(`faqid`, '$faqids')");
			if(!$faqQueryResult)
			{
				$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
				header("Location: faq_view.php");
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Deleted!";
			header("Location: faq_view.php");
			exit();
		}
		else if($bulk_actions == "active" || $bulk_actions == "inactive")
		{
			$faqQueryResult = $db->custom("UPDATE rb_faqs SET status='$bulk_actions' where FIND_IN_SET(`faqid`, '$faqids')");
			if(!$faqQueryResult)
			{
				echo mysqli_error($connect);
				exit();
			}
			$affected_rows = $connect->affected_rows;
			
			$_SESSION['success_msg'] = "{$affected_rows} Record(s) Updated!";
			header("Location: faq_view.php");
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
	
	header("Location: faq_view.php?$fields_string");
	exit();
}
?>