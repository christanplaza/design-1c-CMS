<?php
// Include config.php
require_once '../../config/config.php';
require_once '../../config/db.php';

// Connect to Redis server
$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);

// If authentication is required
$redis->auth(REDIS_PASSWORD);

// Retrieve the class_id from the request
$classId = $_GET['class_id'];

// Get today's date
$todayDate = date('Y-m-d');

// Retrieve the attendance logs from Redis
$logEntries = $redis->lrange('attendance:logs', 0, -1);

$latestRecords = [];

foreach ($logEntries as $logEntry) {
    $parts = explode('%', $logEntry);
    $firstName = $parts[0];
    $lastName = $parts[1];
    $studentNumber = $parts[4];
    $timestamp = end($parts);

    // Update the latest record for each student
    if (!isset($latestRecords[$studentNumber]) || strtotime($timestamp) > strtotime($latestRecords[$studentNumber]['timestamp'])) {
        $latestRecords[$studentNumber] = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'timestamp' => $timestamp
        ];
    }
}

foreach ($latestRecords as $studentNumber => $record) {
  $firstName = $record['first_name'];
  $lastName = $record['last_name'];
  $timestamp = $record['timestamp'];

  // Check if the record already exists for the same class_id, student_number, and date
  $stmt = $pdo->prepare("SELECT * FROM attendance_table WHERE class_id = ? AND DATE(first_detected) = ? AND student_number = ?");
  $stmt->execute([$classId, $todayDate, $studentNumber]);
  $result = $stmt->fetch();

  if ($result) {
      // Update the last_detected timestamp only if it's greater than the existing value
      $existingLastDetected = strtotime($result['last_detected']);
      $newLastDetected = strtotime($timestamp);
      if ($newLastDetected > $existingLastDetected) {
          $stmt = $pdo->prepare("UPDATE attendance_table SET last_detected = ? WHERE id = ?");
          $stmt->execute([$timestamp, $result['id']]);
      }
  } else {
      // Insert new record with class_id, student_number, and timestamps
      $stmt = $pdo->prepare("INSERT INTO attendance_table (class_id, student_number, first_detected, last_detected) VALUES (?, ?, ?, ?)");
      $stmt->execute([$classId, $studentNumber, $timestamp, $timestamp]);
  }
}

// Retrieve attendance records for the given class_id and today's date from the attendance_table
$stmt = $pdo->prepare("
    SELECT a.*, s.first_name, s.last_name
    FROM attendance_table a
    JOIN students s ON a.student_number = s.student_number
    WHERE a.class_id = ? AND DATE(a.first_detected) = ?
");
$stmt->execute([$classId, $todayDate]);
$attendanceRecords = $stmt->fetchAll();

$filteredLogs = [];

foreach ($attendanceRecords as $record) {
    $studentNumber = $record['student_number'];
    $firstName = $record['first_name'];
    $lastName = $record['last_name'];

    $filteredLogs[] = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'student_number' => $studentNumber,
        'first_seen' => $record['first_detected'],
        'last_seen' => $record['last_detected']
    ];
}

// Return the filtered logs as JSON response
header('Content-Type: application/json');
echo json_encode($filteredLogs);
?>