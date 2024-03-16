<?php
session_start();
include('../functional/db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

// Get the patient's full name
$query = "SELECT fullname FROM patients WHERE username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $patient_fullname);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Get the doctor's full name
$query = "SELECT fullname FROM doctors WHERE username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $doctor_fullname);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Query to retrieve upcoming appointments for the patient or doctor
$query = "SELECT ca.confirmed_appointment_id, pa.fullname AS patient_name, doc.fullname AS doctor_name, ca.appointment_date, ca.appointment_time, ca.status
          FROM confirmed_appointments ca
          INNER JOIN patients pa ON ca.patient_id = pa.patient_id
          INNER JOIN doctors doc ON ca.doctor_id = doc.doctor_id
          WHERE (pa.username = ? OR doc.username = ?) AND ca.status = 'pending'";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "ss", $username, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch appointment data
$appointments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Appointments</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php include('main/nav.php'); ?>
        <div class="mt-5">
            <h2 class="text-center mb-4">Pending Appointments</h2>
            <?php if (!empty($appointments)) : ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Doctor Name</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment) : ?>
                    <tr>
                        <td><?php echo $appointment['confirmed_appointment_id']; ?></td>
                        <td><?php echo $appointment['doctor_name']; ?></td>
                        <td><?php echo $appointment['appointment_date']; ?></td>
                        <td><?php echo $appointment['appointment_time']; ?></td>
                        <td><?php echo $appointment['status']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <p class="text-center">No past appointments.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
