<!-- This code includes: -->

<!-- Table to display salary details for the logged-in non-admin user. -->
<!-- PHP code to fetch and display the user's salary records from the database. -->
<!-- Print button to allow the user to print their salary details. -->
<!-- Proper database connection handling. -->
<!-- Usage of Bootstrap for styling. -->

<?php
// Start session
session_start();

// Check if the user is not logged in or is an admin, redirect to index.php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit();
}

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
    <style>
        /* Add your custom CSS styles here */
    </style>
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
                // Include database connection
                require_once 'db_connection.php';

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
