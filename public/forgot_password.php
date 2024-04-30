<?php
session_start();
include '../config/config.php'; 
include '../config/db.php'; // Include your database connection

// Include the PHPMailer library
require_once '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Function to send JSON response
function sendJsonResponse($status, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

// Check if the request is coming from API (like a mobile app)
$isApiRequest = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

// Check if the form is submitted or if it's an API request
if ($_SERVER["REQUEST_METHOD"] == "POST" || $isApiRequest) {
    // Process forgot password form submission or API request here
    // Collect and sanitize input, validate email, etc.

    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;

    // Validate email against the database
    $sql = "SELECT id, username, email FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindValue(':email', $email);
    
    // Execute the statement
    $stmt->execute();
    
    // Fetch the user
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Assume the email is not found initially
    $emailFound = false;

    if ($user) {
      // User account found
      $emailFound = true;
      
      // Generate a unique password reset token
      $resetToken = generateResetToken();
      
      // Store the reset token and its expiration time in the database
      $expirationTime = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
      $updateSql = "UPDATE users SET reset_token = :reset_token, reset_token_expiration = :expiration WHERE id = :id";
      $updateStmt = $pdo->prepare($updateSql);
      $updateStmt->bindValue(':reset_token', $resetToken);
      $updateStmt->bindValue(':expiration', $expirationTime);
      $updateStmt->bindValue(':id', $user['id']);
      $updateStmt->execute();
      
      // Send the password reset link to the user's email
      sendPasswordResetLink($user['email'], $resetToken);
    }

    // Assume $emailFound is a boolean that is true if email is found
    if ($emailFound) {
        // For API request, send JSON response
        if ($isApiRequest) {
            sendJsonResponse('success', 'Password reset email sent');
        } else {
            // For web request, set session variable and redirect to confirmation page
            // $_SESSION['password_reset_email'] = $email;
            $_SESSION['password_reset_success'] = true;
            header('Location: login.php');
            exit;
        }
    } else {
        // Handle email not found error
        $error = "Email not found.";
        if ($isApiRequest) {
            sendJsonResponse('error', $error);
        } else {
          $_SESSION['password_reset_error'] = $error;
          header('Location: login.php');
        }
    }
}

// Function to generate a unique reset token
function generateResetToken() {
  return bin2hex(random_bytes(32));
}

// Function to send the password reset link
function sendPasswordResetLink($email, $resetToken) {
  $appName = APP_NAME;
  $resetLink = "http://localhost/$appName/reset_password_confirmation.php?token=" . $resetToken;
  
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
      $mail->setFrom('noreply@example.com', 'Design 1C CMS');
      $mail->addAddress($email);
      $mail->Subject = 'Password Reset';
      $mail->Body = "To reset your password: <a href='$resetLink'>Click Here</a>";
      $mail->isHTML(true);

      // Send the email
      $mail->send();
  } catch (Exception $e) {
      // Handle email sending error
      error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
  }
}

// If it's a web request, continue to render HTML
if (!$isApiRequest):
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
    <?php include 'head.php'; ?>
    <body class="bg-gray-200 h-full">
        <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
                <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Forgot Password</h2>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <!-- Display error message if email is not found -->
                <?php if (isset($error)): ?>
                    <p><?php echo $error; ?></p>
                <?php endif; ?>
                
                <form class="space-y-6" action="forgot_password.php" method="POST">
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-2">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Reset Password</button>
                    </div>
                </form>

                <p class="mt-10 text-center text-sm text-gray-500">
                    Remember your password?
                    <a href="login.php" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Login</a>
                </p>
            </div>
        </div>
    </body>
</html>

<?php endif; // End of web request check ?>