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
}?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        /* Add custom styles here */
        body {
            padding-top: 60px;
        }
    </style>
</head>

<body>
<?php include('main_admin/nav.php'); ?>

    <!-- Page Content -->
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <h1 class="text-center mb-4">Welcome to Admin Dashboard</h1>
                <p class="text-center">Choose an action:</p>
                <ul class="list-group text-center">
                    <li class="list-group-item"><a href="admin_registration.php">User Registration</a></li>
                    <li class="list-group-item"><a href="doc_registration.php">Doctor Registration</a></li>
                    <li class="list-group-item"><a href="data.php">View DataBase Tables</a></li>
                    <li class="list-group-item"><a href="database-query.php">SQL DataBase Runner</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>