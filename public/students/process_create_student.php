<?php
require '../../config/db.php'; // Include your PDO connection

session_start();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form data
    $student_number = filter_input(INPUT_POST, 'student_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $course = filter_input(INPUT_POST, 'course', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Insert data into the Classes table
    $sql = "INSERT INTO students (student_number, course, first_name, last_name, phone_number) VALUES (:student_number, :course, :first_name, :last_name, :phone_number)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':student_number', $student_number, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
        $stmt->execute();

        // Set success message in session
        $_SESSION['success_message'] = "Student created successfully!";
    } catch (PDOException $e) {
        // Set error message in session
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Redirect to the form page
header('Location: ../students.php');
exit();
?>
