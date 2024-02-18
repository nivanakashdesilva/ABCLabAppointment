<?php
session_start();
include('../functional/db.php');

// Check if the appointment_id is provided in the URL
if (!isset($_GET['appointment_id'])) {
    // Redirect or handle the case where appointment_id is not provided
    exit("Appointment ID is missing.");
}

$appointment_id = $_GET['appointment_id'];

// Query to fetch the lab report details including the name of the account
$query = "SELECT lr.*, la.fullname AS account_name 
          FROM lab_reports lr 
          INNER JOIN labaccount la ON lr.labaccount_id = la.id 
          WHERE lr.confirmed_appointment_id = ?";
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
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Lab Report</h2>
    <table>
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
            <td><?php echo $row['test_name']; ?></td>
        </tr>
        <tr>
            <td>Test Result</td>
            <td><?php echo $row['test_result']; ?></td>
        </tr>
        <tr>
            <td>Lab Report File</td>
            <td><?php echo $row['lab_report_file']; ?></td>
        </tr>
    </table>
</body>
</html>
