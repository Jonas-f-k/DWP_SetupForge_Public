<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Log in</h1>
<p><?php echo escape($error); ?></p>
<?php if (isset($_GET['registered'])) { ?>
    <p>Account created. You can now log in.</p>
<?php } ?>

<form method="post">
    <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
    <p><label>Email <input type="email" name="email" required></label></p>
    <p><label>Password <input type="password" name="password" required></label></p>
    <button>Log in</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>
