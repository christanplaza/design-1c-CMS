<?php
session_start();
include '../config/db.php'; // Database Connection

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
    // Process login form submission or API request here
    // Collect and sanitize input, validate credentials, etc.

    $username = !empty($_POST['username']) ? trim($_POST['username']) : null;
    $passwordAttempt = !empty($_POST['password']) ? trim($_POST['password']) : null;

    // Validate credentials against the database
    $sql = "SELECT id, username, password FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindValue(':username', $username);
    
    // Execute the statement
    $stmt->execute();
    
    // Fetch the user
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Assume the login is not valid initially
    $validLogin = false;

    if ($user) {
        // User account found, now verify the password
        if (password_verify($passwordAttempt, $user['password'])) {
            // Correct password
            $validLogin = true;
        }
    }

    // Assume $validLogin is a boolean that is true if login is successful
    if ($validLogin) {
        // For API request, send JSON response
        if ($isApiRequest) {
            sendJsonResponse('success', 'Login successful', ['user_id' => $user['id']]);
        } else {
            // For web request, set session variables and redirect to dashboard
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in'] = time();
            header('Location: students.php');
            exit;
        }
    } else {
        // Handle login error
        $error = "Invalid credentials.";
        if ($isApiRequest) {
            sendJsonResponse('error', $error);
        }
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
                <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Sign in to your account</h2>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <!-- Display error message if login fails -->
                <?php if (isset($error)): ?>
                    <p><?php echo $error; ?></p>
                <?php endif; ?>

                <!-- Display success message if password reset link is sent -->
                <?php if (isset($_SESSION['password_reset_success'])): ?>
                    <p>Password reset link has been sent to your email.</p>
                    <?php unset($_SESSION['password_reset_success']); ?>
                <?php endif; ?>

                <!-- Display error message if email is not found during password reset -->
                <?php if (isset($_SESSION['password_reset_error'])): ?>
                    <p><?php echo $_SESSION['password_reset_error']; ?></p>
                    <?php unset($_SESSION['password_reset_error']); ?>
                <?php endif; ?>
                
                <form class="space-y-6" action="login.php" method="POST">
                    <div>
                        <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Username</label>
                        <div class="mt-2">
                        <input id="username" name="username" type="username" autocomplete="username" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-2">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                        <div class="text-sm">
                            <a href="forgot_password.php" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                        </div>
                        </div>
                        <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-2">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
                    </div>
                </form>

                <p class="mt-10 text-center text-sm text-gray-500">
                Not a member?
                <a href="signup.php" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Sign Up Here</a>
                </p>
            </div>
        </div>
    </body>
</html>

<?php endif; // End of web request check ?>