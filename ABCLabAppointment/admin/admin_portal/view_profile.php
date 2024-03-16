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
$query = "SELECT * FROM labaccount WHERE username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $technician_username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Check if the technician has appropriate access level
if (!$row) {
    header('Location: ../lablogin.php'); // Redirect to unauthorized access page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Common CSS Styles -->
    <link rel="stylesheet" href="../../css/styles.css">
  
</head>
<body>
<?php include('main_admin/nav.php'); ?>
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">View Profile</h2>
            <div class="container">
                <div class="profile-container">
                    <div class="profile-info">
                        <label>Name:</label>
                        <p><?php echo $row['fullname']; ?></p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="profile-container">
                    <div class="profile-info">
                        <label>Username:</label>
                        <p><?php echo $row['username']; ?></p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="profile-container">
                    <div class="profile-info">
                        <label>Access Level:</label>
                        <p><?php echo $row['access_level']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JavaScript (optional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
