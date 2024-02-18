<?php
session_start();
include('../functional/db.php');
include('../functional/functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate username and password against the database
    if (validateCredentials($username, $password, $connection)) {
        // Set up session with logged-in user information
        $_SESSION['username'] = $username;
        // Redirect to the appointment request page
        header('Location: patient_appointment.php');
        exit();
    } else {
        // Handle invalid credentials (e.g., display error message)
        $error = "Invalid username or password";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if(isset($error)) { ?>
        <div><?php echo $error; ?></div>
    <?php } ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</body>
</html>

