<?php
session_start();
include('../../functional/db.php');
/* 
// Check if the technician is logged in
if (!isset($_SESSION['technician_username'])) {
    header('Location: ../lablogin.php'); // Redirect to technician login page
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
if ($access_level != 'receptionist') {
    header('Location: ../lablogin.php'); // Redirect to unauthorized access page
    exit();
}*/
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_btn'])) {
    $appointment_id = $_POST['appointment_id'];
    
    // Update appointment status to 'confirmed' in the database
    $update_query = "UPDATE appointment_requests SET status = 'confirmed' WHERE appointment_id = ?";
    $stmt = mysqli_prepare($connection, $update_query);
    mysqli_stmt_bind_param($stmt, "i", $appointment_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Get the selected doctor ID from the form
    $selected_doctor_id = $_POST['doctor_id'];
    
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
    
    // Redirect to the same page to refresh appointment list
    header('Location: receptionist_confirm_appointments.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Confirm Appointments</title>
</head>
<body>
    <h2>Receptionist Confirm Appointments</h2>
    <?php if (!empty($appointments)) : ?>
    <ul>
        <?php foreach ($appointments as $appointment) : ?>
        <li>
            <strong>Patient Name:</strong> <?php echo $appointment['patient_name']; ?><br>
            <strong>Appointment Date:</strong> <?php echo $appointment['appointment_date']; ?><br>
            <strong>Appointment Time:</strong> <?php echo $appointment['appointment_time']; ?><br>
            <!-- Add a dropdown for selecting the doctor -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                <select name="doctor_id">
                    <?php
                    // Fetch and display list of doctors
                    $doctor_query = "SELECT doctor_id, fullname FROM doctors";
                    $doctor_result = mysqli_query($connection, $doctor_query);
                    while ($doctor_row = mysqli_fetch_assoc($doctor_result)) {
                        echo "<option value=\"{$doctor_row['doctor_id']}\">{$doctor_row['fullname']}</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="confirm_btn">Confirm</button>
            </form>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else : ?>
    <p>No appointments need confirmation.</p>
    <?php endif; ?>
</body>
</html>