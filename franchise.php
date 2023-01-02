<?php
include_once("inc_config.php");

@$q = strtolower($validation->urlstring_validate($_GET['q']));
if ($_SESSION['regid'] == "") {
    $_SESSION['error_msg_fe'] = "Login to continue!";
    header("Location: {$base_url}login{$suffix}?url={$full_url}");
    exit();
}


$pageid = "franchise";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if ($pageResult['num_rows'] == 0) {
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageRow = $pageResult['result'][0];

$_SESSION['csrf_token'] = substr(sha1(rand(1, 99999)), 0, 32);
$csrf_token = $_SESSION['csrf_token'];
$regid = $_SESSION['regid'];

$franchiseQuery = $db->view('*','mlm_franchise','franchiseid');
$registerRowQuery = $db->view('*','rb_registrations','regid'," and regid={$regid}");
$registerRow = $registerRowQuery['result'][0];

$table = "rb_franchise_purchase";
$id = "fpurchaseid";
$url_parameters = "";
$where_query = " and regid={$regid}";
$orderby_final = "fpurchaseid desc";


$data = $pagination->main($table, $url_parameters, $where_query, $id, $orderby_final);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php if ($pageRow['meta_title'] != "") { ?>
        <title><?php echo $validation->db_field_validate($pageRow['meta_title']); ?></title>
        <meta name="keywords" content="<?php echo $validation->db_field_validate($pageRow['meta_keywords']); ?>" />
        <meta name="description" content="<?php echo $validation->db_field_validate($pageRow['meta_description']); ?>" />
    <?php } else { ?>
        <title><?php echo $validation->db_field_validate($pageRow['title']) . " | ";
                include_once("inc_title.php"); ?></title>
        <meta name="keywords" content="<?php echo $validation->db_field_validate($pageRow['title']); ?>" />
    <?php } ?>
    <?php include_once("inc_files.php"); ?>
    <style>
        .franchise-box{
            transition: background .365s ease-in-out;
        }
        .franchise-box:hover{
            border: 1px solid #008f02;
            background-color: #008f02;
        }
        .franchise-box:hover p,
        .franchise-box:hover h5{
            color: #fff!important;
        }
        
    </style>
</head>
<body>
    <div class="page-wrapper">
    <?php include_once("inc_header.php"); ?>
    <main>
        <div class="page-header text-center" style="background-image: url('<?php echo BASE_URL;?>assets/images/page-header-bg.jpg')">
            <div class="container">
                <h1 class="page-title"><?php echo $validation->db_field_validate($pageRow['title']); ?><!-- <span>Pages</span> --></h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-0">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $validation->db_field_validate($pageRow['title']); ?></li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->


            <div class="page-content">
            	<!-- <div id="map" class="mb-5"></div> --><!-- End #map -->
                <div class="container-fluid">
                <h5 class='mt-3'>Click on any Franchise Plan to Order: </h5>
                	<div class="row mt-5">
                    
                    <?php
                    if($franchiseQuery['num_rows']>= 1){
                        $slr=1;
                        foreach($franchiseQuery['result'] as $franchiseRow)
                        {
                            $planid = $franchiseRow['planid'];
                            $planQuery = $db->view("amount",'mlm_plans','planid'," and planid={$planid}");
                            $amount = $planQuery['result'][0]['amount'];
                    ?>
                            <div class="home-box col-3 franchise-box"  onClick ="formSubmit('#franchise_<?php echo $slr;?>')">
                            
                                <form action="<?php echo BASE_URL;?>franchise_inter.php" method="post" id="franchise_<?php echo $slr;?>">
                                <input type="hidden" name='token' value='<?php echo $csrf_token;?>'>
                                <input type="hidden" name='franchiseid' value='<?php echo $franchiseRow['franchiseid'];?>'>
                                <input type="hidden" name='regid' value='<?php echo $registerRow['regid'];?>'>
                                <input type="hidden" name='membership_id' value='<?php echo $registerRow['membership_id'];?>'>
                                <input type="hidden" name='sponsor_id' value='<?php echo $registerRow['sponsor_id'];?>'>
                                <input type="hidden" name='billing_first_name' value='<?php echo $registerRow['first_name'];?>'>
                                <input type="hidden" name='billing_last_name' value='<?php echo $registerRow['last_name'];?>'>
                                <input type="hidden" name='billing_mobile' value='<?php echo $registerRow['mobile'];?>'>
                                <input type="hidden" name='billing_mobile_alter' value='<?php echo $registerRow['mobile_alter'];?>'>
                                <input type="hidden" name='billing_address' value='<?php echo $registerRow['address'];?>'>
                                <input type="hidden" name='billing_landmark' value='<?php echo $registerRow['landmark'];?>'>
                                <input type="hidden" name='billing_city' value='<?php echo $registerRow['city'];?>'>
                                <input type="hidden" name='billing_state' value='<?php echo $registerRow['state'];?>'>
                                <input type="hidden" name='billing_country' value='<?php echo $registerRow['country'];?>'>
                                <input type="hidden" name='billing_pincode' value='<?php echo $registerRow['pincode'];?>'>
                                    <div class="box-column d-block">
                                        <h5>Franchise Title: <strong><?php echo $validation->db_field_validate($franchiseRow['title']); ?></strong></h5>
                                        <p >Total Pins: <strong><?php echo $validation->db_field_validate($franchiseRow['total_pins']); ?></strong></p>
                                        <p >Free Pins: <strong><?php echo $validation->db_field_validate($franchiseRow['free']); ?></strong></p>
                                        <p>Total Amount: &#8377; <?php echo $amount * $franchiseRow['total_pins'];?></p>
                                        
                                    </div>
                                </form>
                            </div>
                        
                    <?php
                    $slr++;
                        }
                    }else
                        {
                    ?>
                        <p class="text-center font-weight-bold mt-5 mb-5">No Franchise Plan Found!</p>
                    <?php
                    }
                    ?>
                    
                	</div><!-- End .row -->
                    <div class='row'>
                        <div class="col-md-12">
                            <table class='table border-1'>
                                <thead class='w-100 bg-dark'>
                                    <tr class='text-light p-2'>
                                        <th>S.No.</th>
                                        <th>Order ID</th>
                                        <th>Franchise Title</th>
                                        <th>Total Pins</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody class='w-100'>
                                    <?php
                                    $slr=1;
                                    if($data['num_rows']>=1){
                                        foreach($data['result'] as $fpurchaseRow){ 
                                    ?>

                                        <tr>
                                            <td><?php echo $slr;?></td>
                                            <td>#<?php echo $fpurchaseRow['refno'];?></td>
                                            <td><?php echo $fpurchaseRow['franchiseTitle'];?></td>
                                            <td><?php echo $fpurchaseRow['totalPins'];?></td>
                                            <td>&#8377;<?php echo $validation->price_format($fpurchaseRow['totalPrice']); ?></td>
                                            <td class="<?php if($fpurchaseRow['order_status'] == "pending"){ echo "text-danger"; }else{ echo "text-success";}?>""><?php if($fpurchaseRow['order_status'] == "pending"){ echo "Pending"; }else if($fpurchaseRow['order_status'] == "cancelled"){ echo "Cancelled";}else { echo "Fullfilled";}?></td>
                                            <td><?php echo $validation->date_format_custom($fpurchaseRow['createdate']); ?> <br class="mb-hidden" />(<?php echo $validation->timecount("{$fpurchaseRow['createdate']} {$fpurchaseRow['createtime']}"); ?>)</td>
                                        </tr>

                                    <?php 
                                            $slr++;}
                                        }else{
                                    ?>
                                         <tr>
                                            <td class="text-center" colspan="6">No Record Found!</td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- End .container -->
            </div><!-- End .page-content -->
          </main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
<script>
function formSubmit(id){
    $(id).submit();
}

</script>
</body>
</html>