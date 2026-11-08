<?php
if (!$currentUser) {
    header('Location: login.php');
    exit;
}
$error = '';
$id = (int) ($_GET['id'] ?? 0);
$post = ['title' => '', 'description' => '', 'category_id' => 1, 'image_path' => ''];
if ($id) {
    $post = $posts->find($id);
    if (!$post) {
        http_response_code(404);
        exit('Post not found.');
    }
    if (!$isAdmin && $post['user_id'] != $currentUser['user_id']) {
        http_response_code(403);
        exit('This is not your post.');
    }
}
$categories = $posts->categories();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkToken();
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $categoryExists = false;
    foreach ($categories as $category) {
        if ($category['category_id'] == $categoryId) {
            $categoryExists = true;
        }
    }
    if ($title === '' || strlen($title) > 150 || strlen($description) > 10000) {
        $error = 'Enter a title up to 150 bytes and text up to 10000 bytes.';
    } elseif (!$categoryExists) {
        $error = 'Choose a category.';
    } else {
        require __DIR__ . '/../includes/upload.php';
        if (!$id && $imagePath === '' && $error === '') {
            $error = 'Choose an image.';
        }
        if ($error === '') {
            $image = $post['image_path'];
            if ($imagePath !== '') {
                $image = $imagePath;
            }
            try {
                if ($id) {
                    $posts->update($id, $categoryId, $title, $description, $image);
                    if ($imagePath !== '') {
                        deleteImage($post['image_path']);
                    }
                } else {
                    $id = $posts->create($currentUser['user_id'], $categoryId, $title, $description, $image);
                }
                header('Location: post.php?id=' . $id);
                exit;
            } catch (PDOException $e) {
                error_log($e->getMessage());
                deleteImage($imagePath);
                $error = 'Could not save the post.';
            }
        }
    }
    $post['title'] = $title;
    $post['description'] = $description;
    $post['category_id'] = $categoryId;
}
