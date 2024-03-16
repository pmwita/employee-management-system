<?php
// Start the session
session_start();

// Check if user is logged in, redirect to login page if not
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include database connection
require_once 'db_connection.php';

// If the username is not set in the session, retrieve and store it
if (!isset($_SESSION['username'])) {
    // Prepare a SELECT statement to retrieve the username
    $sql = "SELECT username FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("i", $_SESSION['user_id']);
        // Execute the statement
        if ($stmt->execute()) {
            // Store the result
            $stmt->store_result();
            // Bind the result variables
            $stmt->bind_result($username);
            // Fetch the result
            if ($stmt->fetch()) {
                // Store the username in the session
                $_SESSION['username'] = $username;
            }
        }
        // Close the statement
        $stmt->close();
    }
}

// Define variables and initialize with empty values
$resignation_date = $reason = "";
$resignation_date_err = $reason_err = "";

// Process form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate resignation date
    if (empty(trim($_POST['resignation_date']))) {
        $resignation_date_err = "Please enter the resignation date.";
    } else {
        $resignation_date = trim($_POST['resignation_date']);
    }

    // Validate reason
    if (empty(trim($_POST['reason']))) {
        $reason_err = "Please enter the reason for resignation.";
    } else {
        $reason = trim($_POST['reason']);
    }

    // Check input errors before inserting into database
    if (empty($resignation_date_err) && empty($reason_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO resignations (employee_id, resignation_date, reason, status) VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("isss", $param_employee_id, $param_resignation_date, $param_reason, $param_status);

            // Set parameters
            $param_employee_id = $_SESSION['user_id'];
            $param_resignation_date = $resignation_date;
            $param_reason = $reason;
            $param_status = 'pending';

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to resignation page
                header("location: resignation_non_admin.php");
                exit(); // Add exit to prevent further execution
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
}

// Include header
include_once 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resignation</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to your CSS stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Resignation</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="resignation_date">Resignation Date:</label>
                <input type="date" class="form-control" id="resignation_date" name="resignation_date" value="<?php echo $resignation_date; ?>" required>
                <span class="text-danger"><?php echo $resignation_date_err; ?></span>
            </div>
            <div class="form-group">
                <label for="reason">Reason:</label>
                <textarea class="form-control" id="reason" name="reason" required><?php echo $reason; ?></textarea>
                <span class="text-danger"><?php echo $reason_err; ?></span>
            </div>
            <input type="hidden" name="user" value="<?php echo $_SESSION['username']; ?>">
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- Display Inserted Resignation Details -->
        <h2>Resignation Details</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Resignation Date</th>
                    <th>Reason</th>
                    <th>User</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Retrieve inserted resignation details
                $sql = "SELECT resignation_date, reason, status FROM resignations WHERE employee_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("i", $_SESSION['user_id']);

                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        // Store result
                        $stmt->store_result();

                        // Check if any resignation records exist
                        if ($stmt->num_rows > 0) {
                            // Bind result variables
                            $stmt->bind_result($resignation_date, $reason, $status);
                            // Fetch records
                            while ($stmt->fetch()) {
                                echo "<tr>";
                                echo "<td>" . $resignation_date . "</td>";
                                echo "<td>" . $reason . "</td>";
                                echo "<td>" . $_SESSION['username'] . "</td>";
                                echo "<td>" . $status . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No resignation records found.</td></tr>";
                        }
                    } else {
                        echo "Error: Unable to execute SQL statement.";
                    }

                    // Close statement
                    $stmt->close();
                }

                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();

// Include footer
include_once 'footer.php';
?>
