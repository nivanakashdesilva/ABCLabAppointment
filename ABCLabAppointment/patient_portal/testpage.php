<?session_start();
include('../functional/db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
?>

<H1>loged in </H1>

<p><a href="patient_appointment.php">appointment</a></p>