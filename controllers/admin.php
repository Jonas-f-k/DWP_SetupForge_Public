<?php
if (!$currentUser) {
    header('Location: login.php');
    exit;
}
if (!$isAdmin) {
    http_response_code(403);
    exit('Admin access required.');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkToken();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    try {
        if ($action === 'moderate') {
            $visibility = 'visible';
            if (isset($_POST['visibility']) && $_POST['visibility'] === 'hidden') {
                $visibility = 'hidden';
            }
            $posts->moderate($id, $visibility);
        } elseif ($action === 'access' || $action === 'delete_user') {
            if ($id == $currentUser['user_id']) {
                $error = 'You cannot remove your own admin access or account.';
            } elseif ($action === 'delete_user') {
                $pictures = $posts->imagesByUser($id);
                $users->delete($id);
                foreach ($pictures as $picture) {
                    deleteImage($picture['image_path']);
                }
            } else {
                $role = 'member';
                if (isset($_POST['role']) && $_POST['role'] === 'admin') {
                    $role = 'admin';
                }
                $banned = 0;
                if (isset($_POST['banned'])) {
                    $banned = 1;
                }
                $users->access($id, $role, $banned);
            }
        } elseif ($action === 'page') {
            $slug = trim($_POST['slug'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $body = trim($_POST['body'] ?? '');
            $published = 0;
            if (isset($_POST['published'])) {
                $published = 1;
            }
            if (!preg_match('/^[a-z0-9-]{1,80}$/', $slug)) {
                $error = 'Slug: use lowercase letters, numbers and hyphens.';
            } elseif ($title === '' || strlen($title) > 150 || strlen($body) > 10000) {
                $error = 'Enter a title up to 150 bytes and text up to 10000 bytes.';
            } else {
                $cms->savePage($id, $slug, $title, $body, $published, $currentUser['user_id']);
            }
        } elseif ($action === 'delete_page') {
            $cms->deletePage($id);
        }
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log($e->getMessage());
        $error = 'Could not save. A username, email or page slug may already be used.';
    }
    if ($error === '') {
        header('Location: admin.php');
        exit;
    }
}
$allPosts = $posts->adminList();
$allUsers = $users->all();
$editablePages = $cms->pages();
$editablePages[] = ['page_id' => 0, 'slug' => '', 'title' => '', 'body' => '', 'is_published' => 0];
