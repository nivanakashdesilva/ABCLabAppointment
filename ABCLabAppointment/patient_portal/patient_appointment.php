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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Appointment</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
        // Include the navigation bar page
        include('main/nav.php');
        ?>
        <div class="mt-5">
            <h2 class="text-center">Request Appointment</h2>
            <h3 class="text-center">Welcome,
                <?php echo $username; ?>!
            </h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <label for="appointmentDate" class="form-label">Appointment Date:</label>
                    <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
                </div>
                <div class="mb-3">
                    <label for="appointmentTime" class="form-label">Appointment Time:</label>
                    <input type="time" class="form-control" id="appointmentTime" name="appointment_time" required>
                </div>
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Appointment:</label>
                    <textarea class="form-control" id="reason" name="reason" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" name="request_btn">Request Appointment</button>
            </form>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>