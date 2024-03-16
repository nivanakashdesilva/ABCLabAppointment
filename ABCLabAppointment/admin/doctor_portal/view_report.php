<?php
session_start();
include('../../functional/db.php');

// Check if the user is authenticated
if (!isset($_SESSION['technician_username'])) {
    header('Location: index.php'); // Redirect to the login page
    exit();
}

// Fetch doctor's username
$doctor_username = $_SESSION['technician_username'];

// Query to retrieve upcoming appointments for the doctor including the status
$query = "SELECT ca.confirmed_appointment_id, p.fullname AS patient_name, ca.appointment_date, ca.appointment_time, ca.status
          FROM confirmed_appointments ca
          INNER JOIN patients p ON ca.patient_id = p.patient_id
          WHERE ca.doctor_id = (
              SELECT doctor_id FROM doctors WHERE username = ?
          ) AND ca.status = 'pending'";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $doctor_username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if the appointment_id is provided in the URL
if (!isset($_GET['appointment_id'])) {
    // Redirect or handle the case where appointment_id is not provided
    exit("Appointment ID is missing.");
}

$appointment_id = $_GET['appointment_id'];

// Query to fetch the lab report details including the name of the account
$query = "SELECT ptr.*, la.fullname AS account_name 
          FROM patienttestresults ptr 
          INNER JOIN labaccount la ON ptr.labaccount_id = la.id 
          WHERE ptr.confirmed_appointment_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $appointment_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    // Handle case where no lab report with provided appointment ID is found
    exit("Lab report not found.");
}

$row = mysqli_fetch_assoc($result);

// Close statement and connection
mysqli_stmt_close($stmt);
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Lab Report</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('main/nav.php'); ?>

    <div class="container mt-5">
        <h2>Lab Report</h2>
        <table class="table">
            <tr>
                <th>Attribute</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>ID</td>
                <td><?php echo $row['id']; ?></td>
            </tr>
            <tr>
                <td>Confirmed Appointment ID</td>
                <td><?php echo $row['confirmed_appointment_id']; ?></td>
            </tr>
            <tr>
                <td>Patient ID</td>
                <td><?php echo $row['patient_id']; ?></td>
            </tr>
            <tr>
                <td>Lab Account</td>
                <td><?php echo $row['account_name']; ?></td> <!-- Display account name instead of ID -->
            </tr>
            <tr>
                <td>Test Name</td>
                <td><?php echo $row['TestName']; ?></td> <!-- Updated field name -->
            </tr>
            <tr>
                <td>Test Results</td>
                <td><?php echo $row['TestResults']; ?></td> <!-- Updated field name -->
            </tr>
            <!-- For the Lab Report File, you can't directly display it in a table cell -->
            <!-- You might want to provide a link to download it -->
        </table>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
