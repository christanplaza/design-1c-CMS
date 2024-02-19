<?php
require '../../config/db.php'; // Include your PDO connection

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_NUMBER_INT);
    $student_number = filter_input(INPUT_POST, 'editStudentNumber', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $first_name = filter_input(INPUT_POST, 'editStudentFirstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $last_name = filter_input(INPUT_POST, 'editStudentLastName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone_number = filter_input(INPUT_POST, 'editStudentPhoneNumber', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $course = filter_input(INPUT_POST, 'editStudentCourse', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        // Update class details in the database
        $stmt = $pdo->prepare('UPDATE students SET student_number = ?, course = ?, first_name = ?, last_name = ?, phone_number = ? WHERE id = ?');
        $stmt->execute([$student_number, $course, $first_name, $last_name, $phone_number, $student_id]);

        $_SESSION['success_message'] = 'Student updated successfully';
    } catch (PDOException $e) {
        // Handle database error
        $_SESSION['error_message'] = 'Error updating Student';
    }
    
    // Redirect to the form page
    header('Location: ../students.php');
    exit();
} else {
    // Invalid request method
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}
