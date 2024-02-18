<?php
session_start();
include('../../functional/db.php');

// Check if the technician is logged in
if (!isset($_SESSION['technician_username'])) {
    header('Location: ../lablogin.php'); // Redirect to technician login page
    exit();
}

// Fetch technician's access level from labaccount table
$technician_username = $_SESSION['technician_username'];
$query = "SELECT access_level FROM labaccount WHERE username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $technician_username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $access_level);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Check if the technician has appropriate access level
if ($access_level != 'admin') {
    header('Location: ../lablogin.php'); // Redirect to unauthorized access page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
</head>
<body>
    <h1>Welcome to Admin Home</h1>
    <p>Choose an action:</p>
    <ul>
        <li><a href="admin_registration.php">Admin User Registration</a></li>
        <li><a href="record_deletion.php">Record Deletion</a></li>
    </ul>
</body>
</html>
