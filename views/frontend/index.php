<?php
require __DIR__ . '/../partials/header.php';
?>
<h1><?php echo escape($settings['site_name'] ?? 'SetupForge'); ?></h1>
<p><a href="create-post.php">Upload a new image</a></p>

<?php if ($about && $about['is_published']) { ?>

    <h2>About the platform</h2>
    <p><?php echo textFormat($about['body']); ?></p>

<?php } ?>

<h2>Featured pictures</h2>
<p>Still working on this section.</p>
<h2>Newest pictures</h2>
<?php $list = $newest; require __DIR__ . '/../partials/cards.php'; ?>
<h2>Latest comments</h2>

<?php foreach ($latestComments as $comment) { ?>

    <p><?php echo escape($comment['username']); ?> on <a href="post.php?id=<?php echo (int) $comment['post_id']; ?>"><?php echo escape($comment['title']); ?></a>: <?php echo textFormat($comment['body']); ?></p>

<?php } ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
