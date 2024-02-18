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
$query = "SELECT access_level FROM labaccount WHERE username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $technician_username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $access_level);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Check if the technician has appropriate access level
if ($access_level != 'doctors') {
    header('Location: ../lablogin.php'); // Redirect to unauthorized access page
    exit();
}

// Fetch doctor's username
$doctor_username = $_SESSION['technician_username'];

// Query to retrieve upcoming appointments for the doctor including the status
$query = "SELECT ca.confirmed_appointment_id, p.fullname AS patient_name, ca.appointment_date, ca.appointment_time, ca.status
          FROM confirmed_appointments ca
          INNER JOIN patients p ON ca.patient_id = p.patient_id
          WHERE ca.doctor_id = (
              SELECT doctor_id FROM doctors WHERE username = ?
          ) AND ca.status = 'pending'";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $doctor_username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch appointment data
$appointments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}
mysqli_stmt_close($stmt);

// Close Appointment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['close_btn'])) {
    $appointment_id = $_POST['appointment_id'];
    // Update the appointment status to closed
    $query = "UPDATE confirmed_appointments SET status = 'closed' WHERE confirmed_appointment_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $appointment_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    // Redirect to the same page to refresh the appointment list
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor View Appointments</title>
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
    <h2>Doctor View Appointments</h2>
    <?php if (!empty($appointments)) : ?>
    <table>
        <tr>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($appointments as $appointment) : ?>
        <tr>
            <td><?php echo $appointment['confirmed_appointment_id']; ?></td>
            <td><a href="view_report.php?appointment_id=<?php echo $appointment['confirmed_appointment_id']; ?>"><?php echo $appointment['patient_name']; ?></a></td>
            <td><?php echo $appointment['appointment_date']; ?></td>
            <td><?php echo $appointment['appointment_time']; ?></td>
            <td><?php echo $appointment['status']; ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['confirmed_appointment_id']; ?>">
                    <button type="submit" name="close_btn">Close</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else : ?>
    <p>No upcoming appointments.</p>
    <?php endif; ?>
</body>
</html>
