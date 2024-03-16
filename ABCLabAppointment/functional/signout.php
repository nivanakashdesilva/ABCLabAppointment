<?php
// signout.php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page or any other page after signing out
header("Location: ../patient_portal");
exit();
?>
