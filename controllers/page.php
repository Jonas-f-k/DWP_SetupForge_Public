<?php
$slug = $_GET['slug'] ?? 'about';
$page = $cms->page($slug);
if (!$page || !$page['is_published']) {
    http_response_code(404);
    exit('Page not found.');
}
