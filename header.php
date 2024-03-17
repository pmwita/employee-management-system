<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if the user is an admin
function isAdmin() {
    // You would need to replace this with your actual logic to check if the user is an admin
    // For demonstration purposes, let's assume admin user_id is 1 and role 'admin'
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1 && isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Redirect logic for 'Dashboard' link
$dashboardLink = isset($_SESSION['user_id']) ? (isAdmin() ? 'AdminDashboard.php' : 'NonAdminDashboard.php') : 'index.php';

// Redirect logic for 'Payroll Management' link
$payrollLink = isset($_SESSION['user_id']) ? (isAdmin() ? 'Payroll_management_admin.php' : 'Payroll_management_non_admin.php') : 'index.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to your CSS stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="bg-dark">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="#"><img src="logo.png" alt="Company Logo"></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $dashboardLink; ?>">Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <!-- Logic to determine the URL for Payroll Management based on user role -->
                            <a class="nav-link" href="<?php echo $payrollLink; ?>">Payroll Management</a>
                        </li>

                        <li class="nav-item">
                            <!-- Logic to determine the URL for Leave Management based on user role -->
                            <?php if (isAdmin()): ?>
                                <a class="nav-link" href="leave_management_admin.php">Leave Management</a>
                            <?php else: ?>
                                <a class="nav-link" href="leave_management_non_admin.php">Leave Management</a>
                            <?php endif; ?>
                        </li>

                        <li class="nav-item">
                            <!-- Logic to determine the URL for Training and Performance Appraisal based on user role -->
                            <?php if (isAdmin()): ?>
                                <a class="nav-link" href="training_performance_admin.php">Training and Performance Appraisal</a>
                            <?php else: ?>
                                <a class="nav-link" href="training_performance_non_admin.php">Training and Performance Appraisal</a>
                            <?php endif; ?>
                        </li>

                        <li class="nav-item">
                            <!-- Logic to determine the URL for resignation based on user role -->
                            <?php if (isAdmin()): ?>
                                <a class="nav-link" href="resignation_admin.php">Resignation</a>
                            <?php else: ?>
                                <a class="nav-link" href="resignation_non_admin.php"> Resignation</a>
                            <?php endif; ?>
                        </li>

                        <!-- Logout link (visible if user is logged in) -->
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">Logout</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <!-- Link to Bootstrap JS and jQuery (place these before the closing body tag) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
