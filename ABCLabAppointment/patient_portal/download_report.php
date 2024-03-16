<?php
require_once("../functional/db.php"); // Include the database connection script

// Check if appointment ID is provided in the URL
if (isset($_GET['ConfirmedAppointmentID'])) { // Check for 'ConfirmedAppointmentID'
    // Prepare and bind parameters
    $stmt = $connection->prepare("SELECT PDFReport FROM patienttestresults WHERE confirmed_appointment_id = ?");
    $stmt->bind_param("i", $confirmed_appointment_id);

    // Set parameter and execute
    $confirmed_appointment_id = $_GET['ConfirmedAppointmentID'];
    $stmt->execute();

    // Bind result variable
    $stmt->bind_result($pdfReport);

    // Fetch result
    if ($stmt->fetch()) {
        // Set headers for PDF file
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename='report.pdf'");
        header("Content-Transfer-Encoding: binary");
        header("Accept-Ranges: bytes");

        // Output PDF contents
        echo $pdfReport;
    } else {
        echo "Report not found for this confirmed appointment ID.";
    }

    // Close statement
    $stmt->close();
} else {
    echo "Confirmed Appointment ID not provided.";
}
?>
