<?php
session_start();
include('../../functional/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_btn'])) {
    // Collect form data
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    
    // Insert doctor data into the database
    $query = "INSERT INTO doctors (fullname, username, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "sss", $fullname, $username, $password);
    
    if (mysqli_stmt_execute($stmt)) {
        // Registration successful
        header('Location: doctor_login.php'); // Redirect to doctor login page
        exit();
    } else {
        // Error occurred during registration
        echo "Error: " . mysqli_error($connection);
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
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>
        </div>
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <button type="submit" name="register_btn">Register</button>
        </div>
    </form>
</body>
</html>
