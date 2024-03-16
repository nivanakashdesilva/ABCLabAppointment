    <?php
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include('../functional/db.php');

    // Define variables and initialize with empty values
    $fullname = $nic = $username = $password = $address = $email = $mobile_number = "";
    $fullname_err = $nic_err = $username_err = $password_err = $address_err = $email_err = $mobile_number_err = "";

    // Processing form data when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate fullname
        if (empty(trim($_POST["fullname"]))) {
            $fullname_err = "Please enter your fullname.";
        } else {
            $fullname = trim($_POST["fullname"]);
        }

        // Validate NIC
        if (empty(trim($_POST["nic"]))) {
            $nic_err = "Please enter your NIC.";
        } else {
            $nic = trim($_POST["nic"]);
        }

        // Validate username
        if (empty(trim($_POST["username"]))) {
            $username_err = "Please enter a username.";
        } else {
            $username = trim($_POST["username"]);
            // Check if username is already taken
            $sql = "SELECT username FROM patients WHERE username = ?";
            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }

        // Validate password
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a password.";
        } else {
            $password = trim($_POST["password"]);
        }

        // Validate address
        if (empty(trim($_POST["address"]))) {
            $address_err = "Please enter your address.";
        } else {
            $address = trim($_POST["address"]);
        }

        // Validate email
        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter your email.";
        } else {
            $email = trim($_POST["email"]);
        }

        // Validate mobile number
        if (empty(trim($_POST["mobile_number"]))) {
            $mobile_number_err = "Please enter your mobile number.";
        } else {
            $mobile_number = trim($_POST["mobile_number"]);
        }

        // If no errors, proceed with registration
        if (empty($fullname_err) && empty($nic_err) && empty($username_err) && empty($password_err) && empty($address_err) && empty($email_err) && empty($mobile_number_err)) {
            // Prepare an insert statement
            $sql = "INSERT INTO patients (fullname, nic, username, password, address, email, mobile_number) VALUES (?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($connection, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sssssss", $param_fullname, $param_nic, $param_username, $param_password, $param_address, $param_email, $param_mobile_number);

                // Set parameters
                $param_fullname = $fullname;
                $param_nic = $nic;
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                $param_address = $address;
                $param_email = $email;
                $param_mobile_number = $mobile_number;

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to registration success page
                    header("location: registration_success.php");
                    exit();
                } else {
                    echo "Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }

        // Close connection
        mysqli_close($connection);
    }
    ?>
    
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Patient Registration</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="fullname" class="form-label">Fullname:</label>
                <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo $fullname; ?>" required>
                <span class="error"><?php echo $fullname_err; ?></span>
            </div>
            <div class="mb-3">
                <label for="nic" class="form-label">NIC:</label>
                <input type="text" class="form-control" id="nic" name="nic" value="<?php echo $nic; ?>" required>
                <span class="error"><?php echo $nic_err; ?></span>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
                <span class="error"><?php echo $username_err; ?></span>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address:</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" required>
                <span class="error"><?php echo $address_err; ?></span>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <div class="mb-3">
                <label for="mobile_number" class="form-label">Mobile Number:</label>
                <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo $mobile_number; ?>" required>
                <span class="error"><?php echo $mobile_number_err; ?></span>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS (optional) -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
