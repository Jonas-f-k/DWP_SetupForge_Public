<?php
$id = 0;
if ($currentUser) {
    $id = $currentUser['user_id'];
}
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
}
$profile = $users->find($id);
if (!$profile || ($profile['is_banned'] && !$isAdmin)) {
    http_response_code(404);
    exit('Profile not found.');
}
$canEdit = false;
if ($currentUser) {
    if ($id == $currentUser['user_id'] || $isAdmin) {
        $canEdit = true;
    }
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkToken();
    if (!$canEdit) {
        http_response_code(403);
        exit('You cannot edit this profile.');
    }
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $password = $_POST['password'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';

    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $error = 'Use 3 to 50 letters, numbers or underscores for the username.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $error = 'Enter a valid email.';
    } elseif (strlen($bio) > 5000) {
        $error = 'Bio is too long.';
    } elseif ($password !== '' && (strlen($password) < 8 || strlen($password) > 72)) {
        $error = 'Use a password between 8 and 72 bytes.';
    } elseif ($password !== '' && !$isAdmin && !password_verify($currentPassword, $profile['password_hash'])) {
        $error = 'Current password incorrect.';
    } else {
        try {
            $users->update($id, $username, $email, $bio, $password);
            header('Location: profile.php?id=' . $id);
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Could not save. The username or email may already be used.';
        }
    }
}
$list = $posts->forUser($id);
