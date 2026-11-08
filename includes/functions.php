<?php
function escape($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function textFormat($text)
{
    $text = escape($text);
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/__(.+?)__/s', '<em>$1</em>', $text);
    return nl2br($text);
}

function checkToken()
{
    $token = $_POST['token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['token'], $token)) {
        http_response_code(403);
        exit('Invalid form. Reload the page.');
    }
}

function deleteImage($path)
{
    if (preg_match('/^uploads\/[a-f0-9]{32}\.(png|jpg|webp)$/', $path)) {
        $file = __DIR__ . '/../' . $path;
        if (is_file($file)) {
            unlink($file);
        }
    }
}
