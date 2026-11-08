<?php
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkToken();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = $users->login($email, $password);

    if ($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['token'] = bin2hex(random_bytes(32));
        header('Location: index.php');
        exit;
    }
    $error = 'Email or password incorrect, or account unavailable.';
}
