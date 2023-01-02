<?php
include_once("inc_config.php");

if($_SESSION['regid'] != "")
{
    header("Location: {$base_url}home{$suffix}");
    exit();
}

$pageid = "login";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if($pageResult['num_rows'] == 0)
{
    header("Location: {$base_url}error{$suffix}");
    exit();
}   
$pageRow = $pageResult['result'][0];

$redirect_url = $validation->urlstring_validate($_GET['url']);
$q = $validation->urlstring_validate($_GET['q']);

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
							        <a class="nav-link active" id="signin-tab-2" data-toggle="tab" href="#signin-2" role="tab" aria-controls="signin-2" aria-selected="false">Login</a>
							    </li>
							   
							</ul>
							<div class="tab-content">
							    <div class="tab-pane show active" id="signin-2" role="tabpanel" aria-labelledby="signin-tab-2">
							    	<form action="<?php echo BASE_URL; ?>login_check.php" method="post" >
							    		<input type="hidden" name="token" value="<?php echo $csrf_token; ?>" />
				                        <input type="hidden" name="redirect_url" value="<?php echo $redirect_url; ?>" />
				                        <input type="hidden" name="q" value="<?php echo $q; ?>" />
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
							    		<div class="form-group">
							    			<label for="membership_id">Membership ID. *</label>
							    			<input class="form-control" type="text" name="membership_id" id="membership_id" required>
							    		</div><!-- End .form-group -->

							    		<div class="form-group">
							    			<label for="password">Password *</label>
							    			<input type="password" class="form-control" id="password" name="password" required>
							    		</div><!-- End .form-group -->

							    		<div class="form-footer">
							    			<button type="submit" class="btn btn-outline-primary-2">
			                					<span>LOG IN</span>
			            						<i class="icon-long-arrow-right"></i>
			                				</button>

			                				<!-- <div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" id="signin-remember-2">
												<label class="custom-control-label" for="signin-remember-2">Agr</label>
											</div> --><!-- End .custom-checkbox -->

											<a href="<?php echo BASE_URL . "forgot-password" . SUFFIX;?>" class="forgot-link">Forgot Your Password?</a>
							    		</div><!-- End .form-footer -->
							    	</form>
                                    <div class="form-choice">
                                        <p class="text-center">Don't Have an Account?<a href="<?php echo BASE_URL. 'mlm/register.php';?>"> Register</a></p>
                                    </div>
							    	
							    </div><!-- .End .tab-pane -->
							    
							</div><!-- End .tab-content -->
						</div><!-- End .form-tab -->
            		</div><!-- End .form-box -->
            	</div><!-- End .container -->
            </div><!-- End .login-page section-bg -->
        </main><!-- End .main -->

        <?php include_once("inc_footer.php");?>
    </div><!-- End .page-wrapper -->
    <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
    <!-- Plugins JS File -->
    <?php include_once("inc_files_bottom.php");?>
</body>

</html>