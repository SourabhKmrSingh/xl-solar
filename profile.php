<?php
include_once("inc_config.php");

if($_SESSION['regid'] == "")
{
	$_SESSION['error_msg_fe'] = "Login to continue!";
	header("Location: {$base_url}login{$suffix}?url={$full_url}");
	exit();
}

$pageid = "update-your-profile";
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if($pageResult['num_rows'] == 0)
{
	header("Location: {$base_url}error{$suffix}");
	exit();
}
$pageRow = $pageResult['result'][0];

$registerResult = $db->view("*", "rb_registrations", "regid", "and regid='{$regid}'");
$registerRow = $registerResult['result'][0];

$_SESSION['csrf_token'] = substr(sha1(rand(1, 99999)),0,32);
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
        <div class="register-login-section p-5 bg-shadow spad">
			<div class="container">
				<div class="row">
					<div class="col-lg-8 offset-lg-2">
						<div class="login-form">
							
							
							<?php if($pageRow['description'] != "") { ?>
								<div class="row mb-4">
									<div class="col-12">
										<?php echo $validation->db_field_validate($pageRow['description']); ?>
									</div>
								</div>
							<?php } ?>
							
							<form action="<?php echo BASE_URL; ?>profile_inter.php" method="post" class='form-box'>
								<input type="hidden" name="token" value="<?php echo $csrf_token; ?>" />
								<input type="hidden" name="user_ip" value="<?php echo $validation->db_field_validate($registerRow['user_ip']); ?>" />
								
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
									<div class="col-md-12 group-input text-center">
										<?php
											if($registerRow['membership_id']!="")
											{?>
													Referral Link: <a href="<?php echo BASE_URL.'register'.SUFFIX.'?id='.$validation->db_field_validate($registerRow['membership_id']); ?>" target="_blank" class="anchor-tag"><?php echo BASE_URL.'register'.SUFFIX.'?id='.$validation->db_field_validate($registerRow['membership_id']); ?></a>
										<?php	}

										?>
										
									</div>
									
									<div class="form-group col-md-6 col-sm-12">
										<label for="first_name">First Name</label>
										<input name="first_name" class="form-control" id="first_name" type="text" value="<?php echo $validation->db_field_validate($registerRow['first_name']); ?>" required />
									</div>
									<div class="col-md-6 col-sm-12 form-group">
										<label for="last_name">Last Name</label>
										<input name="last_name" id="last_name" class='form-control' type="text" value="<?php echo $validation->db_field_validate($registerRow['last_name']); ?>" />
									</div>
									<div class="col-md-12 group-input">
										<label for="email">Email-ID</label>
										<input name="email" class='form-control' id="email" type="email" value="<?php echo $validation->db_field_validate($registerRow['email']); ?>" disabled />
									</div>
									<div class="col-md-12 group-input">
										<label for="mobile">Mobile No.</label>
										<input name="mobile" class='form-control' id="mobile" type="text" value="<?php echo $validation->db_field_validate($registerRow['mobile']); ?>" readonly required />
									</div>
									<div class="col-md-12 group-input">
										<label for="mobile_alter">Mobile No. (Alternative)</label>
										<input name="mobile_alter" class='form-control' id="mobile_alter" type="text" value="<?php echo $validation->db_field_validate($registerRow['mobile_alter']); ?>" />
									</div>
									<div class="col-md-12 group-input">
										<label for="gender">Gender</label>
										<select class="form-control w-100" name="gender" id="gender" required >
											<option value="" <?php if($registerRow['gender'] == "") echo "selected"; ?>>Select your Gender</option>
											<option value="male" <?php if($registerRow['gender'] == "male") echo "selected"; ?>>Male</option>
											<option value="female" <?php if($registerRow['gender'] == "female") echo "selected"; ?>>Female</option>
										</select>
									</div>
									<div class="col-md-12 group-input">
										<label for="date_of_birth">Date of Birth</label>
										<input name="date_of_birth" class='form-control' id="date_of_birth" type="date" value="<?php echo $validation->db_field_validate($registerRow['date_of_birth']); ?>" required />
									</div>
									<div class="col-md-12 group-input">
										<label for="address">Address</label>
										<textarea class="form-control" class='form-control' name="address" id="address" required ><?php echo $validation->db_field_validate($registerRow['address']); ?></textarea>
									</div>
									<div class="col-md-12 group-input">
										<label for="landmark">Landmark</label>
										<input name="landmark" class='form-control' id="landmark" type="text" value="<?php echo $validation->db_field_validate($registerRow['landmark']); ?>" />
									</div>
									<div class="col-md-12 group-input">
										<label for="pincode">Pincode</label>
										<input name="pincode" class='form-control' id="pincode" type="text" value="<?php echo $validation->db_field_validate($registerRow['pincode']); ?>" onBlur="fetch_pincode();" required />
										<span class="pincode_success" style="color:green;display:none;"><i class="fa fa-check"></i> Verified!</span>
										<span class="pincode_failure" style="color:red;display:none;"><i class="fa fa-times"></i> Please enter a valid Pincode!</span>
									</div>
									<div class="col-md-12 group-input">
										<label for="city">City</label>
										<input name="city" class='form-control' id="city" type="text" value="<?php echo $validation->db_field_validate($registerRow['city']); ?>" required />
									</div>
									<div class="col-md-12 group-input">
										<label for="state">State</label>
										<input name="state" class='form-control' id="state" type="text" value="<?php echo $validation->db_field_validate($registerRow['state']); ?>" required />
									</div>
									<div class="col-md-12 group-input">
										<label for="country">Country</label>
										<input name="country" class='form-control' id="country" type="text" value="<?php echo $validation->db_field_validate($registerRow['country']); ?>" required />
									</div>

									<div class="col-md-6 group-input">
										<label for="membership_id">Your Referral ID</label>
										<input name="membership_id" class='form-control' id="membership_id" type="text" value="<?php echo $validation->db_field_validate($registerRow['membership_id']); ?>" class="mb-0 bg-light" onBlur="fetch_member('membership_id');" />
										<p style="display:none;" class="mt-1 mb-0 error_cls text-left"><font color="red">Please enter a valid Membership ID</font></p>
										<p style="display:none;" class="mt-1 mb-0 error_cls2 text-left"><font color="red">Please enter your own membership ID</font></p>
										<div style="color:green;" class="mt-1 mb-0 success_cls text-left"></div>
										<p>Enter your membership ID (e.g. HLL….) here. If you have not filled the membership form yet, <a href="<?php echo BASE_URL.'mlm/register.php'; ?>" target="_blank" class="anchor-tag">click here</a></p>
									</div>
									<div class="col-md-6 group-input">
										<label for="sponsor_id">Referral ID</label>
										<input name="sponsor_id" class='form-control' id="sponsor_id" type="text" value="<?php echo $validation->db_field_validate($registerRow['sponsor_id']); ?>" class="mb-0 bg-light" readonly />
									</div>
									
								</div>
								
								<div class="form-footer">
									<button type="submit" class="btn btn-primary w-100 mt-2 mb-2"><span>UPDATE</span></button>
								</div>
								<div class="form-choice">
                                    <p class="text-center">Secure your account?<a href="<?php echo BASE_URL."change-password".SUFFIX; ?>"> Change Password</a></p>
                                </div>
								
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

    </main>
    <?php include_once("inc_footer.php");?>
    </div><!-- End .page-wrapper -->
    <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
    <!-- Plugins JS File -->
    <?php include_once("inc_files_bottom.php");?>
<script>
function fetch_member(field)
{
	$.ajax({
		type: 'post',
		url: '<?php echo BASE_URL; ?>fetch_member.php',
		data:
		{
			membership_id: $("#"+field).val(),
			mobile: <?php echo $_SESSION['mobile']; ?>
		},
		success: function (response)
		{
			if(response.substr(0, 8) == "usernono")
			{
				$(".error_cls").show();
				$("#sponsor_id").val("");
				$("#membership_id").val("");
				$(".success_cls").html('');
			}
			else if(response.substr(0, 6) == "userno")
			{
				$(".error_cls2").show();
				$("#sponsor_id").val("");
				$("#membership_id").val("");
				$(".success_cls").html('');
			}
			else if(response != "")
			{
				$(".success_cls").html('Verified!');
				$("#sponsor_id").val(response);
				$(".error_cls").hide();
				$(".error_cls2").hide();
			}
			else
			{
				$("#sponsor_id").val(response);
				$(".error_cls").hide();
				$(".error_cls2").hide();
				$(".success_cls").html('');
			}
		}
	});
}

function fetch_pincode()
{
	$.ajax({
		type: 'post',
		url: '<?php echo BASE_URL; ?>fetch_pincode.php',
		data:
		{
			pincode: $("#pincode").val()
		},
		success: function (response)
		{
			if(response == "no")
			{
				$(".pincode_failure").show();
				$(".pincode_success").hide();
				$("#btnSubmit").prop('disabled', true);
			}
			else
			{
				var result = $.parseJSON(response);
				$("#city").val(result[0]);
				$("#state").val(result[1]);
				$("#country").val(result[2]);
				$(".pincode_success").show();
				$(".pincode_failure").hide();
				$("#btnSubmit").prop('disabled', false);
			}
		}
	});
}
</script>
</body>
</html>