<?php
include_once("inc_config.php");

@$q = strtolower($validation->urlstring_validate($_GET['q']));

if ($q == "collaborate") {
    $pageid = "want-to-collaborate";
} else {
    $pageid = "contact";
}
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if ($pageResult['num_rows'] == 0) {
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageRow = $pageResult['result'][0];

$_SESSION['csrf_token'] = substr(sha1(rand(1, 99999)), 0, 32);
$csrf_token = $_SESSION['csrf_token'];
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
                <div class="container">
                	<div class="row mt-5">
                		<div class="col-md-4">
                			<div class="contact-box text-center">
        						<h3>Office</h3>

        						<address><?php echo $validation->db_field_validate($configRow['address']);?></address>
        					</div><!-- End .contact-box -->
                		</div><!-- End .col-md-4 -->

                		<div class="col-md-4">
                			<div class="contact-box text-center">
        						<h3>Start a Conversation</h3>

        						<div><a href="mailto:<?php echo $validation->db_field_validate($configRow['email']);?>"><?php echo $validation->db_field_validate($configRow['email']);?></a></div>
        						<div><a href="tel:<?php echo $validation->db_field_validate($configRow['contact_number']);?>"><?php echo $validation->db_field_validate($configRow['contact_number']);?></a></div>
        					</div><!-- End .contact-box -->
                		</div><!-- End .col-md-4 -->

                		<div class="col-md-4">
                			<div class="contact-box text-center">
        						<h3>Social</h3>

        						<div class="social-icons social-icons-color justify-content-center">
			    					<?php if($configRow['facebook']){?>
                                        <a href="<?php echo $validation->db_field_validate($configRow['facebook']);?>" class="social-icon social-facebook" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                                    <?php }if($configRow['twitter']){?>
                                        <a href="<?php echo $validation->db_field_validate($configRow['twitter']);?>" class="social-icon social-twitter" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                                    <?php }if($configRow['instagram']){?>
                                        <a href="<?php echo $validation->db_field_validate($configRow['instagram']);?>" class="social-icon social-instagram" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                                    <?php }if($configRow['youtube']){?>
                                        <a href="<?php echo $validation->db_field_validate($configRow['youtube']);?>" class="social-icon social-youtube" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
                                    <?php }if($configRow['linkedin']){?>
                                        <a href="<?php echo $validation->db_field_validate($configRow['linkedin']);?>" class="social-icon social-linkedin" title="linkedin" target="_blank"><i class="icon-linkedin text-info"></i></a>
                                    <?php }if($configRow['whatsapp']){?>
                                        <a href="<?php echo $validation->db_field_validate($configRow['whatsapp']);?>" class="social-icon social-whatsapp" title="whatsapp" target="_blank"><i class="icon-whatsapp text-success"></i></a>
                                    <?php }?>
			    				</div><!-- End .soial-icons -->
        					</div><!-- End .contact-box -->
                		</div><!-- End .col-md-4 -->
                	</div><!-- End .row -->

                	<hr class="mt-3 mb-5 mt-md-1">
                	<div class="touch-container row justify-content-center">
                        <div class="col-md-12 d-flex justify-content-center align-items-center border-bottom-1 certificate-container">
                            <img src="<?php echo BASE_URL;?>uploads/logo-registration.jpg" alt="" width="80">
                           <div class="d-flex justify-content-center align-items-center certificate">
                                <a href="<?php echo BASE_URL;?>uploads/CERTIFICATEOFINCORPORATION.PDF" class='ml-4'> CERTIFICATE OF INCORPORATION</a>
                                <a href="<?php echo BASE_URL;?>uploads/certificate-of-registration.pdf" class='ml-4'> CERTIFICATE OF REGISTRATION</a>
                                <a href="<?php echo BASE_URL;?>uploads/pancard.pdf" class='ml-4'> PANCARD</a>
                                <a href="<?php echo BASE_URL;?>uploads/pannumber.pdf" class='ml-4'> PAN NUMBER</a>
                           </div>
                        </div>
                		<div class="col-md-9 col-lg-7">
                			<div class="text-center">
                			<h2 class="title mb-1">Get In Touch</h2><!-- End .title mb-2 -->
                			<!-- <p class="lead text-primary">
                				We collaborate with ambitious brands and people; we’d love to build something great together.
                			</p> --><!-- End .lead text-primary -->
                			<!-- <p class="mb-3">Vestibulum volutpat, lacus a ultrices sagittis, mi neque euismod dui, eu pulvinar nunc sapien ornare nisl. Phasellus pede arcu, dapibus eu, fermentum et, dapibus sed, urna.</p>
                			</div> --><!-- End .text-center -->

                			<form  action="<?php echo BASE_URL; ?>contact_inter.php" method="post" class="contact-form mb-2 mt-4">
                                <input type="hidden" name='token' value="<?php echo $csrf_token;?>">
                				<?php if ($_SESSION['success_msg_fe'] != "" || $_SESSION['error_msg_fe'] != "") { ?>
                                    <div class="text-center w-100 mb-3 font-weight-normal">
                                        <font color="green">
                                            <?php
                                            echo @$_SESSION['success_msg_fe'];
                                            @$_SESSION['success_msg_fe'] = "";
                                            ?>
                                        </font>
                                        <font color="red">
                                            <?php
                                            echo @$_SESSION['error_msg_fe'];
                                            @$_SESSION['error_msg_fe'] = "";
                                            ?>
                                        </font>
                                        <br /><br />
                                    </div>
                                <?php } ?>
                                <div class="row">
                					<div class="col-md-6 col-sm-12">
                                        <label for="first_name" class="sr-only">First Name</label>
                						<input type="text" class="form-control" id="first_name" placeholder="First Name *" required name='first_name'>
                					</div><!-- End .col-sm-4 -->
                                    <div class="col-md-6 col-sm-12">
                                        <label for="last_name" class="sr-only">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" placeholder="Last Name *" required name='last_name'>
                                    </div>

                					<!-- End .col-sm-4 -->
                				</div><!-- End .row -->
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <label for="email" class="sr-only">Email</label>
                                        <input type="email" class="form-control" name='email' id="email" placeholder="Email">
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <label for="mobile" class="sr-only">Mobile</label>
                                        <input type="text" class="form-control" onkeypress="return isNumberKey(event)" minlength="10" maxlength="10" id="mobile" placeholder="Mobile" name='mobile'>
                                    </div>
                                </div>

                                <label for="subject" class="sr-only">Subject</label>
        						<input type="text" class="form-control" id="subject" placeholder="Subject" name='subject'>


                                <label for="message" class="sr-only">Message</label>
                				<textarea class="form-control" cols="30" rows="4" name='message' id="message" required placeholder="Message *"></textarea>
								
								<div class="text-center">
	                				<button type="submit" class="btn btn-primary btn-minwidth-sm">
	                					<span>SUBMIT</span>
	            						<i class="icon-long-arrow-right"></i>
	                				</button>
                				</div><!-- End .text-center -->
                			</form><!-- End .contact-form -->
                		</div><!-- End .col-md-9 col-lg-7 -->
                	</div><!-- End .row -->
                </div><!-- End .container -->
            </div><!-- End .page-content -->
          </main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
</body>
</html>