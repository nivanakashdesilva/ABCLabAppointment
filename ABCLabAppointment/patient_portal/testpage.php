<?php
session_start();
include('../functional/db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

// Query to retrieve patient's test results with appointment ID and technician name
$query = "SELECT ptr.id, ptr.TestName, ptr.TestResults, ptr.confirmed_appointment_id, la.fullname AS TechnicianName, ptr.PDFReport
          FROM patienttestresults ptr
          INNER JOIN patients p ON ptr.patient_id = p.patient_id
          LEFT JOIN labaccount la ON ptr.labaccount_id = la.id
          WHERE p.username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch test results data
$testResults = [];
while ($row = mysqli_fetch_assoc($result)) {
    $testResults[] = $row;
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Test Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <?php include('main/nav.php'); ?> <!-- Include the navigation bar here -->
    <div class="mt-5">
        <h2 class="mb-4">View Test Results</h2>
        <?php if (!empty($testResults)) : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Test Results</th>
                    <th>Appointment ID</th>
                    <th>Technician Name</th>
                    <th>PDF Report</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($testResults as $testResult) : ?>
                <tr>
                    <td><?php echo $testResult['TestName']; ?></td>
                    <td><?php echo $testResult['TestResults']; ?></td>
                    <td><?php echo $testResult['confirmed_appointment_id']; ?></td>
                    <td><?php echo $testResult['TechnicianName']; ?></td>
                    <td>
                        <?php if (!empty($testResult['PDFReport'])) : ?>
                            <a href="download_report.php?ConfirmedAppointmentID=<?php echo $testResult['confirmed_appointment_id']; ?>">Download Report</a>
                        <?php else : ?>
                            No PDF Report
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
        <p>No test results available.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>