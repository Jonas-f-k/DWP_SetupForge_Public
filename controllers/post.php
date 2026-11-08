<?php
$id = (int) ($_GET['id'] ?? 0);
$post = $posts->find($id);
if (!$post || (($post['visibility'] !== 'visible' || $post['is_banned']) && !$isAdmin)) {
    http_response_code(404);
    exit('Post not found.');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$currentUser) {
        header('Location: login.php');
        exit;
    }
    checkToken();
    $action = $_POST['action'] ?? '';
    if ($action === 'comment') {
        $body = trim($_POST['body'] ?? '');
        if ($body === '' || strlen($body) > 5000) {
            $error = 'Enter a comment up to 5000 bytes.';
        } else {
            $posts->addComment($currentUser['user_id'], $id, $body);
        }
    } elseif ($action === 'like') {
        $posts->like($currentUser['user_id'], $id);
    } elseif ($action === 'unlike') {
        $posts->unlike($currentUser['user_id'], $id);
    } elseif ($action === 'delete_comment') {
        $comment = $posts->findComment((int) ($_POST['comment_id'] ?? 0));
        if (!$comment || $comment['post_id'] != $id || (!$isAdmin && $comment['user_id'] != $currentUser['user_id'])) {
            http_response_code(403);
            exit('You cannot delete this comment.');
        }
        $posts->deleteComment($comment['comment_id']);
    } elseif ($action === 'delete') {
        if (!$isAdmin && $post['user_id'] != $currentUser['user_id']) {
            http_response_code(403);
            exit('You cannot delete this post.');
        }
        $posts->delete($id);
        deleteImage($post['image_path']);
        header('Location: index.php');
        exit;
    }
    if ($error === '') {
        header('Location: post.php?id=' . $id);
        exit;
    }
}
$postComments = $posts->comments($id);
$liked = false;
if ($currentUser) {
    $liked = $posts->liked($currentUser['user_id'], $id);
}
