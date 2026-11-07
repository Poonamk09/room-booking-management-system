<?php
session_start();

// Clear admin session
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);

// Destroy session
session_destroy();

// Redirect to Admin Login
header("Location: login.php");
exit();
?>
