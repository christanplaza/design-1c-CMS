<?php
require '../../config/db.php'; // Include your PDO connection

session_start();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form data
    $schedule_id = filter_input(INPUT_POST, 'schedule_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $class_id = filter_input(INPUT_POST, 'class_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $day_of_week = filter_input(INPUT_POST, 'day_of_week', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $start_time = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $end_time = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        $stmt = $pdo->prepare('UPDATE schedules SET day_of_week = ?, start_time = ?, end_time = ?, location = ? WHERE id = ?');
        $stmt->execute([$day_of_week, $start_time, $end_time, $location, $schedule_id]);

        // Set success message in session
        $_SESSION['success_message'] = "Schedule Updated successfully!";
    } catch (PDOException $e) {
        // Set error message in session
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Redirect to the form page
header('Location: class_detail.php?class_id=' . $class_id);
exit();
?>
