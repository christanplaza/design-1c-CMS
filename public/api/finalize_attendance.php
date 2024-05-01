<?php
// Include config.php
require_once '../../config/config.php';
require_once '../../config/db.php';

// Retrieve the class_id from the request
$classId = $_GET['class_id'];

$stmt = $pdo->prepare("SELECT * FROM schedules WHERE class_id = ?");
$stmt->execute([$classId]);
$schedules = $stmt->fetch();

// Get the start and end times from the $schedules array
$startTime = $schedules['start_time'];
$endTime = $schedules['end_time'];

// Create DateTime objects for the start and end times
$start = DateTime::createFromFormat('H:i:s', $startTime);
$end = DateTime::createFromFormat('H:i:s', $endTime);

// Calculate the duration by subtracting the start time from the end time
$duration = $end->diff($start);

// Get the duration in hours
$hours = $duration->h;

// Set the classThreshold based on the duration
//$minutesLateThreshold = $hours * 15; // Comment Out for Demo
$minutesLateThreshold = 3; // Uncomment for Demo


// Get today's date and day of the week
$todayDate = date('Y-m-d');
$dayOfWeek = date('N'); // 1 (Monday) to 7 (Sunday)
$days = array('', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

// Retrieve the class schedule for today
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE class_id = ? AND day_of_week = ?");
$stmt->execute([$classId, $days[$dayOfWeek]]);
$classSchedule = $stmt->fetch();

$stmt = $pdo->prepare("SELECT title FROM classes WHERE id = ?");
$stmt->execute([$classId]);
$classInfo = $stmt->fetch();

$start_time_formatted = date("h:i A", strtotime($classSchedule['start_time']));
$end_time_formatted = date("h:i A", strtotime($classSchedule['end_time']));

$classScheduleInformation = $classSchedule['day_of_week'] . ": " . $start_time_formatted . "-" . $end_time_formatted;

if (!$classSchedule) {
    // No class schedule found for today
    echo json_encode(['error' => 'No class schedule found for today']);
    exit();
}

// Get the class start time and end time
$classStartTime = strtotime($classSchedule['start_time']);
$classEndTime = strtotime($classSchedule['end_time']);

// Calculate the late threshold
$lateThreshold = $classStartTime + ($minutesLateThreshold * 60);

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
    $phoneNumber = $student['phone_number'];
    $attendanceTimeStamp = '';

    // Check if the student has an attendance record for today
    $stmt = $pdo->prepare("SELECT * FROM attendance_table WHERE class_id = ? AND DATE(first_detected) = ? AND student_number = ?");
    $stmt->execute([$classId, $todayDate, $studentNumber]);
    $attendanceRecord = $stmt->fetch();

    if ($attendanceRecord) {
        $firstSeenTimestamp = strtotime($attendanceRecord['first_detected']);
        $attendanceTimeStamp = date('d-m-Y h:i A', $firstSeenTimestamp);

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
        'phone_number' => $phoneNumber,
        'status' => $status,
        'first_seen' => $attendanceTimeStamp,
    ];
}

// Sort the attendance data by last name in alphabetical order
usort($attendanceData, function($a, $b) {
    return strcmp($a['last_name'], $b['last_name']);
});

$returnData = [
    'class_title' => $classInfo['title'],
    'class_schedule' => $classScheduleInformation,
    'attendance_data' => $attendanceData
];

// Return the attendance data as JSON response
header('Content-Type: application/json');
echo json_encode($returnData);
?>