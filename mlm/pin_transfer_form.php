<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "pinTransfer";






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
						<h1 CLASS="page-header">Pin Transfer</h1>
					</div>
				</div>

				<form name="dataform" method="post" class="form-group" action="pin_transfer_form_inter.php" enctype="multipart/form-data">

					<div class="form-rows-custom mt-3">
					<input type="hidden" name="memberAccount" value='<?php echo $memberAccount;?>'>
						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="membership_id"><strong>Membership ID *</strong></label>
							</div>
							<div class="col-sm-9">
								<input type="text" name="membership_id" id="membership_id" class="form-control"  required />
								<small id='error_msg' class='text-danger'><em></em></small>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="username">Username *</label>
							</div>
							<div class="col-sm-9">
								<input type="text" name="username" id="username" class="form-control" readonly  required/>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="mobile">Mobile No. *</label>
							</div>
							<div class="col-sm-9">
								<input type="number" name="mobile" id="mobile" class="form-control" readonly  required/>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="pin">Pins *</label>
							</div>
							<div class="col-sm-9">
							<select NAME="pinid[]" CLASS="form-control chosen" ID="pinid" multiple required>

								<?php

								$pinQueryResult = $db->view('*', 'mlm_activate_pins', 'pinid', " and status='active' and regid='$regid'");

								foreach($pinQueryResult['result'] as $pinRow)

								{

								?>

									<option VALUE="<?php echo $validation->db_field_validate($pinRow['pinid']); ?>"><?php echo $validation->db_field_validate($pinRow['pin']); ?></option>

								<?php

								}

								?>

								</select>
								
							</div>
						</div>

					
						<div class="row mt-4 mb-4">
							<div class="col-sm-12">
								<button type="submit" class="btn btn-default btn-sm mr-2 btn_submit"><i class="fa fa-arrow-circle-right"></i>&nbsp;&nbsp;Transfer</button>
								<button type="reset" class="btn btn-default btn-sm btn_delete"><i class="fas fa-sync-alt"></i>&nbsp;&nbsp;Reset</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

</body>

<script>

$('#membership_id').bind('focusout', (event) => {
  if($('#membership_id').val() != ""){
	$.ajax({
	  url: "fetch_member.php",
	  type: 'POST',
	  data: {
		  membershipid: $('#membership_id').val()
	  },
	  success: function(data){
		if(data == 0){
			$('#error_msg em').html("User doesnt exist");
			$("#username").val("");
			$('#mobile').val("");
		}else{
			$('#error_msg em').html("");
			let memberdetails = JSON.parse(data);
			$("#username").val(memberdetails.username);
			$('#mobile').val(memberdetails.mobile);
		}
	  }
  })
  }
});



</script>

</html>