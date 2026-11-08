<?php
if (!isset($cardSection)) {
    $cardSection = 0;
}
$cardSection++;
?>
<?php if (!$list) { ?>
    <p class="empty-state">No pictures here yet. Share your setup to get started.</p>
<?php } ?>
<div class="gallery-grid">
<?php foreach ($list as $item) { ?>
    <?php
    $cardLiked = false;
    if ($currentUser) {
        $cardLiked = $posts->liked($currentUser['user_id'], $item['post_id']);
    }
    $likeClass = '';
    $likeAction = 'like';
    $likePressed = 'false';
    $likeText = '♡ Like';
    if ($cardLiked) {
        $likeClass = 'is-liked';
        $likeAction = 'unlike';
        $likePressed = 'true';
        $likeText = '♥ Liked';
    }
    ?>
    <article class="setup-card" id="card-<?php echo $cardSection; ?>-<?php echo (int) $item['post_id']; ?>">
        <a class="card-picture" href="post.php?id=<?php echo (int) $item['post_id']; ?>">
            <?php if ($item['image_path']) { ?>
                <img src="image.php?id=<?php echo (int) $item['post_id']; ?>" alt="<?php echo escape($item['title']); ?>" loading="lazy">
            <?php } else { ?>
                <span class="no-picture">No image</span>
            <?php } ?>
        </a>
        <div class="card-content">
            <span class="category"><?php echo escape($item['category_name']); ?></span>
            <h3><a href="post.php?id=<?php echo (int) $item['post_id']; ?>"><?php echo escape($item['title']); ?></a></h3>
            <p class="card-author">By <a href="profile.php?id=<?php echo (int) $item['user_id']; ?>"><?php echo escape($item['username']); ?></a></p>
            <div class="card-actions">
                <?php if ($currentUser) { ?>
                    <form action="like.php" method="post" class="like-form">
                        <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
                        <input type="hidden" name="anchor" value="card-<?php echo $cardSection; ?>-<?php echo (int) $item['post_id']; ?>">
                        <input type="hidden" name="post_id" value="<?php echo (int) $item['post_id']; ?>">
                        <input type="hidden" name="return_page" value="<?php echo escape(basename($_SERVER['SCRIPT_NAME'])); ?>">
                        <input type="hidden" name="profile_id" value="<?php echo (int) ($_GET['id'] ?? 0); ?>">
                        <button class="like-button <?php echo $likeClass; ?>" name="action" value="<?php echo $likeAction; ?>" aria-pressed="<?php echo $likePressed; ?>">
                            <?php echo $likeText; ?> · <?php echo (int) $item['like_count']; ?>
                        </button>
                    </form>
                <?php } else { ?>
                    <a class="login-like" href="login.php">♡ <?php echo (int) $item['like_count']; ?> likes</a>
                <?php } ?>
                <a class="comment-link" href="post.php?id=<?php echo (int) $item['post_id']; ?>">Comments · <?php echo (int) $item['comment_count']; ?></a>
            </div>
        </div>
    </article>
<?php } ?>
</div>
