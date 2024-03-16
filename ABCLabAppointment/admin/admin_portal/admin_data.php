<?php
session_start();
include('../../functional/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $access_level = $_POST['access_level']; // Assuming access level is selected from a dropdown

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new admin into database
    $query = "INSERT INTO labaccount (fullname, username, password, access_level) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $fullname, $username, $hashed_password, $access_level);

    if (mysqli_stmt_execute($stmt)) {
        // Registration successful
        header('Location: admin_dashboard.php'); // Redirect to admin dashboard or another page
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
</head>
<body>
<?php include('main_admin/nav.php'); ?>
    <h2>Admin Registration</h2>
    <?php if(isset($error)) { ?>
        <div><?php echo $error; ?></div>
    <?php } ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label>Fullname:</label>
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
        <div>
            <label>Access Level:</label>
            <select name="access_level" required>
                <option value="admin">Admin</option>
                <option value="doctors">doctors</option>
                <option value="receptionist">receptionist</option>
                <option value="technician">technician</option>
            </select>
        </div>
        <button type="submit" name="register_btn">Register</button>
    </form>
</body>
</html>
