<?php
session_start();
ob_start();

unset($_SESSION['be_userid']);
session_destroy();

session_start();
$_SESSION['success_msg'] = "You have logged out!";
header("Location: index.php");
exit();
?>