<?php
include_once("inc_config.php");
include_once("login_user_check.php");


$_SESSION['active_menu'] = "dashboard";
$membership_id = $_SESSION['mlm_membership_id'];

$registerResult = $db->view('regid,sponsor_id, status, members,wallet_total,wallet_money,total_debit,membership_id,createdate,createtime,rewardid', 'mlm_registrations', 'regid', " and regid = '$regid'");
$registerRow = $registerResult['result'][0];


$businessVolumeResult = $db->view("sum(business_volume) as total_amount", "rb_purchases", "purchaseid", " and income_type = 'repurchase' and tracking_status = 'delivered' and invoicedate = '$createdate'");

$membership_id = $registerRow['membership_id'];

$TotalMemberResult = $db->view('*', 'mlm_registrations', 'regid', " and sponsor_id = '{$membership_id}'");
$TotalMember = $TotalMemberResult['num_rows'];


$directIncomeResult = $db->view("sum(amount) as total_amount", "mlm_ewallet", "ewalletid", " and membership_id = '$membership_id' and level = '1'");
$directIncomeRow = $directIncomeResult['result'][0];

	
$ActivePurchaseDetailResult = $db->view("*", "rb_purchases", "purchaseid", " and tracking_status ='delivered' and membership_id = '$membership_id' and income_type = 'level'", "purchaseid asc", 1);
$ActivePurchaseDetailRow = $ActivePurchaseDetailResult['result'][0];


$creditResult = $db->view("SUM(amount) as total_credit_amount", "mlm_ewallet", "ewalletid", "and type='credit' and regid = '$regid' and reason='REPURCHASE INCOME'");
$creditRow = $creditResult['result'][0];


$creditResultSingleLeg = $db->view("SUM(amount) as total_amount", "mlm_ewallet", "ewalletid", "and type='credit' and regid = '$regid' and reason = 'Reward'");
$creditRowSingleLeg = $creditResultSingleLeg['result'][0];


$creditResultroyal = $db->view("SUM(amount) as total_amount", "mlm_ewallet", "ewalletid", "and type='credit' and regid = '$regid' and reason = 'Challenge'");
$creditRowRoyal = $creditResultroyal['result'][0];


$debitResult = $db->view("SUM(amount) as total_debit_amount", "mlm_ewallet", "ewalletid", "and type='debit' and regid = '$regid'");
$debitRow = $debitResult['result'][0];

$levelIncomeResult = $db->view("SUM(amount) as total_amount", "mlm_ewallet", "ewalletid", " and type='credit' and regid = '$regid' and reason='LEVEL INCOME'");
$levelIncomeRow = $levelIncomeResult['result'][0];



$enquiryResult = $db->view('enquiryid', 'mlm_enquiries', 'enquiryid');
$enquiryCount = $enquiryResult['num_rows'];




$totalwithdrawnResult = $db->view("SUM(amount) as total_credit_amount", "mlm_ewallet_requests", "requestid", " and regid = '$regid' and status='fulfilled'");
$totalwithdrawn = $totalwithdrawnResult['result'][0]['total_credit_amount'];



if($totalwithdrawn == ''){
	$totalwithdrawn = 0;
}

$totalDownlineMember = 0;

function getAllDownlines($parent)
{
    global $db, $totalDownlineMember;
    $treeResult = $db->view('membership_id,imgName,username,status', 'mlm_registrations', 'regid', "and sponsor_id='$parent'", 'regid asc');
    // echo "<script>alert('$parent')</script>";

	if($treeResult['num_rows'] >= 1)
	{
		foreach($treeResult['result'] as $treeRow)
		{
			$totalDownlineMember++; 
			getAllDownlines($treeRow['membership_id']);
		}
	}
	return $totalDownlineMember;
}

getAllDownlines($membership_id);



$registerDate = $registerRow['createdate'];
$registertime = $registerRow['createtime'];


$singleLegTeamQuery =  $db->view('*','mlm_registrations','regid'," and concat(createdate,' ',createtime) > '{$registerDate} {$registertime}' and status='active'");

$singleLegTeam = $singleLegTeamQuery['num_rows'];


$total_DownlineQuery  =  $db->view('*','mlm_registrations','regid'," and concat(createdate,' ',createtime) > '{$registerDate} {$registertime}'");

$total_Downline = $total_DownlineQuery['num_rows'];

$totalActiveMember = 0;

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
			<h1 CLASS="page-header">Dashboard</h1>
		</div>
	</div>
	<br>
	
	<?php if($_SESSION['mlm_account_number'] == "" || $_SESSION['mlm_document'] == "" || $_SESSION['pan_card_number'] == "") { ?>
		<!-- <div CLASS="row mb-3">
			<div CLASS="col-12 text-center">
				<p class="font-weight-bold"><font color="red"><?php if($_SESSION['mlm_account_number'] == "") echo "Bank Details,"; if($_SESSION['mlm_document'] == "") echo " KYC Details";if($_SESSION['pan_card_number'] == "") echo " Pan Card Details"; ?> are mandatory otherwise you will not get any amount in your account. Please complete your profile from <a href="profile.php">here</a></font></p>
			</div>
		</div> -->
	<?php } ?>

	<?php if($registerRow['rewardid'] >=1){?>
		<!-- <p class='text-dark text-center'><b>Note: </b> Single Leg Income has been stopped until <b>12 June 2021</b> </p> -->
	<?php }?>
	
	<div CLASS="row">
		<div CLASS="col-lg-3 col-md-6 mb-1 mb-md-0 ">
			<div class="card overflow-hidden" style="max-height: 165px;border-radius:5px;">
				<div class="card-heading  bg-info text-light">
					<div class="row">
						<div class="col">
							<h6 class=""><?php echo $validation->db_field_validate($registerRow['first_name'] . ' ' . $registerRow['last_name']); ?></h6>
							<h6 class="mb-2 number-font">Membership ID: <?php echo $validation->db_field_validate($registerRow['membership_id']); ?></h6>
							<p class=" mb-0">
								<span class="">Sponsor ID: <?php echo $validation->db_field_validate($registerRow['sponsor_id']); ?></span>
							</p>
							<p class=" mb-0">
								<span class="font-weight-bold">Status: <?php echo $validation->db_field_validate(ucfirst($registerRow['status'])); ?></span>&nbsp;&nbsp;&nbsp;
							</p>
							
							<?php if($registerRow['status'] == 'active'):
								$productid = $ActivePurchaseDetailRow['productid'];
								$productResult = $db->view("title", "rb_products", 'productid', " and productid = '$productid'");
								$productRow = $productResult['result'][0];
								
								?>
								<?php if($productRow['title'] != ""){?>
								<p class="font-weight-bold mb-0">Product: <?= substr($productRow['title'], 0, 30) . "...";?></p>
								<p class="font-weight-bold">Business Volume: <?= $ActivePurchaseDetailRow['business_volume'];?></p>
								<?php }?>
							<?php endif;?>

						</div>
						
					</div>
				</div>
			</div>
		</div><br>

		<div CLASS="col-lg-3 col-md-6 mb-1">
			<div CLASS="card card-blue">
				<div CLASS="card-heading bg-info">
					<div CLASS="row ">
						<div CLASS="col-3 ">
							<i CLASS="fa fa-network-wired fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge"><?php echo $total_Downline; ?></div>
							<div>Total Downline Team!</div>
						</div> 	
					</div>
				</div>
				<a HREF="downline_member_view.php">
					<div CLASS="card-footer">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a> 	
				
			</div>
		</div>
		
		<div CLASS="col-lg-3 col-md-6 mb-1 mb-md-0">
			<div CLASS="card card-blue">
				<div CLASS="card-heading">
					<div CLASS="row ">
						<div CLASS="col-3">
						<i class="fas fa-money-bill fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge">₹<?php echo $registerRow['wallet_total']; ?></div>
							<div>Total Income!</div>
						</div> 	
					</div>
				</div>
				<a HREF="ewallet_view.php?type=credit">
					<div CLASS="card-footer">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div CLASS="col-lg-3 col-md-6 mb-1 mb-md-0">
			<div CLASS="card card-blue">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
						<i class="fas fa-money-bill fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge">₹<?php echo $registerRow['wallet_money']; ?></div>
							<div>Total Wallet Balance!</div>
						</div> 	
					</div>
				</div>
				<a HREF="wallet_transfer_view.php">
					<div CLASS="card-footer ">
						<span CLASS="float-left">Transfer Money</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div CLASS="col-lg-3 col-md-6 mb-1 mb-md-0">
			<div CLASS="card card-green">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
						<i class="fas fa-money-bill fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge">₹<?php echo $totalwithdrawn; ?></div>
							<div>Total Withdrawn !</div>
						</div> 	
					</div>
				</div>
				<a HREF="ewallet_request_view.php">
					<div CLASS="card-footer ">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a> 
			</div>
		</div>

		<div CLASS="col-lg-3 col-md-6 mb-1 mb-md-0">
			<div CLASS="card card-blue">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fa fa-network-wired fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge"><?php echo $TotalMember; ?></div>
							<div>My Direct team!</div>
						</div>
						
					</div>
				</div>
				<a HREF="direct_member_view.php">
					<div CLASS="card-footer ">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a> 	
			</div>
		</div>

	
		<div CLASS="col-lg-3 col-md-6 mb-1">
			<div CLASS="card card-green">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fas fa-wallet fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge">&#8377;<?php echo $validation->price_format($directIncomeRow['total_amount']); ?></div>
							<div>Direct Income!</div>
						</div>
					</div>
				</div>
				<a HREF="ewallet_view.php?type=credit&reason=LEVEL INCOME&level=1">
					<div CLASS="card-footer">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div CLASS="col-lg-3 col-md-6 mb-1">
			<div CLASS="card card-green">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fas fa-wallet fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge">&#8377;<?php echo $validation->price_format($creditRow['total_credit_amount']); ?></div>
							<div>Repurchase Income!</div>
						</div>
					</div>
				</div>
				<a HREF="ewallet_view.php?type=credit&reason=REPURCHASE INCOME">
					<div CLASS="card-footer">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
	
		<div CLASS="col-lg-3 col-md-6 mb-1">
			<div CLASS="card card-green">
				<div CLASS="card-heading bg-success">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fas fa-wallet fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge">&#8377;<?php echo $validation->price_format($levelIncomeRow['total_amount']); ?></div>
							<div>LEVEL INCOME!!</div>
						</div>
					</div>
				</div>
				<a HREF="level_income_view.php">
					<div CLASS="card-footer">
						<span CLASS="float-left">Earn More</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>

		<div CLASS="col-lg-4 col-md-6 mb-4">
			<div CLASS="card card-green">
				<div CLASS="card-heading bg-success">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fas fa-wallet fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge">&#8377;<?php echo $validation->price_format($businessVolumeResult['result'][0]['total_amount']); ?></div>
							<div>Company Daily Repurchasing !!</div>
						</div>
					</div>
				</div>
				<a HREF="repurchase_sales_view.php" >
					<div CLASS="card-footer ">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		
		<div CLASS="col-lg-3 col-md-6 mb-3 mb-md-0">
			<div CLASS="card card-red">
				<div CLASS="card-heading">
					<div CLASS="row">
						<div CLASS="col-3">
							<i CLASS="fa fa-envelope fa-5x"></i>
						</div>
						<div CLASS="col-9 text-right">
							<div CLASS="huge"><?php echo $enquiryCount; ?></div>
							<div>Enquiries/Tickets!</div>
						</div>
					</div>
				</div>
				<a HREF="enquiry_view.php">
					<div CLASS="card-footer">
						<span CLASS="float-left">View All</span>
						<span CLASS="float-right"><i CLASS="fa fa-arrow-circle-right"></i></span>
						<div CLASS="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
	</div>
	<div class="col-12 text-center mt-3">
		<p class="text">Referral link : <a href="<?php echo BASE_URL . 'register' . SUFFIX . '?id=' . $validation->db_field_validate($_SESSION['mlm_membership_id']); ?>" target="_blank"><?php echo BASE_URL . 'register' . SUFFIX . '?id=' . $validation->db_field_validate($_SESSION['mlm_membership_id']); ?></a>&nbsp;&nbsp; - &nbsp;&nbsp;
		<a href='#' class='btn btn-info' onClick='copyLink("<?php echo BASE_URL . 'register' . SUFFIX . '?id=' . $validation->db_field_validate($_SESSION['mlm_membership_id']); ?>");'><i class="fas fa-copy"></i></a>
			<?php 
				$link = BASE_URL . 'register' . SUFFIX . '?id=' . $validation->db_field_validate($_SESSION['mlm_membership_id']);
				$referral_link = rawurlencode($link);
				
			?>
			<p>Share on: </p>
			<a href="https://api.whatsapp.com/send?text=<?php echo $referral_link;?>" class='btn btn-success' target='_blank'><i class="fab fa-whatsapp " ></i></a>
			<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $referral_link;?>" class='btn btn-primary' target="_blank">
				<i class="fab fa-facebook" ></i>
			</a>
		</p>
	</div>


	
</div>
</div>
<script>
   function copyLink(str){
      	const el = document.createElement('textarea');
     	el.value = str;
		document.body.appendChild(el);
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
		$.notify("Link Copied!", { className: 'success', autoHide: true, autoHideDelay: 1000 });
    }
</script>
</div>
</body>
</html>