<?php
$imagePath = '';
$file = $_FILES['image'] ?? null;

if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'The image could not be uploaded.';
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $error = 'Maximum file size: 5 MB.';
    } elseif (!is_uploaded_file($file['tmp_name'])) {
        $error = 'Invalid upload.';
    } else {
        $info = getimagesize($file['tmp_name']);
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $fileInfo->file($file['tmp_name']);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (!$info || !isset($types[$mime]) || $info['mime'] !== $mime) {
            $error = 'Choose a JPEG, PNG or WebP image.';
        } else {
            $width = $info[0];
            $height = $info[1];
            if ($width < 100 || $height < 100 || $width > 4096 || $height > 4096) {
                $error = 'Each side must be between 100 and 4096 pixels.';
            } elseif ($width > $height * 4 || $height > $width * 4) {
                $error = 'The image is too wide or too tall. Maximum ratio: 4:1.';
            } elseif ($width * $height > 12000000) {
                $error = 'Maximum image size: 12 megapixels.';
            } else {
                $imagePath = 'uploads/' . bin2hex(random_bytes(16)) . '.' . $types[$mime];
                if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $imagePath)) {
                    $error = 'Could not save the image. Check the uploads folder.';
                    $imagePath = '';
                }
            }
        }
    }
}
