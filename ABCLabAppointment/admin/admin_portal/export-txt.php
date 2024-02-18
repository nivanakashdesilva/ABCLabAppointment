<?php
// Check if the SQL query is provided in the URL parameter
if(isset($_GET["sqlQuery"])) {
    // Retrieve and decode the SQL query
    $sqlQuery = urldecode($_GET["sqlQuery"]);
    
    // Validate the SQL query
    if(!empty(trim($sqlQuery))) {
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

// Check if the query was successful
if ($result === FALSE) {
    echo "Error executing query: " . $conn->error;
} else {
    // Create a filename for the TXT file (e.g., query_result.txt)
    $filename = "query_result.txt";

    // Open the file for writing
    $file = fopen($filename, "w");

    // Write the header row to the file
    $headerRow = [];
    while ($field = $result->fetch_field()) {
        $headerRow[] = $field->name;
    }
    fwrite($file, implode("\t", $headerRow) . "\n");

    // Write the data rows to the file
    while ($row = $result->fetch_assoc()) {
        fwrite($file, implode("\t", $row) . "\n");
    }

    // Close the file
    fclose($file);

    // Provide a download link for the user
    header("Content-Disposition: attachment; filename=" . $filename);
    header("Content-Type: application/octet-stream");
    header("Content-Length: " . filesize($filename));
    readfile($filename);

    // Delete the file after downloading
    unlink($filename);
}

// Close connection
$conn->close();
    } else {
        echo "Empty or invalid SQL query provided.";
    }
} else {
    echo "No SQL query provided.";
}
?>
?>
