<?php
session_start();

/*// Check if user is logged in as admin, you need to implement your own authentication mechanism here
if (!isset($_SESSION['admin_username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}*/

// Database connection
include('../../functional/db.php');

// Initialize variables
$queryResult = null;
$error = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql_query = $_POST['sql_query'];
    
    // Execute SQL query
    $result = mysqli_query($connection, $sql_query);
    
    if ($result === false) {
        // SQL query execution failed, retrieve error message
        $error = mysqli_error($connection);
    } else {
        // SQL query execution successful, fetch results if any
        $queryResult = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $queryResult[] = $row;
        }
    }
}

// Close connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Query Runner</title>
</head>
<body>
    <h2>SQL Query Runner</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="sql_query">Enter SQL Query:</label>
            <textarea name="sql_query" id="sql_query" rows="4" cols="50" required></textarea>
        </div>
        <button type="submit">Execute Query</button>
    </form>

    <?php if ($error !== null) : ?>
        <p>Error: <?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($queryResult !== null) : ?>
        <?php if (empty($queryResult)) : ?>
            <p>No results found.</p>
        <?php else: ?>
            <h3>Query Results:</h3>
            <table border="1">
                <thead>
                    <tr>
                        <th>Index ID</th>
                        <?php foreach ($queryResult[0] as $fieldName => $value) : ?>
                            <th><?php echo htmlspecialchars($fieldName); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($queryResult as $index => $row) : ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <?php foreach ($row as $value) : ?>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
