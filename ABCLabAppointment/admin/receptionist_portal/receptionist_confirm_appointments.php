<?php
session_start();
include('../../functional/db.php');

// Check if the user is authenticated
if (!isset($_SESSION['technician_username'])) {
    header('Location: index.php'); // Redirect to the login page
    exit();
}

// Query to retrieve all pending appointments
$query = "SELECT ar.appointment_id, p.fullname AS patient_name, ar.appointment_date, ar.appointment_time
          FROM appointment_requests ar
          INNER JOIN patients p ON ar.patient_id = p.patient_id
          WHERE ar.status = 'pending'";
$result = mysqli_query($connection, $query);

// Fetch appointment data
$appointments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}

// Handle appointment confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm_btn'])) {
        $appointment_id = $_POST['appointment_id'];
        $selected_doctor_id = $_POST['doctor_id'];
        
        // Update appointment status to 'confirmed' in the database
        $update_query = "UPDATE appointment_requests SET status = 'confirmed' WHERE appointment_id = ?";
        $stmt = mysqli_prepare($connection, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $appointment_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Retrieve confirmed appointment details
        $select_query = "SELECT * FROM appointment_requests WHERE appointment_id = ?";
        $stmt = mysqli_prepare($connection, $select_query);
        mysqli_stmt_bind_param($stmt, "i", $appointment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $confirmed_appointment = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        // Insert confirmed appointment into the confirmed_appointments table
        $insert_query = "INSERT INTO confirmed_appointments (patient_id, doctor_id, appointment_date, appointment_time)
                         VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $insert_query);
        mysqli_stmt_bind_param($stmt, "iiss", $confirmed_appointment['patient_id'], $selected_doctor_id, $confirmed_appointment['appointment_date'], $confirmed_appointment['appointment_time']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Redirect to refresh the page
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } elseif (isset($_POST['decline_btn'])) {
        $appointment_id = $_POST['appointment_id'];
        
        // Update appointment status to 'declined' in the database
        $update_query = "UPDATE appointment_requests SET status = 'declined' WHERE appointment_id = ?";
        $stmt = mysqli_prepare($connection, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $appointment_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Redirect to refresh the page
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Confirm Appointments</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('main_receptionist/nav.php'); ?>

    <div class="container mt-5">
        <h2 class="mb-4">Receptionist Confirm Appointments</h2>

        <?php if (!empty($appointments)) : ?>
            <div class="list-group">
                <?php foreach ($appointments as $appointment) : ?>
                    <div class="list-group-item">
                        <p class="mb-1"><strong>Patient Name:</strong> <?php echo $appointment['patient_name']; ?></p>
                        <p class="mb-1"><strong>Appointment Date:</strong> <?php echo $appointment['appointment_date']; ?></p>
                        <p class="mb-1"><strong>Appointment Time:</strong> <?php echo $appointment['appointment_time']; ?></p>
                        <!-- Add a dropdown for selecting the doctor -->
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                            <select class="form-select mb-2" name="doctor_id">
                                <?php
                                // Fetch and display list of doctors
                                $doctor_query = "SELECT doctor_id, fullname FROM doctors";
                                $doctor_result = mysqli_query($connection, $doctor_query);
                                while ($doctor_row = mysqli_fetch_assoc($doctor_result)) {
                                    echo "<option value=\"{$doctor_row['doctor_id']}\">{$doctor_row['fullname']}</option>";
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn btn-primary me-2" name="confirm_btn">Confirm</button>
                            <button type="submit" class="btn btn-danger" name="decline_btn">Decline</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>No appointments need confirmation.</p>
        <?php endif; ?>
    </div>

    <!-- Include Bootstrap JS if needed -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
