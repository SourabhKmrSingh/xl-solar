<?php
include_once("inc_config.php");
session_start();
ob_start();

unset($_SESSION['mlm_regid']);
session_destroy();

session_start();
//$_SESSION['success_msg'] = "You have logged out!";
header("Location: {$base_url_web}");
exit();
?>