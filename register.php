<?php
// Include header
include_once 'header.php';

// Initialize variables with empty values
$username = $password = $confirm_password = $email = $role = "";
$username_err = $password_err = $confirm_password_err = $email_err = $role_err = $registration_err = "";

// Process form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and process registration
    // Include database connection
    require_once 'db_connection.php';

    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Check if username already exists
    $sql_check_username = "SELECT id FROM users WHERE username = ?";
    $stmt_check_username = $conn->prepare($sql_check_username);
    $stmt_check_username->bind_param("s", $username);
    $stmt_check_username->execute();
    $stmt_check_username->store_result();
    if ($stmt_check_username->num_rows > 0) {
        $username_err = "Username already exists and has been used to register someone.";
    }

    // Check if email already exists
    $sql_check_email = "SELECT id FROM users WHERE email = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();
    if ($stmt_check_email->num_rows > 0) {
        $email_err = "Email already exists and has been used to register someone.";
    }

    // If both username and email are unique, proceed with registration
    if (empty($username_err) && empty($email_err)) {
        // Insert new user into database
        $sql_insert_user = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)";
        $stmt_insert_user = $conn->prepare($sql_insert_user);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_insert_user->bind_param("ssss", $username, $hashed_password, $email, $role);
        if ($stmt_insert_user->execute()) {
            $registration_success = true;
            // Set success message
            $success_message = "Registration successful. You can now <a href='";
            if ($role === 'admin') {
                $success_message .= "login_admin.php'>login as an admin</a>.";
            } else {
                $success_message .= "login_user.php'>login as a user</a>.";
            }
        } else {
            $registration_err = "Something went wrong. Please try again later.";
        }
    }

    // Close statements and database connection
    $stmt_check_username->close();
    $stmt_check_email->close();
    // Check if $stmt_insert_user is set before closing
    if (isset($stmt_insert_user)) {
        $stmt_insert_user->close();
    }
    $conn->close();
}
?>

<?php
// Include header
include_once 'header.php';
?>

<!-- HTML form for user registration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include necessary meta tags, CSS links, etc. -->
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Registration</h2>
        <?php if(isset($registration_success) && $registration_success): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($registration_err)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $registration_err; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($username_err) || !empty($email_err)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $username_err . "<br>" . $email_err; ?>
            </div>
        <?php endif; ?>
        <form id="registration-form" class="mt-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $username; ?>" class="form-control" required>
                <span class="text-danger"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="<?php echo $password; ?>" class="form-control" required>
                <span class="text-danger"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" value="<?php echo $confirm_password; ?>" class="form-control" required>
                <span class="text-danger"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" class="form-control" required>
                <span class="text-danger"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="" selected disabled>Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <span class="text-danger"><?php echo $role_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Register" class="btn btn-primary">
            </div>
        </form>
        <p>Already have an account? <a href="index.php">Login here</a></p>
    </div>

    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Handle registration form submission
            $('#registration-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'register.php',
                    data: formData,
                    success: function(response) {
                        // Reload the page after successful registration
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        // Display error messages in the form
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
// Include footer
include_once 'footer.php';
?>
