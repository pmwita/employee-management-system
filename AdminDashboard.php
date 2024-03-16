<?php
// Start session
session_start();

// Check if the user is not logged in, redirect to index.php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Function to display the dashboard form for admin users
function displayAdminDashboard() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <!-- Link to Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <!-- Link to your CSS stylesheet -->
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php include_once 'header.php'; ?>
        <h2>Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>!</h2>
        <h3>Admin Dashboard</h3>
        <div class="dashboard-container">
            <!-- Display admin dashboard elements -->
            <div class="dashboard-item">
                <h2>Payroll Management</h2>
                <p>Manage employee salaries and payments.</p>
                <!-- <a href="payroll_management.php">Go to Payroll</a> -->
                <a href="Payroll_management_admin.php">Go to Payroll</a>
            </div>

            <div class="dashboard-item">
                <h2>Leave Management</h2>
                <p>Handle employee leave requests and approvals.</p>
                <a href="leave_management_admin.php">Go to Leave Management</a>
            </div>

            <div class="dashboard-item">
                <h2>Training and Performance Appraisal</h2>
                <p>Manage employee training sessions and performance reviews.</p>
                <a href="training_performance_admin.php">Go to Training & Performance</a>
            </div>

            <div class="dashboard-item">
                <h2>Resignation</h2>
                <p>Handle employee resignations.</p>
                <a href="resignation_admin.php">Go to Resignation</a>
            </div>
        </div>
        <?php include_once 'footer.php'; ?>
    </body>
    </html>
    <?php
}

displayAdminDashboard();
?>
