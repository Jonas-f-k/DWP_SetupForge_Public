<?php
require __DIR__ . '/../partials/header.php';
?>
<h1><?php echo escape($page['title']); ?></h1>
<p><?php echo textFormat($page['body']); ?></p>
<?php require __DIR__ . '/../partials/footer.php'; ?>
