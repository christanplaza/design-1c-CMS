<?php
require '../../config/db.php'; // Include your PDO connection

session_start();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form data
    $name = filter_input(INPUT_POST, 'teacher_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'teacher_email', FILTER_SANITIZE_EMAIL);

    // Insert data into the Classes table
    $sql = "INSERT INTO teachers (name, email_address) VALUES (:name, :email)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Set success message in session
        $_SESSION['success_message'] = "Teacher created successfully!";
    } catch (PDOException $e) {
        // Set error message in session
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Redirect to the form page
header('Location: ../teachers.php');
exit();
?>
