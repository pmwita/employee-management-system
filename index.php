<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_role'])) {
    // Redirect based on user role
    if ($_SESSION['user_role'] === 'Admin') {
        header("Location: AdminDashboard.php");
        exit();
    } elseif ($_SESSION['user_role'] === 'User') {
        header("Location: NonAdminDashboard.php");
        exit();
    }
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
        <h2>Login</h2>
        <div class="row">
            <div class="col-md-6">
                <a href="login_admin.php" class="btn btn-primary btn-block">Admin Login</a>
            </div>
            <div class="col-md-6">
                <a href="login_user.php" class="btn btn-secondary btn-block">User Login</a>
            </div>
        </div>
        <p>Forgot your password? <a href="reset_password.php">Reset Password</a></p>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

</body>
</html>
<?php
// Include footer
include_once 'footer.php';
?>
