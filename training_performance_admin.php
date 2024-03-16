<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $training_id = $_POST['training_id'];
        $sql_approve = "UPDATE training_performance SET status = 'Approved' WHERE id = ?";
        if ($stmt_approve = $conn->prepare($sql_approve)) {
            $stmt_approve->bind_param("i", $training_id);
            $stmt_approve->execute();
            $stmt_approve->close();
        }
    } elseif (isset($_POST['reject'])) {
        $training_id = $_POST['training_id'];
        $sql_reject = "UPDATE training_performance SET status = 'Rejected' WHERE id = ?";
        if ($stmt_reject = $conn->prepare($sql_reject)) {
            $stmt_reject->bind_param("i", $training_id);
            $stmt_reject->execute();
            $stmt_reject->close();
        }
    }
}

$sql_fetch = "SELECT * FROM training_performance";
$result = $conn->query($sql_fetch);

include_once 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training and Performance Appraisal (Admin)</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Training and Performance Appraisal (Admin)</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Training Date</th>
                    <th>Training Description</th>
                    <th>Performance Review Date</th>
                    <th>Performance Review Notes</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['training_date']; ?></td>
                        <td><?php echo $row['training_description']; ?></td>
                        <td><?php echo $row['performance_review_date']; ?></td>
                        <td><?php echo $row['performance_review_notes']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <input type="hidden" name="training_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="approve" class="btn btn-success">Approve</button>
                                <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
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
