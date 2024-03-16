<?php
// Start session
session_start();

// Check if the user is not logged in or not an admin, redirect to index.php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Include header
include_once 'header.php';

// Include database connection
require_once 'db_connection.php';

// Function to calculate salary
function calculateSalary($basic_pay, $allowances, $deductions, $bonuses, $commissions) {
    // Convert parameters to float if they are not already numeric
    $basic_pay = is_numeric($basic_pay) ? floatval($basic_pay) : 0;
    $allowances = is_numeric($allowances) ? floatval($allowances) : 0;
    $deductions = is_numeric($deductions) ? floatval($deductions) : 0;
    $bonuses = is_numeric($bonuses) ? floatval($bonuses) : 0;
    $commissions = is_numeric($commissions) ? floatval($commissions) : 0;

    // Calculate salary
    return $basic_pay + $allowances - $deductions + $bonuses + $commissions;
}

// Function to get non-admin users
function getNonAdminUsers($conn) {
    $sql = "SELECT id, username FROM users WHERE role <> 'admin'";
    $result = $conn->query($sql);
    $users = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[$row['id']] = $row['username'];
        }
    }
    return $users;
}

// Initialize user_id variable
$user_id = '';

// Check if form is submitted for insertion or edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['insert'])) {
        // Retrieve form data
        $basic_pay = $_POST['basic_pay'];
        $allowances = $_POST['allowances'];
        $deductions = $_POST['deductions'];
        $bonuses = $_POST['bonuses'];
        $commissions = $_POST['commissions'];
        $user_id = $_POST['user_id'];

        // Calculate salary
        $calculated_salary = calculateSalary($basic_pay, $allowances, $deductions, $bonuses, $commissions);

        // Retrieve username corresponding to selected user_id
        $username_sql = "SELECT username FROM users WHERE id='$user_id'";
        $username_result = $conn->query($username_sql);
        if ($username_result->num_rows > 0) {
            $username_row = $username_result->fetch_assoc();
            $username = $username_row['username'];

            // Insert data into database
            $sql = "INSERT INTO salary_records (basic_pay, allowances, deductions, bonuses, commissions, calculated_salary, user_id, username, created_at) 
                    VALUES ('$basic_pay', '$allowances', '$deductions', '$bonuses', '$commissions', '$calculated_salary', '$user_id', '$username', NOW())";
            if ($conn->query($sql) === TRUE) {
                echo "Record inserted successfully!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Error: Username not found for selected user.";
        }
    }

    // Check if form is submitted for deletion
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM salary_records WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully!";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}

// Prepopulate form fields for editing
if (isset($_POST['edit'])) {
    $edit_id = $_POST['id'];
    $sql = "SELECT * FROM salary_records WHERE id='$edit_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Assign fetched values to variables for prepopulation
        $basic_pay = $row['basic_pay'];
        $allowances = $row['allowances'];
        $deductions = $row['deductions'];
        $bonuses = $row['bonuses'];
        $commissions = $row['commissions'];
        $user_id = $row['user_id'];
        // Get username corresponding to user_id for prepopulation
        $username_sql = "SELECT username FROM users WHERE id='$user_id'";
        $username_result = $conn->query($username_sql);
        if ($username_result->num_rows > 0) {
            $username_row = $username_result->fetch_assoc();
            $username = $username_row['username'];
        }
    }
}

// Get non-admin users
$users = getNonAdminUsers($conn);

// Fetch all salary records
$sql = "SELECT salary_records.*, users.username 
        FROM salary_records 
        INNER JOIN users ON salary_records.user_id = users.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Management (Admin)</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Add your custom CSS styles here */
    </style>
</head>
<body>
    <div class="container">
        <h1>Payroll Management (Admin)</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="basic_pay">Basic Pay:</label>
                <input type="text" class="form-control" id="basic_pay" name="basic_pay" value="<?php echo isset($basic_pay) ? $basic_pay : ''; ?>">
            </div>
            <div class="form-group">
                <label for="allowances">Allowances:</label>
                <input type="text" class="form-control" id="allowances" name="allowances" value="<?php echo isset($allowances) ? $allowances : ''; ?>">
            </div>
            <div class="form-group">
                <label for="deductions">Deductions:</label>
                <input type="text" class="form-control" id="deductions" name="deductions" value="<?php echo isset($deductions) ? $deductions : ''; ?>">
            </div>
            <div class="form-group">
                <label for="bonuses">Bonuses:</label>
                <input type="text" class="form-control" id="bonuses" name="bonuses" value="<?php echo isset($bonuses) ? $bonuses : ''; ?>">
            </div>
            <div class="form-group">
                <label for="commissions">Commissions:</label>
                <input type="text" class="form-control" id="commissions" name="commissions" value="<?php echo isset($commissions) ? $commissions : ''; ?>">
            </div>
            <div class="form-group">
                <label for="user_id">Select User:</label>
                <select class="form-control" id="user_id" name="user_id">
                    <?php
                    foreach ($users as $user_id => $username) {
                        $selected = ($user_id == $selected_user_id) ? 'selected' : '';
                        echo "<option value='$user_id' $selected>$username</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="insert">Insert</button>
            <button type="submit" class="btn btn-primary" name="edit">Edit</button>
        </form>

        <br>
        <h2>Inserted Records</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Basic Pay</th>
                    <th>Allowances</th>
                    <th>Deductions</th>
                    <th>Bonuses</th>
                    <th>Commissions</th>
                    <th>Calculated Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['username']."</td>";
                        echo "<td>".$row['basic_pay']."</td>";
                        echo "<td>".$row['allowances']."</td>";
                        echo "<td>".$row['deductions']."</td>";
                        echo "<td>".$row['bonuses']."</td>";
                        echo "<td>".$row['commissions']."</td>";
                        echo "<td>".$row['calculated_salary']."</td>";
                        echo "<td>
                                <form method='POST' action='".htmlspecialchars($_SERVER["PHP_SELF"])."'>
                                    <input type='hidden' name='id' value='".$row['id']."'>
                                    <button type='submit' class='btn btn-primary' name='edit'>Edit</button>
                                    <button type='submit' class='btn btn-danger' name='delete'>Delete</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
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
