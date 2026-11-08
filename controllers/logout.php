<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Use the log out button.');
}
checkToken();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
