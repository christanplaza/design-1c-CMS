<?php
require '../../config/db.php'; // Include your PDO connection

session_start();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form data
    $class_id = filter_input(INPUT_POST, 'class_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $day_of_week = filter_input(INPUT_POST, 'day_of_week', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $start_time = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $end_time = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Insert data into the Classes table
    $sql = "INSERT INTO schedules (class_id, day_of_week, start_time, end_time, location) VALUES (:class_id, :day_of_week, :start_time, :end_time, :location)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':class_id', $class_id, PDO::PARAM_STR);
        $stmt->bindParam(':day_of_week', $day_of_week, PDO::PARAM_STR);
        $stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);
        $stmt->bindParam(':end_time', $end_time, PDO::PARAM_STR);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->execute();

        // Set success message in session
        $_SESSION['success_message'] = "Schedule created successfully!";
    } catch (PDOException $e) {
        // Set error message in session
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Redirect to the form page
header('Location: class_detail.php?class_id=' . $class_id);
exit();
?>
