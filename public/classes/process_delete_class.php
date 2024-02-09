<?php
// Include your database connection file (e.g., db.php)
require '../../config/db.php'; // Include your PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the class ID from the request body
    $classId = file_get_contents('php://input');
    $classId = filter_var($classId, FILTER_SANITIZE_NUMBER_INT);

    try {
        // Delete the class from the database
        $stmt = $pdo->prepare('DELETE FROM classes WHERE id = ?');
        $stmt->execute([$classId]);

        // Optionally, you can set a success message in the session
        session_start();
        $_SESSION['success_message'] = 'Class deleted successfully';

        // Respond with a success message (optional)
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    } catch (PDOException $e) {
        // Handle database error
        // Optionally, you can set an error message in the session
        session_start();
        $_SESSION['error_message'] = 'Error deleting class';

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
