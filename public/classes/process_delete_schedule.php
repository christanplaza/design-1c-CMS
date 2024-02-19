<?php
// Include your database connection file (e.g., db.php)
require '../../config/db.php'; // Include your PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the schedule ID from the request body
    $scheduleId = file_get_contents('php://input');
    $scheduleId = filter_var($scheduleId, FILTER_SANITIZE_NUMBER_INT);

    try {
        // Delete the schedule from the database
        $stmt = $pdo->prepare('DELETE FROM schedules WHERE id = ?');
        $stmt->execute([$scheduleId]);

        // Optionally, you can set a success message in the session
        session_start();
        $_SESSION['success_message'] = 'Schedule deleted successfully';

        // Respond with a success message (optional)
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    } catch (PDOException $e) {
        // Handle database error
        // Optionally, you can set an error message in the session
        session_start();
        $_SESSION['error_message'] = 'Error deleting schedule';

        // Respond with an error message (optional)
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database error']);
        exit;
    }
} else {
    // Invalid request method
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}
