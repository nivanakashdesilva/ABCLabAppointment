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
$query = "SELECT access_level, id FROM labaccount WHERE username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $technician_username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $access_level, $labAccountID);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Check if the technician has appropriate access level
if ($access_level != 'technician') {
    header('Location: ../lablogin.php'); // Redirect to unauthorized access page
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind parameters
    $stmt = $connection->prepare("INSERT INTO patienttestresults (patient_id, labaccount_id, confirmed_appointment_id, TestName, TestResults, PDFReport) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisss", $patientID, $labAccountID, $confirmedAppointmentID, $testName, $testResults, $pdfReport);

    // Set parameters and execute
    $patientID = $_POST['patientID'];
    $confirmedAppointmentID = $_POST['confirmedAppointmentID'];
    $testName = $_POST['testName'];
    $testResults = $_POST['testResults'];
    $pdfReport = file_get_contents($_FILES['pdfReport']['tmp_name']);

    if ($stmt->execute()) {
        echo "Data uploaded successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Patient Test Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Upload Patient Test Results</h2>
            <!-- Signout Button -->
            <a href="../main/signout.php" class="btn btn-danger">Sign Out</a>
        </div>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="patientID" class="form-label">Patient ID:</label>
                <input type="text" class="form-control" id="patientID" name="patientID">
            </div>
            <div class="mb-3">
                <label for="labAccountID" class="form-label">Lab Account ID:</label>
                <input type="text" class="form-control" id="labAccountID" name="labAccountID" value="<?php echo $labAccountID; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="confirmedAppointmentID" class="form-label">Confirmed Appointment ID:</label>
                <input type="text" class="form-control" id="confirmedAppointmentID" name="confirmedAppointmentID">
            </div>
            <div class="mb-3">
                <label for="testName" class="form-label">Test Name:</label>
                <input type="text" class="form-control" id="testName" name="testName">
            </div>
            <div class="mb-3">
                <label for="testResults" class="form-label">Test Results:</label>
                <input type="text" class="form-control" id="testResults" name="testResults">
            </div>
            <div class="mb-3">
                <label for="pdfReport" class="form-label">PDF Report:</label>
                <input type="file" class="form-control" id="pdfReport" name="pdfReport">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
