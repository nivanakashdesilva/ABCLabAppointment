<?php
// Database connection parameters
$host = "localhost"; // Change to your database host if different
$username = "root"; // Default username for MySQL
$password = ""; // Default password for MySQL (usually empty)
$database = "lab_appointment"; // Change to your database name

// Establishing a database connection
$connection = new mysqli($host, $username, $password, $database);

// Check connection
if ($connection->connect_errno) {
    die("Failed to connect to MySQL: (" . $connection->connect_errno . ") " . $connection->connect_error);
}

