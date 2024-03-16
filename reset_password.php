<?php
// Include database connection
require_once 'db_connection.php';

// Define variables and initialize with empty values
$username_email = $new_password = $confirm_password = "";
$username_email_err = $new_password_err = $confirm_password_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username or email
    if (empty(trim($_POST["username_email"]))) {
        $username_email_err = "Please enter your username or email.";
    } else {
        $username_email = trim($_POST["username_email"]);
    }

    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before updating the password
    if (empty($username_email_err) && empty($new_password_err) && empty($confirm_password_err)) {
        // Check if the input is an email
        if (filter_var($username_email, FILTER_VALIDATE_EMAIL)) {
            // If email, find user by email
            $sql = "SELECT id, email FROM users WHERE email = ?";
        } else {
            // If not email, find user by username
            $sql = "SELECT id, username FROM users WHERE username = ?";
        }
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username_email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $user_id = $row['id'];

                // Update password hash in the database
                $sql_update = "UPDATE users SET password = ? WHERE id = ?";
                if ($stmt_update = $conn->prepare($sql_update)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt_update->bind_param("si", $hashed_password, $user_id);
                    if ($stmt_update->execute()) {
                        // Password updated successfully
                        header("location: index.php");
                        exit();
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    $stmt_update->close();
                }
            } else {
                $username_email_err = "No user found with that username or email.";
            }
            $stmt->close();
        }
    }

    // Close database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to your CSS stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header -->
    <?php include_once 'header.php'; ?>

    <div class="container mt-5">
        <h2>Reset Password</h2>
        <p>Please enter your username or email and the new password to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username or Email</label>
                <input type="text" name="username_email" class="form-control <?php echo (!empty($username_email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username_email; ?>">
                <span class="invalid-feedback"><?php echo $username_email_err; ?></span>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Reset">
            </div>
        </form>
    </div>

    <!-- Footer -->
    <?php include_once 'footer.php'; ?>

</body>
</html>
