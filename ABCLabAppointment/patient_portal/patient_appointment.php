<?php
session_start();
include('../functional/db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

// Get the patient's ID from the database
$query = "SELECT patient_id FROM patients WHERE username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $patient_id);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    // Insert appointment data into the database
    $query = "INSERT INTO appointment_requests (patient_id, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $patient_id, $appointment_date, $appointment_time, $reason);

    if (mysqli_stmt_execute($stmt)) {
        // Appointment request successfully inserted
        header('Location: appointment_confirmation.php');
        exit();
    } else {
        // Error occurred during insertion
        echo "Error: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Appointment</title>
</head>
<body>
    <h2>Welcome, <?php echo $username; ?>!</h2>
    <h3>Request Appointment</h3>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label>Appointment Date:</label>
            <input type="date" name="appointment_date" required>
        </div>
        <div>
            <label>Appointment Time:</label>
            <input type="time" name="appointment_time" required>
        </div>
        <div>
            <label>Reason for Appointment:</label>
            <textarea name="reason" required></textarea>
        </div>
        <button type="submit" name="request_btn">Request Appointment</button>
    </form>
</body>
</html>

