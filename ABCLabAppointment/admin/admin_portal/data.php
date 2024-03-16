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

// Fetch table names from your database
$tablesQuery = "SHOW TABLES";
$tablesResult = mysqli_query($connection, $tablesQuery);

// Store table names in an array
$tables = [];
while ($row = mysqli_fetch_row($tablesResult)) {
    $tables[] = $row[0];
}

// Check if a filter is selected
if (isset($_GET['filter']) && in_array($_GET['filter'], $tables)) {
    // Filter is selected, apply filter query here
    $selectedFilter = $_GET['filter'];
    $filterQuery = "SELECT * FROM $selectedFilter"; // Modify this query based on your requirements
    $result = mysqli_query($connection, $filterQuery);
    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Generating Page</title>
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
        <h2>Report Generating Page</h2>
        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="filter">Filter by:</label>
                <select class="form-control" name="filter" id="filter">
                    <option value="">Select Table</option>
                    <?php foreach ($tables as $table) : ?>
                        <option value="<?php echo $table; ?>"><?php echo $table; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </form>
        <br>
        <?php if (isset($data) && !empty($data)) : ?>
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <?php foreach ($data[0] as $key => $value) : ?>
                                <th><?php echo $key; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row) : ?>
                            <tr>
                                <?php foreach ($row as $value) : ?>
                                    <td><?php echo $value; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Include Bootstrap JavaScript (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
