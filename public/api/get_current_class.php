<?php
// Include your database connection file (e.g., db.php)
include '../../config/db.php';

// Get the current day of the week and time
$currentDayOfWeek = date('l');
$currentTime = date('H:i:s');

try {
    // Prepare the query to retrieve ongoing classes
    $stmt = $pdo->prepare("
        SELECT 
            c.title,
            c.course,
            s.location,
            s.start_time,
            s.end_time,
            t.name AS teacher_name,
            c.id
        FROM classes c
        JOIN teachers t ON c.teacher_id = t.id
        JOIN schedules s ON c.id = s.class_id
        WHERE 
            s.day_of_week = ?
            AND s.start_time <= ?
            AND s.end_time >= ?
    ");

    // Execute the query with the current day of the week and time
    $stmt->execute([$currentDayOfWeek, $currentTime, $currentTime]);

    // Fetch all ongoing classes
    $ongoingClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return ongoing classes in JSON format
    header('Content-Type: application/json');
    echo json_encode($ongoingClasses);
    exit;

} catch (PDOException $e) {
    // Handle database error
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
}
?>