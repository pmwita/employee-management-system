<?php
// Database configuration
$servername = "localhost";
$username = "id21986686_root";
$password = "y'j&W5L[73]R`~cJ";
$database = "id21986686_employee_management1";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
