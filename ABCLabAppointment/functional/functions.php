<?php
function validateCredentials($username, $password, $connection) {
    // Prepare SQL statement to fetch user with the given username
    $query = "SELECT password FROM patients WHERE username = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    // Check if a user with the given username exists
    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $hashed_password);
        mysqli_stmt_fetch($stmt);
        // Verify the provided password against the hashed password
        if (password_verify($password, $hashed_password)) {
            return true; // Passwords match, credentials are valid
        }
    }
    
    return false; // Either user doesn't exist or password doesn't match
}
?>
