<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "pincode";

if(isset($_GET['mode']))
{
	$mode = $validation->urlstring_validate($_GET['mode']);
}
else
{
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: pincode_view.php");
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
	if(isset($_GET['pincodeid']))
	{
		$pincodeid = $validation->urlstring_validate($_GET['pincodeid']);
		if($_SESSION['search_filter'] != "")
		{
			$search_filter = "?".$_SESSION['search_filter'];
		}
	}
	else
	{
		$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
		header("Location: pincode_view.php");
		exit();
	}
}

$pincode = $validation->input_validate($_POST['pincode']);
if($pincode=='')
{
	$pincode = 0;
}
$area = $validation->input_validate($_POST['area']);
$city = $validation->input_validate($_POST['city']);
$state = $validation->input_validate($_POST['state']);
$country = $validation->input_validate($_POST['country']);
if(isset($_POST['priority']))
{
	$priority = 1;
}
else
{
	$priority = 0;
}
$status = $validation->input_validate($_POST['status']);

$user_ip_array = ($_POST['user_ip']!='') ? explode(", ", $validation->input_validate($_POST['user_ip'])) : array();
array_push($user_ip_array, $user_ip);
$user_ip_array = array_unique($user_ip_array);
$user_ip = implode(", ", $user_ip_array);

$dupresult = $db->check_duplicates('rb_pincodes', 'pincodeid', $pincodeid, 'pincode', strtolower($pincode), $mode);
if($dupresult >= 1)
{
	$_SESSION['error_msg'] = "Pincode already exists!";
	header("Location: pincode_view.php");
	exit();
}

$fields = array('pincode'=>$pincode, 'area'=>$area, 'city'=>$city, 'state'=>$state, 'country'=>$country, 'priority'=>$priority, 'status'=>$status, 'user_ip'=>$user_ip);

if($mode == "insert")
{
	$fields['userid'] = $userid;
	$fields['createtime'] = $createtime;
	$fields['createdate'] = $createdate;
	
	$pincodeQueryResult = $db->insert("rb_pincodes", $fields);
	if(!$pincodeQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Added!";
	header("Location: pincode_view.php");
	exit();
}
else if($mode == "edit")
{
	$fields['userid_updt'] = $userid;
	$fields['modifytime'] = $createtime;
	$fields['modifydate'] = $createdate;
	
	$pincodeQueryResult = $db->update("rb_pincodes", $fields, array('pincodeid'=>$pincodeid));
	if(!$pincodeQueryResult)
	{
		echo mysqli_error($connect);
		exit();
	}
	
	$_SESSION['success_msg'] = "Record Updated!";
	header("Location: pincode_view.php$search_filter");
	exit();
}
?>