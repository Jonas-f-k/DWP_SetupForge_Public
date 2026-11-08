<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Create account</h1>
<p><?php echo escape($error); ?></p>

<form method="post">
    <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
    <p><label>Username <input name="username" maxlength="50" required></label></p>
    <p><label>Email <input type="email" name="email" maxlength="254" required></label></p>
    <p><label>Password <input type="password" name="password" minlength="8" maxlength="72" required></label></p>
    <button>Create account</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>
