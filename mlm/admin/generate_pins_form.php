<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "generate_pins";

if (isset($_GET['mode'])) {
	$mode = $validation->urlstring_validate($_GET['mode']);
} else {
	$_SESSION['error_msg'] = "There is a problem. Please Try Again!";
	header("Location: generate_pins_view.php");
	exit();
}


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
						<h1 CLASS="page-header">Generate New Pins</h1>
					</div>
				</div>

				<form name="dataform" method="post" class="form-group" action="<?php
																				switch ($mode) {
																					case "insert":
																						echo "generate_pins_form_inter.php?mode=$mode";
																						break;
																					default:
																						echo "generate_pins_form_inter.php";
																				}
																				?>" enctype="multipart/form-data">
					<div class="row mb-3">
							<div class="col-sm-3">
								<label for="planid">Plan *</label>
							</div>
							<div class="col-sm-9">
								<select NAME="planid" CLASS="form-control" ID="planid" required>
									<option VALUE="">--select--</option>
									<?php
									$planQueryResult = $db->view('planid,title', 'mlm_plans', 'planid', "and status='active'", 'title asc');
									foreach ($planQueryResult['result'] as $planRow) {
									?>
										<option VALUE="<?php echo $validation->db_field_validate($planRow['planid']); ?>" <?php if ($mode == 'edit') { if ($planRow['planid'] == $franchiseRow['planid']) echo "selected";} ?>><?php echo $validation->db_field_validate($planRow['title']); ?></option>
									<?php
									}
									?>
								</select>
							</div>
						</div>

					<div class="form-rows-custom mt-3">
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
								<label for="pin_total">Total Pins *</label>
							</div>
							<div class="col-sm-9">
								<input type="number" name="pin_total" id="pin_total" class="form-control" value="" required />
							</div>
						</div>
<!-- 
						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="earnings">Earnings</label>
							</div>
							<div class="col-sm-9">
								<input type="number" name="earnings" id="earnings" class="form-control" value="<?php if ($mode == 'edit') echo $validation->db_field_validate($franchiseRow['earnings']); ?>" />
							</div>
						</div> -->

						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="free">Free <em>(Optional)</em></label>
							</div>
							<div class="col-sm-9">
								<input type="number" name="free" id="free" class="form-control" value="" />
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="tax">Tax</label>
							</div>
							<div class="col-sm-9">
								<input type="number" name="tax" id="tax" class="form-control" value='5'/>
								<input type="hidden" name='tax_amount' id='tax_amount'>
								<small><em>In percentage</em></small>
							</div>
						</div>
						
						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="subtotal">Subtotal</label>
							</div>
							<div class="col-sm-9">
								<input type="hidden" name='subtotal' id='subtotal' value="">
								<p class='subtotal'>&#8377; 0</p>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="tax_amount">Tax Amount</label>
							</div>
							<div class="col-sm-9">
								<p class='tax_amount'>+ &#8377; 0</p>
							</div>
						</div>
						
						
						<div class="row mb-3">
							<div class="col-sm-3">
								<label for="total_amount">Total Amount</label>
							</div>
							<div class="col-sm-9">
								<input type="hidden" name='total_amount' id='total_amount' value="">
								<p class='total_amount'>&#8377; 0</p>
							</div>
						</div>

					
						<div class="row mt-4 mb-4">
							<div class="col-sm-12">
								<?php
								if ($mode == "insert") {
								?>
									<button type="submit" class="btn btn-default btn-sm mr-2 btn_submit" disabled><i class="fa fa-arrow-circle-right" ></i>&nbsp;&nbsp;Generate</button>
									<button type="reset" class="btn btn-default btn-sm btn_delete"><i class="fas fa-sync-alt"></i>&nbsp;&nbsp;Reset</button>
								<?php
								} 
								?>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div ID="image_model" CLASS="modal">
		<div CLASS="modal-content">
			<div class="row">
				<div class="col-10">
					<div class="image_modal_heading"><i class="fa fa-image" aria-hidden="true"></i> Upload Image</div>
				</div>
				<div class="col-2">
					<div CLASS="image_close_button">&times;</div>
				</div>
			</div>
			<div STYLE="background:; padding:3%;">
				<p align="center">Select/Upload files from your local machine to server.</p>
				<div ID="drop-area">
					<p CLASS="drop-text" STYLE="margin-top:50px;">
						<p class="image_upper_text" id="image_upper_text"><i class="fas fa-check" aria-hidden="true" style="color: #0BC414;"></i> Your Image has been Uploaded. Upload more pictures!!!</p>
						<img src="images/Loading_icon.gif" class="image_model_loader" style="display:none;" />
						<p class="image_lower_text">
							<form name="uploadForm" id="uploadForm">
								<input type="file" name="userImage" class="d-none" onChange="uploadimage(this);" id="userImage">
								<label for="userImage" class="file_design"><i class="fa fa-image" aria-hidden="true"></i> Select File</label>&nbsp; or Drag it Here
							</form>
						</p>
					</p>
				</div>
				<br>
				<button TYPE="BUTTON" ID="image_close" CLASS="btn btn-success btn-sm">Done</button>
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
			$('#error_msg em').html("No Record Found!");
			$("#username").val("");
			$("#membership_id").val("");
		}else{
			$("#username").val(data);
			$('#error_msg em').html("");
		}
	  }
  })
  }
});

$('#tax').bind('focusout', (event) => {
	setTotalPrice();
	$(".btn_submit").prop('disabled', false);;
});
$('#pin_total').bind('focusout', (event) => {
	setTotalPrice();
	$(".btn_submit").prop('disabled', false);;
});

function setTotalPrice(){
	if($('#tax').val() != "" && $('#pin_total').val() != ""){
	$.ajax({
	  url: "fetch_price.php",
	  type: 'POST',
	  data: {
		  planid: $('#planid').val()
	  },
	  success: function(data){

		const amount = data;
		const totalPins = $("#pin_total").val();
		const tax = $('#tax').val();
		const subtotal = amount * totalPins;
		const taxAmount =  subtotal * tax / 100; 
		const total_amount = subtotal + taxAmount;
		
		$("#subtotal").val(subtotal);
		$(".subtotal").html("&#8377; "+subtotal)
		$('#total_amount').val(total_amount);
		$('.total_amount').html("&#8377; "+total_amount);
		$('#tax_amount').val(taxAmount);
		$('.tax_amount').html("+ &#8377; "+taxAmount);
	  }
  })
  }
}

</script>

</html>