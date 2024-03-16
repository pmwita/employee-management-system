<?php
// Start the session
session_start();

// Check if user is logged in, redirect to login page if not
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include header
include_once 'header.php';

// Include database connection
require_once 'db_connection.php';

// Function to update leave request status
function updateLeaveStatus($conn, $requestId, $status) {
    $sql = "UPDATE leave_requests SET status = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $status, $requestId);
        $stmt->execute();
        $stmt->close();
        return true;
    }
    return false;
}

// Process leave request approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve']) || isset($_POST['reject'])) {
        $requestId = isset($_POST['approve']) ? $_POST['approve'] : $_POST['reject'];
        $status = isset($_POST['approve']) ? 'Approved' : 'Rejected';
        if (updateLeaveStatus($conn, $requestId, $status)) {
            // Redirect to prevent form resubmission
            header("Location: leave_management_admin.php");
            exit();
        } else {
            echo "Failed to update leave request status.";
        }
    }
}

// Fetch leave requests for admin users after updating
if ($_SESSION['role'] === 'admin') {
    $sql = "SELECT * FROM leave_requests";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management</title>
    <!-- Include necessary CSS links -->
    <link rel="stylesheet" href="style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-approve {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-reject {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Leave Management</h2>

        <!-- Leave request form for admin users -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop through leave requests if result is set -->
                    <?php if (isset($result)): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['employee_id']; ?></td>
                            <td><?php echo $row['start_date']; ?></td>
                            <td><?php echo $row['end_date']; ?></td>
                            <td><?php echo $row['reason']; ?></td>
                            <td>
                                <?php if ($row['status'] === 'Pending'): ?>
                                    <button type="submit" name="approve" value="<?php echo $row['id']; ?>" class="btn btn-success btn-approve">Approve</button>
                                    <button type="submit" name="reject" value="<?php echo $row['id']; ?>" class="btn btn-danger btn-reject">Reject</button>
                                <?php elseif ($row['status'] === 'Approved'): ?>
                                    <span class="text-success">Approved</span>
                                    <button type="submit" name="reject" value="<?php echo $row['id']; ?>" class="btn btn-danger btn-reject">Reject</button>
                                <?php elseif ($row['status'] === 'Rejected'): ?>
                                    <span class="text-danger">Rejected</span>
                                    <button type="submit" name="approve" value="<?php echo $row['id']; ?>" class="btn btn-success btn-approve">Approve</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();

// Include footer
include_once 'footer.php';
?>
