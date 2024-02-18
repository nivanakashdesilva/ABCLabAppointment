<?php
session_start();
include('db.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_username'])) {
    header('Location: lablogin.php'); // Redirect to admin login page
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $access_level = $_POST['access_level']; // Assuming access level is selected from a dropdown

    // Check if the username already exists
    $query = "SELECT id FROM admins WHERE username = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $error = "Username already exists. Please choose a different username.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new admin into database
        $query = "INSERT INTO admins (username, password, access_level) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $access_level);

        if (mysqli_stmt_execute($stmt)) {
            // Registration successful
            header('Location: admin_dashboard.php'); // Redirect to admin dashboard or another page
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
</head>
<body>
    <h2>Admin Registration</h2>
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
        <div>
            <label>Access Level:</label>
            <select name="access_level" required>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="supervisor">Supervisor</option>
                <option value="employee">Employee</option>
            </select>
        </div>
        <button type="submit" name="register_btn">Register</button>
    </form>
</body>
</html>
