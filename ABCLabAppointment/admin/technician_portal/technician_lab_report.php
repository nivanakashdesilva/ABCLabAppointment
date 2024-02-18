<?php
session_start();
include('db.php');

// Check if the technician is logged in
if (!isset($_SESSION['technician_username'])) {
    header('Location: lablogin.php'); // Redirect to technician login page
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
if ($access_level != 'technician') {
    header('Location: lablogin.php'); // Redirect to unauthorized access page
    exit();
}
echo "Reached here"; // Debugging statement

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from the form
    $patient_id = $_POST['patient_id'];
    $test_name = $_POST['test_name'];
    $test_result = $_POST['test_result'];

    // Upload PDF file
    $file_name = $_FILES['lab_report']['name'];
    $file_tmp = $_FILES['lab_report']['tmp_name'];
    $file_type = $_FILES['lab_report']['type'];
    $file_error = $_FILES['lab_report']['error'];

    // Check if file was uploaded without errors
    if ($file_error === 0) {
        $file_destination = 'lab_reports/' . $file_name; // Adjust the destination folder as needed
        move_uploaded_file($file_tmp, $file_destination);

        // Insert lab report into database
        $query = "INSERT INTO lab_reports (patient_id, test_name, test_result, lab_report_file) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "ssss", $patient_id, $test_name, $test_result, $file_destination);

        if (mysqli_stmt_execute($stmt)) {
            // Report submission successful
            $success_message = "Lab report submitted successfully.";
        } else {
            $error = "Failed to submit lab report. Please try again.";
        }
    } else {
        $error = "Error uploading file. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Technician Lab Report Submission</title>
</head>
<body>
    <h2>Technician Lab Report Submission</h2>
    <?php if(isset($error)) { ?>
        <div><?php echo $error; ?></div>
    <?php } ?>
    <?php if(isset($success_message)) { ?>
        <div><?php echo $success_message; ?></div>
    <?php } ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <div>
            <label>Patient ID:</label>
            <input type="text" name="patient_id" required>
        </div>
        <div>
            <label>Test Name:</label>
            <input type="text" name="test_name" required>
        </div>
        <div>
            <label>Test Result:</label>
            <textarea name="test_result" required></textarea>
        </div>
        <div>
            <label>Lab Report PDF:</label>
            <input type="file" name="lab_report" accept=".pdf" required>
        </div>
        <button type="submit" name="submit_btn">Submit Lab Report</button>
    </form>
</body>
</html>
