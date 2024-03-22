<?php
// Include your database connection file (e.g., db.php)
include '../../config/db.php';

// Check if the class_id is provided via POST
if (isset($_POST['class_id'])) {
    // Sanitize the input
    $classId = filter_var($_POST['class_id'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Prepare and execute the query to retrieve class details
        $stmt = $pdo->prepare('SELECT c.id, c.title, c.course, t.name AS teacher_name, c.description 
                      FROM classes c 
                      INNER JOIN teachers t ON c.teacher_id = t.id 
                      WHERE c.id = ?');
        $stmt->execute([$classId]);

        // Fetch the class details
        $classDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the class was found
        if ($classDetails) {
            // Return class details in JSON format
            header('Content-Type: application/json');
            echo json_encode($classDetails);
            exit;
        } else {
            // Class not found
            http_response_code(404);
            echo json_encode(['error' => 'Class not found']);
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
