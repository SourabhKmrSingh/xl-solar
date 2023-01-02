<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "ewallet_request";
echo $validation->section($_SESSION['per_ewallet']);

$requestid = $validation->urlstring_validate($_GET['requestid']);
$requestResult = $db->view('*', 'mlm_ewallet_requests', 'requestid', "and requestid = '$requestid'");
$requestRow = $requestResult['result'][0];
?>
<!DOCTYPE html>
<html LANG="en">
<head>
<?php include_once("inc_title.php"); ?>
<?php include_once("inc_files.php"); ?>
</head>
<body>
<div ID="wrapper">
<?php include_once("inc_header.php"); ?>
<div ID="page-wrapper">
<div CLASS="container-fluid">
<div CLASS="row">
	<div CLASS="col-lg-12">
		<h1 CLASS="page-header">E-Wallet Request Money</h1>
	</div>
</div>

<form name="dataform" method="post" class="form-group" action="ewallet_request_form_inter.php" enctype="multipart/form-data">
<input type="hidden" name="requestid" value="<?php echo $requestid; ?>" />
<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="remarks">Remarks</label>
		</div>
		<div class="col-sm-9">
			<textarea name="remarks" id="remarks" class="form-control"></textarea>
		</div>
	</div>
	
	<div class="row mt-4 mb-4">
		<div class="col-sm-12">
			<button type="submit" name="submit" class="btn btn-default btn-sm mr-2 btn_submit">&nbsp;Decline</button>
		</div>
	</div>
</div>
</form>
</div>
</div>
</div>

</body>
</html>