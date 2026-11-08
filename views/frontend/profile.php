<?php
require __DIR__ . '/../partials/header.php';
?>
<h1><?php echo escape($profile['username']); ?></h1>
<p>Role: <?php echo escape($profile['role']); ?> · Joined: <?php echo escape($profile['created_at']); ?></p>
<p><?php echo textFormat($profile['bio']); ?></p>

<?php if ($canEdit) { ?>

    <h2>Edit profile</h2>
    <p><?php echo escape($error); ?></p>
    <form method="post">
        <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
        <p>
            <label>Username <input name="username" value="<?php echo escape($profile['username']); ?>" maxlength="50" required></label></p>
        <p>
            <label>Email <input name="email" type="email" value="<?php echo escape($profile['email']); ?>" maxlength="254" required></label></p>
        <p>
            <label>Bio <textarea name="bio" maxlength="5000"><?php echo escape($profile['bio']); ?></textarea></label></p>
        <p>
            <label>Current password <input name="current_password" type="password"></label></p>
        <p>
            <label>New password (optional) <input name="password" type="password" minlength="8" maxlength="72"></label></p>
        <button>Save profile</button>
    </form>

<?php } ?>

<p><a href="create-post.php">Upload a new image</a></p>
<h2>Uploaded pictures</h2>
<?php require __DIR__ . '/../partials/cards.php'; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
