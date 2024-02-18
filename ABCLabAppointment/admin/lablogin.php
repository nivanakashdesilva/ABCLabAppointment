<?php
session_start();
include('../functional/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check credentials against database
    $query = "SELECT * FROM labaccount WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            // Set session variable based on access level
            $access_level = $row['access_level'];
            if ($access_level == 'technician') {
                $_SESSION['technician_username'] = $username;
            }

            // Redirect based on access level
            if ($access_level == 'admin') {
                $_SESSION['technician_username'] = $username;
                header('Location: admin_portal/admin_index.php');
                exit();
            } elseif ($access_level == 'doctors') {
                $_SESSION['technician_username'] = $username;
                header('Location: doctor_portal/view_appointment.php');
                exit();
            } elseif ($access_level == 'receptionist') {
                $_SESSION['technician_username'] = $username;
                header('Location: receptionist_portal/receptionist_confirm_appointments.php');
                exit();
            } elseif ($access_level == 'technician') {
                $_SESSION['technician_username'] = $username;                                                                                           
                header('Location: technician_portal/technician_lab_report.php');
                exit();
            }
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
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
        <button type="submit" name="login_btn">Login</button>
    </form>
</body>
</html>

