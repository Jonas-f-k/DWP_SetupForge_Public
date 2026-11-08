<?php
$post = $posts->find((int) ($_GET['id'] ?? 0));
if (!$post || (($post['visibility'] !== 'visible' || $post['is_banned']) && !$isAdmin)) {
    http_response_code(404);
    exit;
}
$path = $post['image_path'];
$upload = preg_match('/^uploads\/[a-f0-9]{32}\.(png|jpg|webp)$/', $path);
$sample = preg_match('/^assets\/sample-[123]\.svg$/', $path);
if ((!$upload && !$sample) || !is_file(__DIR__ . '/../' . $path)) {
    http_response_code(404);
    exit;
}
$types = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'webp' => 'image/webp', 'svg' => 'image/svg+xml'];
$extension = pathinfo($path, PATHINFO_EXTENSION);
header('Content-Type: ' . $types[$extension]);
header("Content-Security-Policy: default-src 'none'; sandbox");
readfile(__DIR__ . '/../' . $path);
