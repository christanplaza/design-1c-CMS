<?php
// Include your database connection file (e.g., db.php)
include '../../config/db.php';

// Check if the class_id is provided via POST
if (isset($_POST['schedule_id'])) {
    // Sanitize the input
    $schedule_id = filter_var($_POST['schedule_id'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Prepare and execute the query to retrieve class details
        $stmt = $pdo->prepare('SELECT id, class_id, day_of_week, start_time, end_time, location FROM schedules WHERE id = ?');
        $stmt->execute([$schedule_id]);

        // Fetch the class details
        $scheduleDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the class was found
        if ($scheduleDetails) {
            // Return class details in JSON format
            header('Content-Type: application/json');
            echo json_encode($scheduleDetails);
            exit;
        } else {
            // Class not found
            http_response_code(404);
            echo json_encode(['error' => 'Schedule not found']);
            exit;
        }
    } catch (PDOException $e) {
        // Handle database error
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
        exit;
    }
} else {
    // Invalid request
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}
?>
