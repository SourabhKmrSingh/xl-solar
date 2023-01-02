<!-- Plugins JS File -->
<script src="<?php echo BASE_URL;?>assets/js/jquery.min.js"></script>
<script src="<?php echo BASE_URL;?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL;?>assets/js/jquery.hoverIntent.min.js"></script>
<script src="<?php echo BASE_URL;?>assets/js/jquery.waypoints.min.js"></script>
<script src="<?php echo BASE_URL;?>assets/js/superfish.min.js"></script>
<script src="<?php echo BASE_URL;?>assets/js/owl.carousel.min.js"></script>
<script src="<?php echo BASE_URL;?>assets/js/jquery.plugin.min.js"></script>
<script src="<?php echo BASE_URL;?>assets/js/jquery.magnific-popup.min.js"></script>
<script src="<?php echo BASE_URL;?>assets/js/jquery.countdown.min.js"></script>
<!-- Main JS File -->
<script src="<?php echo BASE_URL;?>assets/js/main.js"></script>
<script src="<?php echo BASE_URL;?>assets/js/demos/demo-8.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/notify.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/nouislider.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/wNumb.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/bootstrap-input-spinner.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/jquery-ui.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/jquery.elevateZoom.min.js"></script>

<script>
	$(window).bind("load", function() {
		$.notify("<?php echo @$_SESSION['notify_success_msg_fe']; ?>", {
			className: 'success',
			autoHide: true,
			autoHideDelay: 8000
		});
		$.notify("<?php echo @$_SESSION['success_msg_fe']; ?>", {
			className: 'success',
			autoHide: true,
			autoHideDelay: 8000
		});
		$.notify("<?php echo @$_SESSION['cart_remove_msg_succ']; ?>", {
			className: 'success',
			autoHide: true,
			autoHideDelay: 8000
		});
		$.notify("<?php echo @$_SESSION['error_msg_fe']; ?>", {
			className: 'error',
			autoHide: true,
			autoHideDelay: 8000
		});		
		$.notify("<?php echo @$_SESSION['notify_error_msg_fe']; ?>", {
			className: 'error',
			autoHide: true,
			autoHideDelay: 8000
		});
		$.notify("<?php echo @$_SESSION['wishlist_remove_alert']; ?>", {
			className: 'error',
			autoHide: true,
			autoHideDelay: 8000
		});

	});

	function get_quantity(productid, variantid, slr) {
		if ($("#quantity" + slr).val() == "10+") {
			$("#quantity" + slr).hide();
			$("#quantity_custom" + slr).show();
			$("#quantity_custom_btn" + slr).show();
		} else if ($("#quantity" + slr).val() > 0) {
			location.replace("<?php echo BASE_URL; ?>cart_inter.php?token=<?php echo $csrf_token; ?>&id=" + productid + "&id2=" + variantid + "&qty=" + $("#quantity" + slr).val());
		}
	}

	function get_quantity_product() {
		if ($("#quantity").val() == "10+") {
			$("#quantity").hide();
			$("#quantity_custom").show();
			$("#quantity_custom_btn").show();
		}
	}

	function get_quantitycustom(productid, variantid, slr) {
		location.replace("<?php echo BASE_URL; ?>cart_inter.php?token=<?php echo $csrf_token; ?>&id=" + productid + "&id2=" + variantid + "&qty=" + $("#quantity_custom" + slr).val());
	}
</script>
<?php
@$_SESSION['notify_success_msg_fe'] = "";
@$_SESSION['notify_error_msg_fe'] = "";
@$_SESSION['wishlist_remove_alert'] = "";
@$_SESSION['success_msg_fe'] = "";
@$_SESSION['cart_remove_msg_succ'] = "";
@$_SESSION['error_msg_fe'] = "";
?>