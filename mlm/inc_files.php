<meta charset="utf-8" />
<meta HTTP-EQUIV="X-UA-Compatible" CONTENT="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

<link rel="icon" href="content/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="admin/assets/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="admin/assets/font-awesome-v5/css/all.min.css" />
<script type="text/javascript" src="admin/assets/font-awesome-v5/js/all.min.js"></script>
<script type="text/javascript" src="admin/assets/js/jquery.min.js"></script>
<!--<script type="text/javascript" src="admin/assets/bootstrap/js/bootstrap.min.js"></script>-->
<script type="text/javascript" src="admin/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="admin/assets/js/notify.min.js"></script>
<link rel="stylesheet" href="admin/assets/css/style.css" />
<link REL="STYLESHEET" HREF="admin/assets/css/editor.css" />
<script SRC="admin/assets/tinymce/tinymce.min.js"></script>
<script TYPE="TEXT/JAVASCRIPT" SRC="admin/assets/js/editor.js"></script>
<script TYPE="TEXT/JAVASCRIPT" SRC="admin/assets/js/custom.js"></script>
<link REL="STYLESHEET" HREF="admin/assets/css/chosen.css" />
<link REL="STYLESHEET" HREF="admin/assets/css/card.css" />
<script TYPE="TEXT/JAVASCRIPT" SRC="admin/assets/js/chosen.js"></script>

<script>

$(document).ready(function(){

	$(".chosen").chosen({

		disable_search_threshold: 10,

		no_results_text: "No record found!"

	});

});

</script>

<script>


$(window).bind("load", function(){
	$.notify("<?php echo @$_SESSION['success_msg']; ?>", { className: 'success', autoHide: true, autoHideDelay: 8000 });

	<?php if($_SESSION['error_msg'] != "Please login first!")
	{
	?>
	$.notify("<?php echo @$_SESSION['error_msg']; ?>", { className: 'error', autoHide: true, autoHideDelay: 8000 });

	<?php }?>
});
</script>
<?php

@$_SESSION['success_msg'] = "";
@$_SESSION['error_msg'] = "";

?>
