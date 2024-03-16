<?php
session_start();
include('../functional/db.php');

// Check if the appointment_id is provided in the URL
if (!isset($_GET['appointment_id'])) {
    // Redirect or handle the case where appointment_id is not provided
    exit("Appointment ID is missing.");
}

$appointment_id = $_GET['appointment_id'];

// Query to fetch the patient test results
$query = "SELECT * FROM patienttestresults WHERE ConfirmedAppointmentID = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $appointment_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    // Handle case where no patient test results with provided appointment ID are found
    exit("Patient test results not found.");
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
    <title>View Patient Test Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
// Include the navigation bar page
include('main/nav.php');
?>
    <div class="container mt-5">
        <h2 class="text-center">Patient Test Results</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>Attribute</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Confirmed Appointment ID</td>
                    <td><?php echo $row['ConfirmedAppointmentID']; ?></td>
                </tr>
                <tr>
                    <td>Patient ID</td>
                    <td><?php echo $row['PatientID']; ?></td>
                </tr>
                <tr>
                    <td>Lab Account ID</td>
                    <td><?php echo $row['LabAccountID']; ?></td>
                </tr>
                <tr>
                    <td>Test Name</td>
                    <td><?php echo $row['TestName']; ?></td>
                </tr>
                <tr>
                    <td>Test Results</td>
                    <td><?php echo $row['TestResults']; ?></td>
                </tr>
                <tr>
                    <td>PDF Report</td>
                    <td><a href="data:application/pdf;base64,<?php echo base64_encode($row['PDFReport']); ?>" target="_blank">View PDF</a></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
