<?php require __DIR__ . '/../partials/header.php'; ?>

<?php if ($id) { ?>
    <h1>Edit post</h1>
<?php } else { ?>
    <h1>Upload image</h1>
<?php } ?>
<p><?php echo escape($error); ?></p>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">

    <p>
        <label>Title
            <input name="title" maxlength="150" value="<?php echo escape($post['title']); ?>" required>
        </label>
    </p>
    <p>
        <label>Category
            <select name="category_id">
                <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo (int) $category['category_id']; ?>" <?php if ($category['category_id'] == $post['category_id']) { echo 'selected'; } ?>>
                        <?php echo escape($category['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </label>
    </p>
    <p>
        <label>Description
            <textarea name="description" maxlength="10000"><?php echo escape($post['description']); ?></textarea>
        </label>
    </p>
    <p>Use **bold** and __italic__ for text styling.</p>
    <p>
        <label>Image
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" <?php if (!$id) { echo 'required'; } ?>>
        </label>
    </p>
    <p>JPEG, PNG or WebP. Maximum 5 MB, 100–4096 pixels per side and a 4:1 ratio. Maximum 12 megapixels.</p>
    <p>When editing, leave the file empty to keep your picture.</p>
    <button>Save post</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>
