<?php
session_start();

// Clear session data
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear the token cookie
setcookie("token", "", time() - 3600, "/", "", true, true);

// Redirect to the login page
header("Location: index.php");
exit;
?>