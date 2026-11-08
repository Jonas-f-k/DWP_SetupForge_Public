<?php
require __DIR__ . '/../partials/header.php';
?>
<h1><?php echo escape($post['title']); ?></h1>

<?php if ($post['image_path']) { ?>

    <img src="image.php?id=<?php echo $id; ?>" width="400" alt="<?php echo escape($post['title']); ?>">
<?php } ?>

<p>By <a href="profile.php?id=<?php echo (int) $post['user_id']; ?>"><?php echo escape($post['username']); ?></a> · <?php echo escape($post['category_name']); ?></p>
<p><?php echo textFormat($post['description']); ?></p>
<p><?php echo (int) $post['like_count']; ?> likes · <?php echo (int) $post['comment_count']; ?> comments</p>
<p><?php echo escape($error); ?></p>

<?php if ($currentUser) { ?>

    <form method="post">
        <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
        <?php if ($liked) { ?>
            <button name="action" value="unlike">Unlike</button>
        <?php } else { ?>
            <button name="action" value="like">Like</button>
        <?php } ?>
    </form>

    <?php if ($isAdmin || $post['user_id'] == $currentUser['user_id']) { ?>

        <p><a href="create-post.php?id=<?php echo $id; ?>">Edit post</a></p>
        <form method="post">
            <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
            <button name="action" value="delete">Delete post and its comments</button>
        </form>
    <?php } ?>
<?php } ?>
<h2>Comments</h2>

<?php foreach ($postComments as $comment) { ?>

    <p><strong><?php echo escape($comment['username']); ?></strong>: <?php echo textFormat($comment['body']); ?></p>

    <?php if ($currentUser && ($isAdmin || $comment['user_id'] == $currentUser['user_id'])) { ?>

        <form method="post">
            <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
            <input type="hidden" name="comment_id" value="<?php echo (int) $comment['comment_id']; ?>">
            <button name="action" value="delete_comment">Delete comment</button>
        </form>
<?php } ?>
<?php } ?>

<?php if ($currentUser) { ?>

    <form method="post">
        <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
        <p>
            <label>Comment <textarea name="body" maxlength="5000" required></textarea></label></p>
        <p>Use **bold** or __italic__.</p>
        <button name="action" value="comment">Add comment</button>
    </form>

<?php } else { ?>

    <p><a href="login.php">Log in to like or comment</a></p>
<?php } ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
