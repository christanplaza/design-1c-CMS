<?php
require '../../config/db.php'; // Include your PDO connection

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $classId = filter_input(INPUT_POST, 'class_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        // Update class details in the database
        $stmt = $pdo->prepare('UPDATE classes SET title = ?, description = ? WHERE id = ?');
        $stmt->execute([$title, $description, $classId]);

        $_SESSION['success_message'] = 'Class updated successfully';
    } catch (PDOException $e) {
        // Handle database error
        $_SESSION['error_message'] = 'Error updating class';
    }
    
    // Redirect to the form page
    header('Location: ../classes.php');
    exit();
} else {
    // Invalid request method
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}
