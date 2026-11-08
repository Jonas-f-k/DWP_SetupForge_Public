<?php
require __DIR__ . '/../partials/header.php';
?>
<h1>Admin</h1>
<p><?php echo escape($error); ?></p>
<h2>Posts</h2>
<p><a href="create-post.php">Create a post</a></p>

<?php foreach ($allPosts as $item) { ?>

    <form method="post">
        <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
        <input type="hidden" name="id" value="<?php echo (int) $item['post_id']; ?>">
        <p><a href="post.php?id=<?php echo (int) $item['post_id']; ?>"><?php echo escape($item['title']); ?></a> · <a href="create-post.php?id=<?php echo (int) $item['post_id']; ?>">Edit</a></p>
        <label>Visibility <select name="visibility">
                <option value="visible">Visible</option>
                <option value="hidden" <?php if ($item['visibility'] === 'hidden') { echo 'selected'; } ?>>Hidden</option>
        </select></label>
        <button name="action" value="moderate">Save post settings</button>
    </form>

<?php } ?>

<h2>Users</h2>

<?php foreach ($allUsers as $person) { ?>

    <form method="post">
        <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
        <input type="hidden" name="id" value="<?php echo (int) $person['user_id']; ?>">
        <p><a href="profile.php?id=<?php echo (int) $person['user_id']; ?>"><?php echo escape($person['username']); ?></a> · <?php echo escape($person['email']); ?></p>
        <label>Role <select name="role">
                <option value="member">Member</option>
                <option value="admin" <?php if ($person['role'] === 'admin') { echo 'selected'; } ?>>Admin</option>
        </select></label>
        <label>
            <input type="checkbox" name="banned" <?php if ($person['is_banned']) { echo 'checked'; } ?>>Banned</label>
        <button name="action" value="access">Save access</button>
        <button name="action" value="delete_user">Delete user and their posts</button>
    </form>

<?php } ?>

<h2>Pages</h2>

<?php foreach ($editablePages as $page) { ?>

    <form method="post">
        <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
        <input type="hidden" name="id" value="<?php echo (int) $page['page_id']; ?>">
        <?php if ($page['page_id']) { ?>
            <h3><?php echo escape($page['title']); ?></h3>
        <?php } else { ?>
            <h3>New page</h3>
        <?php } ?>
        <p>
            <label>Slug <input name="slug" value="<?php echo escape($page['slug']); ?>" maxlength="80" required></label></p>
        <p>
            <label>Title <input name="title" value="<?php echo escape($page['title']); ?>" maxlength="150" required></label></p>
        <p>
            <label>Text <textarea name="body" maxlength="10000"><?php echo escape($page['body']); ?></textarea></label></p>
        <label>
            <input type="checkbox" name="published" <?php if ($page['is_published']) { echo 'checked'; } ?>>Published</label>
        <button name="action" value="page">Save page</button>

        <?php if ($page['page_id']) { ?>

            <button name="action" value="delete_page" formnovalidate>Delete page</button>
        <?php } ?>

    </form>

<?php } ?>

<h2>Other admin options</h2>
<p>Site settings and post history are still on the to-do list.</p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
