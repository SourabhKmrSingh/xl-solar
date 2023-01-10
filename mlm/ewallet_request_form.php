<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "ewallet_request";

$registerQueryResult = $db->view('*', 'mlm_registrations', 'regid', "and regid = '$regid'");
$registerRow = $registerQueryResult['result'][0];

// $walletResult = $db->view("SUM(amount) as total_wallet_amount", "mlm_ewallet", "regid", "and type='credit' and regid='{$regid}'");
// $walletRow = $walletResult['result'][0];
// $total_wallet_amount = $walletRow['total_wallet_amount'];

// $walletrequestsResult = $db->view("SUM(amount) as total_requests_amount", "mlm_ewallet_requests", "regid", "and status != 'declined' and regid='{$regid}'");
// $walletrequestsRow = $walletrequestsResult['result'][0];
// $total_requests_amount = $walletrequestsRow['total_requests_amount'];

$totalwalletResult = $db->view('wallet_total,wallet_money', 'mlm_registrations', 'regid', "and regid = '$regid' and status='active'");
$totalwalletRow = $totalwalletResult['result'][0];

$redeemable_amount = $totalwalletRow['wallet_money'] * $configRow['redeemable_amount'] / 100;

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
		<h1 CLASS="page-header">Withdrawn </h1>
	</div>
</div>

<?php if($_SESSION['mlm_account_number'] == "" || $_SESSION['mlm_document'] == "" || $_SESSION['pan_card_number'] == "") { ?>
	<div CLASS="row mb-3">
		<div CLASS="col-12 text-center">
			<p class="font-weight-bold"><font color="red"><?php if($_SESSION['mlm_account_number'] == "") echo "Bank Details,"; if($_SESSION['mlm_document'] == "") echo " KYC Details"; if($_SESSION['pan_card_number'] == "") echo " Pan Card Details"; ?> are mandatory otherwise you will not get any amount in your account. Please complete your profile from <a href="profile.php">here</a></font></p>
		</div>
	</div>
<?php } ?>

<form name="dataform" method="post" class="form-group" action="ewallet_request_form_inter.php" enctype="multipart/form-data">
<input type="hidden" name="membership_id" value="<?php echo $validation->db_field_validate($registerRow['membership_id']); ?>" />
<input type="hidden" name="mobile" value="<?php echo $validation->db_field_validate($registerRow['mobile']); ?>" />
<input type="hidden" name="bank_name" value="<?php echo $validation->db_field_validate($registerRow['bank_name']); ?>" />
<input type="hidden" name="account_number" value="<?php echo $validation->db_field_validate($registerRow['account_number']); ?>" />
<input type="hidden" name="ifsc_code" value="<?php echo $validation->db_field_validate($registerRow['ifsc_code']); ?>" />
<input type="hidden" name="account_name" value="<?php echo $validation->db_field_validate($registerRow['account_name']); ?>" />
<input type="hidden" name="balance" value="<?php echo $totalwalletRow['wallet_money']; ?>" />
<input type="hidden" name="redeemable_amount" value="<?php echo $redeemable_amount;?>" />
<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-sm-3">
			<label>Total Balance</label>
		</div>
		<div class="col-sm-9">
			<p class="text">&#8377;<?php echo $validation->price_format($totalwalletRow['wallet_money']); ?></p>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-sm-3">
			<label for="amount">Amount *</label>
		</div>
		<div class="col-sm-9">
			<input type="text" name="amount" id="amount" class="form-control" value="<?php echo $validation->price_format($validation->calculate_discounted_price($configRow['redeemable_amount'], ($totalwalletRow['wallet_money']))); ?>" required />
			<em class="d-block mt-1">Minimum Transaction should be &#8377;<?php echo $configRow['min_wallet_amount']; ?>.<br />You can redeem maximum of <?php echo $configRow['redeemable_amount'];?>% i.e. &#8377;<?php echo $validation->price_format($validation->calculate_discounted_price($configRow['redeemable_amount'], ($totalwalletRow['wallet_money']))); ?> amount from your wallet. <br> You can only redeem amount which  are mulitple of 300.</em>
		</div>
	</div>
	
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
			<button type="submit" name="submit" class="btn btn-default btn-sm mr-2 btn_submit" <?php if($_SESSION['mlm_account_number'] == "" || $_SESSION['mlm_document'] == "" || $_SESSION['pan_card_number'] == "") echo "disabled"; ?>>&nbsp;Request</button>
		</div>
	</div>
</div>
</form>
</div>
</div>
</div>

</body>
</html>