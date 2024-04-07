<?php
// Database connection details
$host = 'localhost';
$port = '3307';
$username = 'root';
$password = '';
$database = 'design-1c-class_management';

// Retrieve the class_id, student_number, first_detected, and last_detected from the request
$classId = $_POST['class_id'];
$studentNumber = $_POST['student_number'];
$firstDetected = $_POST['first_detected'];
$lastDetected = $_POST['last_detected'];

// Create a new MySQLi instance
$mysqli = new mysqli($host, $username, $password, $database, $port);

// Check the connection
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Get today's date
$todayDate = date('Y-m-d');

// Check if the record already exists for the same class_id and date
$stmt = $mysqli->prepare("SELECT * FROM attendance_table WHERE class_id = ? AND DATE(first_detected) = ? AND student_number = ?");
$stmt->bind_param('sss', $classId, $todayDate, $studentNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update the last_detected timestamp
    $stmt = $mysqli->prepare("UPDATE attendance_table SET last_detected = ? WHERE class_id = ? AND DATE(first_detected) = ? AND student_number = ?");
    $stmt->bind_param('ssss', $lastDetected, $classId, $todayDate, $studentNumber);
} else {
    // Insert new record with class_id, student_number, and timestamps
    $stmt = $mysqli->prepare("INSERT INTO attendance_table (class_id, student_number, first_detected, last_detected) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $classId, $studentNumber, $firstDetected, $lastDetected);
}

$stmt->execute();
$stmt->close();
$mysqli->close();

// Return a success response
echo json_encode(['success' => true]);
?>