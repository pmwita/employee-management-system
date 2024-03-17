<?php
// Start session
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

// Include database connection
require_once 'db_connection.php';

// Include header
include_once 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Management (Non-Admin)</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Link to your CSS stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Payroll Management (Non-Admin)</h1>

        <!-- Table to display salary records -->
        <h2>Your Salary Details:</h2>
        <table class="table">
            <!-- Table headers -->
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Basic Pay</th>
                    <th>Allowances</th>
                    <th>Deductions</th>
                    <th>Overtime Hours</th>
                    <th>Bonuses</th>
                    <th>Commissions</th>
                    <th>Calculated Salary</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <!-- PHP code to fetch and display salary records -->
                <?php
                // Fetch salary records for the logged-in user
                $sql = "SELECT * FROM salary_records WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['basic_pay'] . "</td>";
                        echo "<td>" . $row['allowances'] . "</td>";
                        echo "<td>" . $row['deductions'] . "</td>";
                        echo "<td>" . $row['overtime_hours'] . "</td>";
                        echo "<td>" . $row['bonuses'] . "</td>";
                        echo "<td>" . $row['commissions'] . "</td>";
                        echo "<td>" . $row['calculated_salary'] . "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Button to print the table details -->
        <button class="btn btn-primary" onclick="window.print()">Print Salary Details</button>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>

<?php
// Include footer
include_once 'footer.php';
?>