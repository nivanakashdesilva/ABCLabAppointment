<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP SQL Runner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            height: 200px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .query-result {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            white-space: pre-wrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            word-wrap: break-word; /* Text wrapping */
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .export-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .export-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        .export-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PHP SQL Runner</h1>
        <form method="post" action="">
            <textarea name="sqlQuery" placeholder="Enter your SQL query here..."></textarea><br>
            <input type="submit" value="Run Query">
        </form>
        <?php
        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Debugging: Print the contents of the $_POST array
            echo "<pre>";
            print_r($_POST);
            echo "</pre>";

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
                    echo "<table>";
                    // Table header
                    echo "<tr>";
                    echo "<th>#</th>"; // Index column
                    while ($field = $result->fetch_field()) {
                        echo "<th>" . $field->name . "</th>";
                    }
                    echo "</tr>";
                    // Table rows
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
            <div class="export-buttons">
                <a href="export-txt.php?sqlQuery=<?= urlencode($_POST["sqlQuery"]) ?>" class="export-button">Export to TXT File</a>
            </div>
        <?php endif; ?>


    </div>
</body>
</html>