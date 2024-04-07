<?php
// Include config.php
require_once '../../config/config.php';
require_once '../../config/db.php';

// Connect to Redis server
$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);

// If authentication is required
$redis->auth(REDIS_PASSWORD);

// Set the time threshold for the last 5 minutes
$thresholdTime = 5; // Minutes

// Get the current timestamp
$currentTime = time();

// Retrieve attendance logs from Redis
$logEntries = $redis->lrange('attendance:logs', 0, -1);

$filteredLogs = [];

foreach ($logEntries as $logEntry) {
    $parts = explode('%', $logEntry);
    $firstName = $parts[0];
    $lastName = $parts[1];
    $studentNumber = $parts[4];
    $timestamp = strtotime(end($parts));

    if (($currentTime - $timestamp) <= ($thresholdTime * 60)) {
        $filteredLogs[$studentNumber] = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'student_number' => $studentNumber,
            'timestamp' => date('Y-m-d H:i:s', $timestamp)
        ];
    }
}

// Return the filtered logs as JSON response
header('Content-Type: application/json');
echo json_encode(array_values($filteredLogs));
?>