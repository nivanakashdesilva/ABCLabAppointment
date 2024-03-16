<?php
// Include database connection
include('../../functional/db.php');

// Check if the ID parameter is provided in the URL
if (!isset($_GET['id'])) {
    exit("PDF ID is missing.");
}

// Get the ID from the URL
$pdf_id = $_GET['id'];

// Query to fetch the PDF file from the database based on the provided ID
$query = "SELECT PDFReport FROM patienttestresults WHERE id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $pdf_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if a record with the provided ID exists
if (mysqli_num_rows($result) == 0) {
    exit("PDF not found.");
}

// Fetch the PDF content
$pdf_data = mysqli_fetch_assoc($result)['PDFReport'];

// Output appropriate headers for PDF content
header("Content-type: application/pdf");
header("Content-Length: " . strlen($pdf_data));

// Output the PDF content to the browser
echo $pdf_data;
?>
