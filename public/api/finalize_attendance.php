<?php
// Include config.php
require_once '../../config/config.php';
require_once '../../config/db.php';

// Retrieve the class_id from the request
$classId = $_GET['class_id'];

// Get today's date and day of the week
$todayDate = date('Y-m-d');
$dayOfWeek = date('N'); // 1 (Monday) to 7 (Sunday)
$days = array('', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

// Retrieve the class schedule for today
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE class_id = ? AND day_of_week = ?");
$stmt->execute([$classId, $days[$dayOfWeek]]);
$classSchedule = $stmt->fetch();

if (!$classSchedule) {
    // No class schedule found for today
    echo json_encode(['error' => 'No class schedule found for today']);
    exit();
}

// Get the class start time and end time
$classStartTime = strtotime($classSchedule['start_time']);
$classEndTime = strtotime($classSchedule['end_time']);

// Calculate the late threshold (15 minutes after class start time)
$lateThreshold = $classStartTime + (5 * 60);

// Retrieve the class details
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$classId]);
$class = $stmt->fetch();

// Retrieve all students enrolled in the class based on the course
$stmt = $pdo->prepare("SELECT * FROM students WHERE course = ?");
$stmt->execute([$class['course']]);
$enrolledStudents = $stmt->fetchAll();

$attendanceData = [];

foreach ($enrolledStudents as $student) {
    $studentNumber = $student['student_number'];
    $firstName = $student['first_name'];
    $lastName = $student['last_name'];

    // Check if the student has an attendance record for today
    $stmt = $pdo->prepare("SELECT * FROM attendance_table WHERE class_id = ? AND DATE(first_detected) = ? AND student_number = ?");
    $stmt->execute([$classId, $todayDate, $studentNumber]);
    $attendanceRecord = $stmt->fetch();

    if ($attendanceRecord) {
        $firstSeenTimestamp = strtotime($attendanceRecord['first_detected']);

        if ($firstSeenTimestamp <= $lateThreshold) {
            $status = 'Present';
        } else {
            $status = 'Late';
        }
    } else {
        $status = 'Absent';
    }

    $attendanceData[] = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'student_number' => $studentNumber,
        'status' => $status
    ];
}

// Return the attendance data as JSON response
header('Content-Type: application/json');
echo json_encode($attendanceData);
?>