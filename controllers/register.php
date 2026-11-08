<?php
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkToken();
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $error = 'Use 3 to 50 letters, numbers or underscores for the username.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $error = 'Enter a valid email.';
    } elseif (strlen($password) < 8 || strlen($password) > 72) {
        $error = 'Use a password between 8 and 72 bytes.';
    } else {
        try {
            $users->create($username, $email, $password);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Could not create account. The username or email may already be used.';
        }
    }
}
