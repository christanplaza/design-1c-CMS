<?php
// Include your database connection file (e.g., db.php)
include '../../config/db.php';

// Check if the student_id is provided via POST
if (isset($_POST['student_id'])) {
    // Sanitize the input
    $studentId = filter_var($_POST['student_id'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Prepare and execute the query to retrieve class details
        $stmt = $pdo->prepare('SELECT id, student_number, first_name, last_name FROM students WHERE id = ?');
        $stmt->execute([$studentId]);

        // Fetch the class details
        $studentDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the class was found
        if ($studentDetails) {
            // Return class details in JSON format
            header('Content-Type: application/json');
            echo json_encode($studentDetails);
            exit;
        } else {
            // Class not found
            http_response_code(404);
            echo json_encode(['error' => 'Student not found']);
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
