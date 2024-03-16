<?php
// Start session
session_start();

// Include database connection
require_once 'db_connection.php';

// Check if the user is not logged in, redirect to index.php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include header
include_once 'header.php';

// Initialize variables with empty values
$start_date = $end_date = $reason = "";
$start_date_err = $end_date_err = $reason_err = "";

// Process form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    // Validate start date, end date, and reason
    // Your validation code here...

    // Check input errors before inserting into database
    if (empty($start_date_err) && empty($end_date_err) && empty($reason_err)) {
        // Prepare and execute SQL insert statement to add leave request
        $sql = "INSERT INTO leave_requests (employee_id, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("issss", $_SESSION['user_id'], $start_date, $end_date, $reason, $status);

            // Set parameters and execute statement
            $status = 'Pending';
            if ($stmt->execute()) {
                // Redirect to leave management page
                header("Location: leave_management_non_admin.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
}

// Display form to submit leave request
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management</title>
    <!-- Include necessary CSS links -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Leave Management</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="mt-3">
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
                <span class="text-danger"><?php echo $start_date_err; ?></span>
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
                <span class="text-danger"><?php echo $end_date_err; ?></span>
            </div>
            <div class="form-group">
                <label for="reason">Reason:</label>
                <textarea class="form-control" id="reason" name="reason" required><?php echo $reason; ?></textarea>
                <span class="text-danger"><?php echo $reason_err; ?></span>
            </div>
            <button type="submit" class="btn btn-primary">Submit Leave Request</button>
        </form>
    </div>
</body>
</html>
<?php

// Fetch and display leave requests submitted by the non-admin user
$sql = "SELECT id, start_date, end_date, reason, status FROM leave_requests WHERE employee_id = ?";
if ($stmt = $conn->prepare($sql)) {
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("i", $_SESSION['user_id']);

    // Execute statement
    $stmt->execute();

    // Store result
    $result = $stmt->get_result();

    // Check if there are any leave requests
    if ($result->num_rows > 0) {
        // Display table header
        echo "<div class='container mt-5'>";
        echo "<h2>Leave Requests</h2>";
        echo "<table class='table'>";
        echo "<thead class='thead-dark'>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Start Date</th>";
        echo "<th>End Date</th>";
        echo "<th>Reason</th>";
        echo "<th>Status</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['start_date'] . "</td>";
            echo "<td>" . $row['end_date'] . "</td>";
            echo "<td>" . $row['reason'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        // If no leave requests found
        echo "<div class='container mt-5'>";
        echo "<p>No leave requests found.</p>";
        echo "</div>";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();

// Include footer
include_once 'footer.php';
?>
