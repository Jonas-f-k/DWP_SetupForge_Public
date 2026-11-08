<?php
if (!$currentUser) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Use the like button.');
}
checkToken();
$id = (int) ($_POST['post_id'] ?? 0);
$post = $posts->find($id);
if (!$post || $post['visibility'] !== 'visible' || $post['is_banned']) {
    http_response_code(404);
    exit('Post not found.');
}
if (($_POST['action'] ?? '') === 'unlike') {
    $posts->unlike($currentUser['user_id'], $id);
} else {
    $posts->like($currentUser['user_id'], $id);
}
$page = $_POST['return_page'] ?? 'gallery.php';
if (!in_array($page, ['index.php', 'gallery.php', 'profile.php'], true)) {
    $page = 'gallery.php';
}
if ($page === 'profile.php') {
    $profileId = (int) ($_POST['profile_id'] ?? 0);
    if ($profileId > 0) {
        $page .= '?id=' . $profileId;
    }
}
$anchor = $_POST['anchor'] ?? 'card-1-' . $id;
if (!is_string($anchor) || !preg_match('/^card-[0-9]+-' . $id . '$/', $anchor)) {
    $anchor = 'card-1-' . $id;
}
header('Location: ' . $page . '#' . $anchor, true, 303);
exit;
