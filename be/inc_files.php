<meta charset="utf-8" />
<meta HTTP-EQUIV="X-UA-Compatible" CONTENT="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="robots" content="noindex" />

<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="assets/font-awesome-v5/css/all.min.css" />
<script type="text/javascript" src="assets/font-awesome-v5/js/all.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<!--<script type="text/javascript" src="assets/bootstrap/js/bootstrap.min.js"></script>-->
<script type="text/javascript" src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="assets/js/notify.min.js"></script>
<link rel="stylesheet" href="assets/css/style.css" />
<link REL="STYLESHEET" HREF="assets/css/editor.css" />
<script SRC="assets/tinymce/tinymce.min.js"></script>
<script TYPE="TEXT/JAVASCRIPT" SRC="assets/js/editor.js"></script>
<script TYPE="TEXT/JAVASCRIPT" SRC="assets/js/custom.js"></script>

<script>
$(window).bind("load", function(){
	$.notify("<?php echo @$_SESSION['success_msg']; ?>", { className: 'success', autoHide: true, autoHideDelay: 8000 });
	$.notify("<?php echo @$_SESSION['error_msg']; ?>", { className: 'error', autoHide: true, autoHideDelay: 8000 });
});
</script>
<?php
@$_SESSION['success_msg'] = "";
@$_SESSION['error_msg'] = "";
?>
