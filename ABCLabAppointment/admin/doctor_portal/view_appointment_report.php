<?php
// Include database connection
include('../../functional/db.php');

// Initialize variables
$search_results = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the confirmed_appointment_id from the form
    $confirmed_appointment_id = $_POST['confirmed_appointment_id'];

    // Prepare and execute the SQL query to fetch records based on confirmed_appointment_id
    $query = "SELECT * FROM patienttestresults WHERE confirmed_appointment_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $confirmed_appointment_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch and store the search results
    while ($row = mysqli_fetch_assoc($result)) {
        $search_results[] = $row;
    }

    // Close statement
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Patient Test Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('main/nav.php'); ?>
    <div class="container mt-5">
        <h2>Search Patient Test Results</h2>
        <form method="post">
            <div class="mb-3">
                <label for="confirmed_appointment_id" class="form-label">Enter Appointment ID:</label>
                <input type="text" class="form-control" id="confirmed_appointment_id" name="confirmed_appointment_id" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php if (!empty($search_results)) : ?>
            <h3 class="mt-4">Search Results</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient ID</th>
                        <th>Lab Account ID</th>
                        <th>Confirmed Appointment ID</th>
                        <th>Test Name</th>
                        <th>Test Results</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_results as $row) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['patient_id']; ?></td>
                            <td><?php echo $row['labaccount_id']; ?></td>
                            <td><?php echo $row['confirmed_appointment_id']; ?></td>
                            <td><?php echo $row['TestName']; ?></td>
                            <td><?php echo $row['TestResults']; ?></td>
                            <td>
                                <?php if (!empty($row['PDFReport'])) : ?>
                                    <a href="view_pdf.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View PDF</a>
                                <?php else : ?>
                                    No PDF Available
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
