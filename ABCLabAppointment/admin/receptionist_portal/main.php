<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Appointments Pie Chart</title>
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Doctor Appointments Pie Chart</h1>
    
    <!-- Create a canvas element for the chart -->
    <canvas id="appointmentChart" width="400" height="400"></canvas>

    <?php
// Include the database connection file
include('../../functional/db.php');

// SQL query to fetch data
$sql = "SELECT doctors.fullname, COUNT(confirmed_appointments.confirmed_appointment_id) AS appointment_count
        FROM confirmed_appointments
        JOIN doctors ON confirmed_appointments.doctor_id = doctors.doctor_id
        GROUP BY confirmed_appointments.doctor_id";

// Execute the query using the connection object from the included file
$result = $connection->query($sql);

// Arrays to store doctor names and appointment counts
$doctorNames = array();
$appointmentCounts = array();

// Fetching and storing data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($doctorNames, $row['fullname']);
        array_push($appointmentCounts, $row['appointment_count']);
    }
}

// Close connection
$connection->close();
?>


    <script>
        // JavaScript code to render the pie chart
        var doctorNames = <?php echo json_encode($doctorNames); ?>;
        var appointmentCounts = <?php echo json_encode($appointmentCounts); ?>;

        // Generate random colors for the pie chart slices
        var colors = [];
        for (var i = 0; i < doctorNames.length; i++) {
            var color = '#' + Math.floor(Math.random()*16777215).toString(16);
            colors.push(color);
        }

        // Generate pie chart
        var ctx = document.getElementById('appointmentChart').getContext('2d');
        var appointmentChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: doctorNames,
                datasets: [{
                    data: appointmentCounts,
                    backgroundColor: colors
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Doctor Appointments'
                }
            }
        });
    </script>
</body>
</html>
