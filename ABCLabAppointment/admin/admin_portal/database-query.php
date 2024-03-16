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
if ($access_level != 'admin') {
    header('Location: ../lablogin.php'); // Redirect to unauthorized access page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP SQL Runner</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Common CSS Styles -->
    <style>
        /* Add custom styles here */
        body {
            padding-top: 60px;
        }
    </style>
</head>
<body>
<?php include('main_admin/nav.php'); ?>
    <div class="container mt-5">
        <h1 class="text-center">PHP SQL Runner</h1>
        <form method="post" action="">
            <div class="form-group">
                <textarea class="form-control" name="sqlQuery" rows="5" placeholder="Enter your SQL query here..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Run Query</button>
        </form>
        <?php
        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Debugging: Print the contents of the $_POST array
            

            // Get the SQL query from the form
            $sqlQuery = $_POST["sqlQuery"];

            // Database connection details
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "lab_appointment";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Execute the SQL query
            $result = $conn->query($sqlQuery);

            // Display the result
            if ($result === FALSE) {
                echo "<div class='query-result'>Error executing query: " . $conn->error . "</div>";
            } else {
                echo "<div class='query-result'>";
                if ($result->num_rows > 0) {
                    echo "<h2>Query Result</h2>";
                    echo "<table class='table'>";
                    // Table header
                    echo "<thead class='thead-light'>";
                    echo "<tr>";
                    echo "<th>#</th>"; // Index column
                    while ($field = $result->fetch_field()) {
                        echo "<th>" . $field->name . "</th>";
                    }
                    echo "</tr>";
                    echo "</thead>";
                    // Table body
                    echo "<tbody>";
                    $index = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>$index</td>"; // Display index
                        foreach ($row as $value) {
                            echo "<td>$value</td>";
                        }
                        echo "</tr>";
                        $index++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "No rows found.";
                }
                echo "</div>";
            }

            // Close connection
            $conn->close();
        }
        ?>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sqlQuery"]) && !empty(trim($_POST["sqlQuery"]))): ?>
            <div class="text-center mt-4">
                <a href="export-txt.php?sqlQuery=<?= urlencode($_POST["sqlQuery"]) ?>" class="btn btn-primary">Export to TXT File</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Include Bootstrap JavaScript (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
