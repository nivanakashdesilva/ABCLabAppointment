<?php
session_start();
include('../functional/db.php');

$error_admin = '';

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
            } elseif ($access_level == 'receptionist') {
                $_SESSION['technician_username'] = $username;
                header('Location: receptionist_portal/main.php');
                exit();
            } elseif ($access_level == 'technician') {
                $_SESSION['technician_username'] = $username;
                header('Location: technician_portal/tech.php');
                exit();
            }
        } else {
            $error_admin = "Invalid username or password";
        }
    } else {
        $error_admin = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Additional custom styles can be added here if needed */
        #admin_container {
            margin-top: 50px; /* Adjust margin as needed */
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="admin_container" class="col-md-6 mx-auto">
            <h2 class="text-center">Admin Login</h2>
            <div class="collapse" id="errorCollapse">
                <div class="alert alert-danger" id="errorAlert"></div>
            </div>
            <?php if(!empty($error_admin)) { ?>
                <script>
                    // Function to show error message
                    function showError(message) {
                        document.getElementById('errorAlert').innerText = message;
                        document.getElementById('errorCollapse').classList.add('show');
                    }

                    // Show error message if $error_admin is not empty
                    var errorAdmin = '<?php echo $error_admin; ?>';
                    if (errorAdmin !== '') {
                        showError(errorAdmin);
                    }
                </script>
            <?php } ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="admin_username">Username:</label>
                    <input type="text" id="admin_username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="admin_password">Password:</label>
                    <input type="password" id="admin_password" name="password" class="form-control" required>
                </div>
                <p class="mt-3">Click here to access <a href="doctor_portal">Doctors Login Portal</a></p>
                <button type="submit" name="login_btn" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Include Bootstrap JavaScript (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

