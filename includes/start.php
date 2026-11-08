<?php
ini_set('display_errors', '0');
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true
]);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

require __DIR__ . '/functions.php';
require __DIR__ . '/../models/Database.php';
require __DIR__ . '/../models/User.php';
require __DIR__ . '/../models/Post.php';
require __DIR__ . '/../models/Cms.php';

if (!file_exists(__DIR__ . '/../config/database.php')) {
    exit('Add your database details in config/database.php.');
}
$database = new Database();
$db = $database->connect();
$users = new User($db);
$posts = new Post($db);
$cms = new Cms($db);

$currentUser = false;
$isAdmin = false;
if (isset($_SESSION['user_id'])) {
    $currentUser = $users->find($_SESSION['user_id']);
    if (!$currentUser || $currentUser['is_banned']) {
        $_SESSION = [];
        $currentUser = false;
    } else {
        $isAdmin = $currentUser['role'] === 'admin';
    }
}
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$settings = $cms->settings();
$navPages = $cms->pages(true);
