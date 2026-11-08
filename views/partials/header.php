<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo escape($settings['site_name'] ?? 'SetupForge'); ?></title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo filemtime(__DIR__ . '/../../assets/style.css'); ?>">
</head>
<body>

<nav>
    <a class="brand" href="index.php"><?php echo escape($settings['site_name'] ?? 'SetupForge'); ?> / community</a>
    <a href="index.php">Home</a>
    <a href="gallery.php">All pictures</a>
    <?php foreach ($navPages as $navPage) { ?>
        <a href="page.php?slug=<?php echo escape($navPage['slug']); ?>"><?php echo escape($navPage['title']); ?></a>
    <?php } ?>
    <?php if ($currentUser) { ?>
        <a href="create-post.php">Upload image</a>
        <a href="profile.php">My profile</a>
        <?php if ($isAdmin) { ?>
            <a href="admin.php">Admin</a>
        <?php } ?>
        <form action="logout.php" method="post">
            <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
            <button>Log out</button>
        </form>
    <?php } else { ?>
        <a href="register.php">Create account</a>
        <a href="login.php">Log in</a>
    <?php } ?>
</nav>
