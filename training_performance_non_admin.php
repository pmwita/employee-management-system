<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_date = $_POST['training_date'];
    $training_description = $_POST['training_description'];
    $performance_review_date = $_POST['performance_review_date'];
    $performance_review_notes = $_POST['performance_review_notes'];
    $training_school = $_POST['training_school'];
    
    // Insert training request details into the database
    $sql_insert = "INSERT INTO training_performance (employee_id, training_date, training_description, performance_review_date, performance_review_notes, training_school, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
    
    if ($stmt_insert = $conn->prepare($sql_insert)) {
        $stmt_insert->bind_param("isssss", $_SESSION['user_id'], $training_date, $training_description, $performance_review_date, $performance_review_notes, $training_school);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
}

$sql_fetch = "SELECT * FROM training_performance WHERE employee_id = ?";
if ($stmt_fetch = $conn->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $_SESSION['user_id']);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    $stmt_fetch->close();
}

include_once 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training and Performance Appraisal (Non-Admin)</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Training and Performance Appraisal (Non-Admin)</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="training_date">Training Date:</label>
                <input type="date" class="form-control" id="training_date" name="training_date" required>
            </div>
            <div class="form-group">
                <label for="training_description">Training Description:</label>
                <textarea class="form-control" id="training_description" name="training_description" required></textarea>
            </div>
            <div class="form-group">
                <label for="training_school">Training School:</label>
                <input type="text" class="form-control" id="training_school" name="training_school" required>
            </div>
            <div class="form-group">
                <label for="performance_review_date">Performance Review Date:</label>
                <input type="date" class="form-control" id="performance_review_date" name="performance_review_date" required>
            </div>
            <div class="form-group">
                <label for="performance_review_notes">Performance Review Notes:</label>
                <textarea class="form-control" id="performance_review_notes" name="performance_review_notes"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Training Request</button>
        </form>
        
        <h3>Training Requests</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Training Date</th>
                    <th>Training Description</th>
                    <th>Training School</th>
                    <th>Performance Review Date</th>
                    <th>Performance Review Notes</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['training_date']; ?></td>
                        <td><?php echo $row['training_description']; ?></td>
                        <td><?php echo $row['training_school']; ?></td>
                        <td><?php echo $row['performance_review_date']; ?></td>
                        <td><?php echo $row['performance_review_notes']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
include_once 'footer.php';
?>
