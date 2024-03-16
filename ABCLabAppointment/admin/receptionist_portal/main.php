<?php
// Include the database connection file
include('../../functional/db.php');

// SQL query to fetch data for status chart
$statusSql = "SELECT status, COUNT(*) AS count FROM confirmed_appointments GROUP BY status";

// Execute the query using the connection object from the included file
$statusResult = $connection->query($statusSql);

// Arrays to store status labels and appointment counts for status chart
$statusLabels = array();
$appointmentCounts = array();

// Fetching and storing data for status chart
if ($statusResult->num_rows > 0) {
    while ($row = $statusResult->fetch_assoc()) {
        array_push($statusLabels, $row['status']);
        array_push($appointmentCounts, $row['count']);
    }
}

// SQL query to fetch data for doctor appointments chart
$doctorSql = "SELECT doctors.fullname, COUNT(confirmed_appointments.confirmed_appointment_id) AS appointment_count
            FROM confirmed_appointments
            JOIN doctors ON confirmed_appointments.doctor_id = doctors.doctor_id
            GROUP BY confirmed_appointments.doctor_id";

// Execute the query using the connection object from the included file
$doctorResult = $connection->query($doctorSql);

// Arrays to store doctor names and appointment counts for doctor appointments chart
$doctorNames = array();
$doctorAppointmentCounts = array();

// Fetching and storing data for doctor appointments chart
if ($doctorResult->num_rows > 0) {
    while ($row = $doctorResult->fetch_assoc()) {
        array_push($doctorNames, $row['fullname']);
        array_push($doctorAppointmentCounts, $row['appointment_count']);
    }
}

// Close connection
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Status and Doctor Appointments Pie Charts</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php include('main_receptionist/nav.php'); ?>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-6">
                <h1 class="text mb-4">Appointment Status</h1>
                <!-- Create a canvas element for the status chart -->
                <canvas id="statusChart" width="400" height="400"></canvas>
            </div>
            <div class="col-md-6">
                <h1 class="text mb-4">Doctor Appointments</h1>
                <!-- Create a canvas element for the doctor appointments chart -->
                <canvas id="appointmentChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>

    <script>
    // JavaScript code to render the pie chart for appointment status
    var statusLabels = <?php echo json_encode($statusLabels); ?>;
    var appointmentCounts = <?php echo json_encode($appointmentCounts); ?>;
    var statusColors = statusLabels.map(function() {
        return '#' + Math.floor(Math.random() * 16777215).toString(16);
    });

    var statusCtx = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: appointmentCounts,
                backgroundColor: statusColors
            }]
        },
        options: {
            title: {
                display: true,
                text: 'Appointment Status'
            },
            responsive: false, // Disable responsiveness
            maintainAspectRatio: false, // Disable aspect ratio
            width: 100, // Set width
            height: 100 // Set height
        }
    });

    // JavaScript code to render the pie chart for doctor appointments
    var doctorNames = <?php echo json_encode($doctorNames); ?>;
    var doctorAppointmentCounts = <?php echo json_encode($doctorAppointmentCounts); ?>;
    var doctorColors = doctorNames.map(function() {
        return '#' + Math.floor(Math.random() * 16777215).toString(16);
    });

    var doctorCtx = document.getElementById('appointmentChart').getContext('2d');
    var doctorChart = new Chart(doctorCtx, {
        type: 'pie',
        data: {
            labels: doctorNames,
            datasets: [{
                data: doctorAppointmentCounts,
                backgroundColor: doctorColors
            }]
        },
        options: {
            title: {
                display: true,
                text: 'Doctor Appointments'
            },
            responsive: false, // Disable responsiveness
            maintainAspectRatio: false, // Disable aspect ratio
            width: 100, // Set width
            height: 100 // Set height
        }
    });
</script>


    <!-- Include Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
