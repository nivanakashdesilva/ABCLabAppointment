<?php
session_start();
include('../../functional/db.php');



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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        h2 {
            margin-top: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        select {
            padding: 5px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 8px 16px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Report Generating Page</h2>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="filter">Filter by:</label>
        <select name="filter" id="filter">
            <option value="">Select Table</option>
            <?php foreach ($tables as $table) : ?>
                <option value="<?php echo $table; ?>"><?php echo $table; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Apply Filter</button>
    </form>
    <br>
    <?php if (isset($data) && !empty($data)) : ?>
        <table>
            <thead>
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
    <?php endif; ?>
</body>
</html>
