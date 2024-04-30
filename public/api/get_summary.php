<?php
// Include config.php
require_once '../../config/config.php';
require_once '../../config/db.php';
session_start();

// Retrieve the class_id and period from the request
$classId = $_POST['class_id'];
$period = $_POST['period'];


// When ga start kag end ang 1st sem kag 2nd sem 

// Include the PHPSpreadsheet and PHPMailer libraries
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Validate the period parameter
if (!in_array($period, ['weekly', 'monthly', 'semestral'])) {
    echo json_encode(['error' => 'Invalid period specified']);
    exit();
}

// Get the current date and calculate the start and end dates based on the period
$currentDate = date('Y-m-d');
$startDate = '';
$endDate = '';

// Fetch the teacher information for the class
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ? ");

$stmt->execute([$classId]);
$classInfo = $stmt->fetch();

if ($period === 'weekly') {
    $startDate = date('Y-m-d', strtotime('-1 week'));
    $endDate = $currentDate;
} elseif ($period === 'monthly') {
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');
} elseif ($period === 'semestral') {
    $currentMonth = date('m');
    $currentYear = date('Y');
    if ($currentMonth >= 1 && $currentMonth <= 6) { // Sem 1 = 1st half of year, sem 2 = 2nd half of year
        $startDate = $currentYear . '-01-01';
        $endDate = $currentYear . '-06-30';
    } else {
        $startDate = $currentYear . '-07-01';
        $endDate = $currentYear . '-12-31';
    }
}

// Retrieve the attendance summary for each student in the specified class and period
$stmt = $pdo->prepare("
    SELECT
        st.student_number,
        st.first_name,
        st.last_name,
        COUNT(CASE WHEN a.first_detected IS NOT NULL AND TIME(a.first_detected) <= s.start_time + INTERVAL 15 MINUTE THEN 1 END) AS present_count,
        COUNT(CASE WHEN a.first_detected IS NOT NULL AND TIME(a.first_detected) > s.start_time + INTERVAL 15 MINUTE THEN 1 END) AS late_count,
        COUNT(CASE 
            WHEN a.first_detected IS NULL 
            OR (a.last_detected IS NOT NULL AND TIME(a.last_detected) < s.end_time - INTERVAL 15 MINUTE)
            THEN 1 
        END) AS absent_count
    FROM
        students st
        LEFT JOIN attendance_table a ON st.student_number = a.student_number AND DATE(a.first_detected) BETWEEN ? AND ?
        LEFT JOIN schedules s ON s.class_id = ?
    WHERE
        st.course = (SELECT course FROM classes WHERE id = ?)
    GROUP BY
        st.student_number, st.first_name, st.last_name
");

$stmt->execute([$startDate, $endDate, $classId, $classId]);
$attendanceSummary = $stmt->fetchAll();

// Return the attendance summary as JSON response
header('Content-Type: application/json');
echo json_encode($attendanceSummary);

// Fetch the teacher information for the class
$stmt = $pdo->prepare("
    SELECT
        t.id,
        t.name,
        t.email_address
    FROM
        classes c
        JOIN teachers t ON c.teacher_id = t.id
    WHERE
        c.id = ?
");

$stmt->execute([$classId]);
$teacherInfo = $stmt->fetch();

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Class Name: ');
$sheet->setCellValue('B1', $classInfo['title']);
$sheet->setCellValue('A2', 'Course: ');
$sheet->setCellValue('B2', $classInfo['course']);

// Set the header row
$sheet->setCellValue('A3', 'Student Number');
$sheet->setCellValue('B3', 'First Name');
$sheet->setCellValue('C3', 'Last Name');
$sheet->setCellValue('D3', 'Present Count');
$sheet->setCellValue('E3', 'Late Count');
$sheet->setCellValue('F3', 'Absent Count');

// Populate the data rows
$rowIndex = 4;
foreach ($attendanceSummary as $student) {
    $sheet->setCellValue('A' . $rowIndex, $student['student_number']);
    $sheet->setCellValue('B' . $rowIndex, $student['first_name']);
    $sheet->setCellValue('C' . $rowIndex, $student['last_name']);
    $sheet->setCellValue('D' . $rowIndex, $student['present_count']);
    $sheet->setCellValue('E' . $rowIndex, $student['late_count']);
    $sheet->setCellValue('F' . $rowIndex, $student['absent_count']);
    $rowIndex++;
}

// Save the spreadsheet to a temporary file
$tempFilePath = tempnam(sys_get_temp_dir(), 'attendance_summary');
$writer = new Xlsx($spreadsheet);
$writer->save($tempFilePath);

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Configure the SMTP settings
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Port = 2525;
    $mail->Username = MAILTRAP_USERNAME;
    $mail->Password = MAILTRAP_PASSWORD;

    // Set the email content
    $mail->setFrom('attendancesystem@mail.com', 'Attendance System');
    $mail->addAddress($teacherInfo['email_address'], $teacherInfo['name']);
    $mail->Subject = $classInfo['title'] . ' - ' . $classInfo['course'] . ' Attendance Summary (' . $period . ')';
    $mail->Body = 'Please find the attendance summary attached.';

    // Attach the Excel file
    $mail->addAttachment($tempFilePath, 'attendance_summary.xlsx');

    // Send the email
    $mail->send();

    $_SESSION['success_message'] = "Attendance summary sent successfully.";
} catch (Exception $e) {
    $_SESSION['error_message'] = "Email could not be sent. Error: {$mail->ErrorInfo}";
}



// Delete the temporary file
unlink($tempFilePath);

header('Location: ../classes/class_detail.php?class_id=' . $classId);
?>