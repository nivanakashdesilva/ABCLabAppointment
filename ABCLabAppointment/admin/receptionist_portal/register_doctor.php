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
if ($access_level != 'receptionist') {
    header('Location: ../lablogin.php'); // Redirect to unauthorized access page
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new doctor into database
    $query = "INSERT INTO doctors (fullname, username, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "sss", $fullname, $username, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        // Registration successful
        header('Location: doctor_login.php'); // Redirect to doctor login page
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration</title>
</head>
<body>
    <h2>Doctor Registration</h2>
    <?php if(isset($error)) { ?>
        <div><?php echo $error; ?></div>
    <?php } ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label>Full Name:</label>
            <input type="text" name="fullname" required>
        </div>
        <div>
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" name="register_btn">Register</button>
    </form>
</body>
</html>
