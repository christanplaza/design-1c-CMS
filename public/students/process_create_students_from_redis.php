<?php
// Include config.php
require_once '../../config/config.php';
require_once '../../config/db.php';

// Connect to Redis server
$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);

// If authentication is required
$redis->auth(REDIS_PASSWORD);
    
// Retrieve data
$keys = $redis->HKEYS('register');

$parsed_data = array();


foreach ($keys as $key) {
    // Split the string into an array of values using '%' as the delimiter
    $values = explode('%', $key);

    // Create an associative array for each entry
    $entry = array(
        'first_name' => $values[0],
        'last_name' => $values[1],
        'course' => $values[2],
        'student_number' => $values[3],
        'phone_number' => $values[4]
    );

    // Add the entry to the parsed data array
    $parsed_data[] = $entry;
}

$sql = "INSERT INTO students (student_number, course, first_name, last_name, phone_number) VALUES (:student_number, :course, :first_name, :last_name, :phone_number)";

try {
    $stmt = $pdo->prepare($sql);

    // Loop through each student data
    foreach ($parsed_data as $student) {
        // Check if student number already exists
        $check_sql = "SELECT COUNT(*) FROM students WHERE student_number = :student_number";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->bindParam(':student_number', $student['student_number'], PDO::PARAM_STR);
        $check_stmt->execute();
        $count = $check_stmt->fetchColumn();
    
        if ($count == 0) {
            // Student does not exist, proceed with insertion
            $insert_sql = "INSERT INTO students (student_number, course, first_name, last_name, phone_number, is_deleted) VALUES (:student_number, :course, :first_name, :last_name, :phone_number, 0)";
            $stmt = $pdo->prepare($insert_sql);
            $stmt->bindParam(':student_number', $student['student_number'], PDO::PARAM_STR);
            $stmt->bindParam(':course', $student['course'], PDO::PARAM_STR);
            $stmt->bindParam(':first_name', $student['first_name'], PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $student['last_name'], PDO::PARAM_STR);
            $stmt->bindParam(':phone_number', $student['phone_number'], PDO::PARAM_STR) ;
            $stmt->execute();
        } else {
            // Student already exists, skip insertion
            // You may choose to do something else here, such as update the existing record
            continue;
        }
    }

    // Set success message in session
    $_SESSION['success_message'] = "Students created successfully!";
} catch (PDOException $e) {
    // Set error message in session
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
}


// Close Redis connection
$redis->close();
header('Location: ../students.php');
?>