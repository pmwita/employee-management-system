<?php
// Start session
session_start();

// Check if the user is not logged in, redirect to index.php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Function to display the dashboard form for non-admin users
function displayNonAdminDashboard() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Dashboard</title>
        <!-- Link to Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <!-- Link to your CSS stylesheet -->
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php include_once 'header.php'; ?>
        <h2>Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>!</h2>
        <h3>User Dashboard</h3>
        <div class="dashboard-container">
            <!-- Display non-admin dashboard elements -->
            <div class="dashboard-item">
                <h2>View Leave Status</h2>
                <p>Check the status of your leave requests.</p>
                <a href="leave_management_non_admin.php">View Leave Status</a>
            </div>
            
            <div class="dashboard-item">
                <h2>Payroll Management</h2>
                <p>View your payroll details.</p>
                <a href="payroll_management_non_admin.php">View Payroll Details</a>
            </div>

            <div class="dashboard-item">
                <h2>Training and Performance Appraisal</h2>
                <p>View your Training Request Status.</p>
                <a href="training_performance_non_admin.php">View Training Request Status</a>
            </div>

             <div class="dashboard-item">
                <h2>Resignation</h2>
                <p>View resignation status.</p>
                <a href="resignation_non_admin.php">Go to Resignation</a>
            </div>

        </div>
        <?php include_once 'footer.php'; ?>
    </body>
    </html>
    <?php
}

displayNonAdminDashboard();
?>
