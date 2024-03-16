<?php
session_start();

// Check if user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: AdminDashboard.php");
    } else {
        header("Location: NonAdminDashboard.php");
    }
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Perform login process
    // Include database connection
    require_once 'db_connection.php';

    // Retrieve form data
    if(isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Query to check user credentials
        $sql = "SELECT id, role, password FROM users WHERE username = ? AND role = 'user'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // User found, verify password
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];

                // Redirect to non-admin dashboard
                header("Location: NonAdminDashboard.php");
                exit();
            } else {
                $login_err = "Invalid username or password.";
            }
        } else {
            $login_err = "Invalid username or password.";
        }

        // Close statement and database connection
        $stmt->close();
    }
    $conn->close();
}
?>

<?php
// Include header
include_once 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to your CSS stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container mt-5">
        <h2>User Login</h2>
        <?php if(isset($login_err)) echo "<p class='text-danger'>$login_err</p>"; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p>Are you an admin? <a href="index.php">Login here</a></p>
        <p>Forgot your password? <a href="reset_password.php">Reset Password</a></p>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

</body>
</html>
<?php
// Include footer
include_once 'footer.php';
?>
