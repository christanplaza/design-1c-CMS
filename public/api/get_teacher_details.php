<?php
// Include your database connection file (e.g., db.php)
include '../../config/db.php';

// Check if the teacher_id is provided via POST
if (isset($_POST['teacher_id'])) {
    // Sanitize the input
    $teacherId = filter_var($_POST['teacher_id'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Prepare and execute the query to retrieve teacher details
        $stmt = $pdo->prepare('SELECT id, name, email_address, status FROM teachers WHERE id = ?');
        $stmt->execute([$teacherId]);

        // Fetch the teacher details
        $teacherDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the teacher was found
        if ($teacherDetails) {
            // Return teacher details in JSON format
            header('Content-Type: application/json');
            echo json_encode($teacherDetails);
            exit;
        } else {
            // teacher not found
            http_response_code(404);
            echo json_encode(['error' => 'Teacher not found']);
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
