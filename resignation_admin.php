<?php
// Start the session
session_start();

// Check if admin is logged in, redirect to login page if not
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include database connection
require_once 'db_connection.php';

// Include header
include_once 'header.php';

// Handle form submission for approving or rejecting resignations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resignation_id']) && isset($_POST['action'])) {
        $resignation_id = $_POST['resignation_id'];
        $action = $_POST['action'];

        // Update the status based on the action
        $status = ($action == 'approve') ? 'resignation approved' : 'rejected';
        
        // Prepare and execute the update statement
        $sql = "UPDATE resignations SET status = ? WHERE resignation_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $resignation_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resignation Requests</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to your CSS stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Resignation Requests</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Resignation Date</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Retrieve resignation requests
                $sql = "SELECT resignation_id, username, resignation_date, reason, status FROM resignations INNER JOIN users ON resignations.employee_id = users.id WHERE users.role = 'user'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['resignation_date'] . "</td>";
                        echo "<td>" . $row['reason'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>";
                        echo "<form action='' method='post'>";
                        echo "<input type='hidden' name='resignation_id' value='" . $row['resignation_id'] . "'>";
                        echo "<button type='submit' class='btn btn-success' name='action' value='approve'>Approve</button>";
                        echo "<button type='submit' class='btn btn-danger' name='action' value='reject'>Reject</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No resignation requests found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
// Include footer
include_once 'footer.php';
?>
