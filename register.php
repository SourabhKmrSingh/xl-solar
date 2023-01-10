<?php
include_once("inc_config.php");

if($_SESSION['regid'] != "")
{
    header("Location: {$base_url}home{$suffix}");
    exit();
}

$pageid = "register";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_idr='$pageid'", '', '1');
if($pageResult['num_rows'] == 0)
{
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageRow = $pageResult['result'][0];

$redirect_url = $validation->urlstring_validate($_GET['url']);
$q = $validation->urlstring_validate($_GET['q']);
$id = $validation->urlstring_validate($_GET['id']);

$_SESSION['csrf_token'] = substr(sha1(rand(1, 99999)),0,32);
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php if($pageRow['meta_title'] != "") { ?>
<title><?php echo $validation->db_field_validate($pageRow['meta_title']); ?></title>
<meta name="keywords" content="<?php echo $validation->db_field_validate($pageRow['meta_keywords']); ?>" />
<meta name="description" content="<?php echo $validation->db_field_validate($pageRow['meta_description']); ?>" />
<?php } else { ?>
<title><?php echo $validation->db_field_validate($pageRow['title'])." | "; include_once("inc_title.php"); ?></title>
<meta name="keywords" content="<?php echo $validation->db_field_validate($pageRow['title']); ?>" />
<?php } ?>
    <?php include_once("inc_files.php");?>
</head>
<body>
    <div class="page-wrapper">
        <?php include_once("inc_header.php");?>
        <main class="main">
            <div class="login-page bg-image pt-8 pb-8 pt-md-12 pb-md-12 pt-lg-17 pb-lg-17" style="background-image: url('<?php echo BASE_URL;?>assets/images/backgrounds/login-bg.jpg')">
                <div class="container">
                    <div class="form-box">
                        <div class="form-tab">
                            <ul class="nav nav-pills nav-fill" role="tablist">
                                <li class="nav-item active">
                                    <a class="nav-link active" id="signin-tab-2" data-toggle="tab" href="#signin-2" role="tab" aria-controls="signin-2" aria-selected="false">Register</a>
                                </li>
                               
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane show active" id="signin-2" role="tabpanel" aria-labelledby="signin-tab-2">
                                    <form action="<?php echo BASE_URL; ?>register_inter.php" method="post" >
                                        <input type="hidden" name="token" value="<?php echo $csrf_token; ?>" />
                                        <div class="col-12">
                                            <?php if($_SESSION['success_msg_fe'] != "") { ?>
                                                <div class="alert alert-success text-center mt-0 mb-4">
                                                    <?php
                                                    echo @$_SESSION['success_msg_fe'];
                                                    @$_SESSION['success_msg_fe'] = "";
                                                    ?>
                                                </div>
                                            <?php } if($_SESSION['error_msg_fe'] != "") { ?>
                                                <div class="alert alert-danger text-center mt-0 mb-4">
                                                    <?php
                                                    echo @$_SESSION['error_msg_fe'];
                                                    @$_SESSION['error_msg_fe'] = "";
                                                    ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12">
                                                <label for="first_name">First Name *</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12">
                                                <label for="last_name">Last Name</label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="singin-email-2">Email address *</label>
                                            <input type="email" class="form-control" id="singin-email-2" name="email" required>
                                        </div><!-- End .form-group -->
                                        <div class="form-group row">
                                           <div class="col-md-6">
                                                <label for="password">Password *</label>
                                                <input type="password" class="form-control" id="password" name="password" required>
                                           </div>
                                            <div class="col-md-6">
                                                <label for="confirm_password">Confirm Password *</label>
                                                <input type="text" class="form-control" id="confirm_password" name="confirm_password" required>
                                           </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="mobile">Mobile *</label>
                                            <input type="text" class="form-control" id="mobile" name="mobile" onkeypress="return isNumberKey(event)" minlength="10" maxlength="10" required>
                                        </div>
                                         <div class="form-group">
                                            <label for="address">Address *</label>
                                            <input type="text" class="form-control" id="address" name="address" required>
                                        </div>
                                         <div class="row">
                                             <div class="form-group col-md-6 col-sm-12">
                                                <label for="pincode">Pincode *</label>
                                                <input type="text" class="form-control" id="pincode" name="pincode" required>
                                            </div>
                                             <div class="form-group col-md-6 col-sm-12">
                                                <label for="city">City *</label>
                                                 <input type="text" class="form-control" id="city" name="city" required>
                                            </div>
                                         </div>
                                         <div class="row">
                                             <div class="form-group col-md-6 col-sm-12">
                                                <label for="state">State *</label>
                                                <input type="text" class="form-control" id="state" name="state" required>
                                                
                                            </div>
                                             <div class="form-group col-md-6 col-sm-12">
                                                <label for="country">Country *</label>
                                               <input type="text" class="form-control" id="country" name="country" required>
                                            </div>
                                         </div>

                                        <div class="form-footer">
                                            <button type="submit" class="btn btn-outline-primary-2">
                                                <span>REGISTER</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </button>

                                            <!-- <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="signin-remember-2">
                                                <label class="custom-control-label" for="signin-remember-2">Agr</label>
                                            </div> --><!-- End .custom-checkbox -->

                                            
                                        </div><!-- End .form-footer -->
                                    </form>
                                    <div class="form-choice">
                                        <p class="text-center">Already Have an Account?<a href="<?php echo BASE_URL . "login" . SUFFIX;?>"> Login</a></p>
                                    </div>
                                    
                                </div><!-- .End .tab-pane -->
                                
                            </div><!-- End .tab-content -->
                        </div><!-- End .form-tab -->
                    </div><!-- End .form-box -->
                </div><!-- End .container -->
            </div><!-- End .login-page section-bg -->
        </main><!-- End .main -->

        <?php include_once("inc_footer.php");?>
        <?php include_once("inc_mobile_menu.php");?>
    </div><!-- End .page-wrapper -->
    <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
    <?php include_once("inc_files_bottom.php");?>
</body>
</html>