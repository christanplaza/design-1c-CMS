<?php
require '../../config/db.php'; // Include your PDO connection

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $teacher_id = filter_input(INPUT_POST, 'teacher_id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'editTeacherName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'editTeacherEmail', FILTER_SANITIZE_EMAIL);
    $status = filter_input(INPUT_POST, 'editTeacherStatus', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        // Update class details in the database
        $stmt = $pdo->prepare('UPDATE teachers SET name = ?, email_address = ?, status = ? WHERE id = ?');
        $stmt->execute([$name, $email, $status, $teacher_id]);

        $_SESSION['success_message'] = 'Teacher updated successfully';
    } catch (PDOException $e) {
        // Handle database error
        $_SESSION['error_message'] = 'Error updating Teacher';
    }
    
    // Redirect to the form page
    header('Location: ../teachers.php');
    exit();
} else {
    // Invalid request method
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}
