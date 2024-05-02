<?php
session_start();
include '../config/db.php'; // Include your database connection


if (!isset($_GET['token'])) {
    header('Location: login.php');
    exit;
}

$resetToken = $_GET['token'];

$verifyTokenSql = "SELECT id, email FROM users WHERE reset_token = :reset_token AND reset_token_expiration > NOW()";
$verifyTokenStmt = $pdo->prepare($verifyTokenSql);
$verifyTokenStmt->bindParam(':reset_token', $resetToken, PDO::PARAM_STR);
$verifyTokenStmt->execute();
$user = $verifyTokenStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit;
}

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate the passwords
    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Update the user's password in the database
        $updateSql = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expiration = NULL WHERE id = :id";
        $updateStmt = $pdo->prepare($updateSql);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt->bindValue(':password', $hashedPassword);
        $updateStmt->bindValue(':id', $user['id']);
        $updateStmt->execute();

        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
    <?php include 'head.php'; ?>
    <body class="bg-gray-200 h-full">
        <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
                <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Reset Password</h2>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <!-- Display error message if passwords do not match -->
                <?php if (isset($error)): ?>
                    <p><?php echo $error; ?></p>
                <?php endif; ?>
                
                <form class="space-y-6" action="reset_password_confirmation.php?token=<?= $resetToken ?>" method="POST">
                    <div>
                        <label for="new_password" class="block text-sm font-medium leading-6 text-gray-900">New Password</label>
                        <div class="mt-2">
                            <input id="new_password" name="new_password" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-2">
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password</label>
                        <div class="mt-2">
                            <input id="confirm_password" name="confirm_password" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-2">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>