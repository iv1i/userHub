<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \core\Views::yield('title', 'App') ?></title>
    <link rel="stylesheet" href="/css/main.css" type="text/css">
    <link rel="stylesheet" href="/css/fontawesome-free-7.1.0-web/css/all.css" type="text/css">
    <link rel="icon" href="/icons/favicon.png" type="image/png">
    <?= \core\Views::yield('styles') ?>

    <?= \core\Views::yield('head_scripts') ?>
</head>
<body>
<div class="header">
    <div class="container">
        <h1><?php echo env('APP_NAME') ?></h1>
        <?php if (isset($_SESSION['admin_logged_in'])): ?>
            <nav>
                <a href="/users">Users</a>
                <a href="/logout">Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a>
            </nav>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    <?php endif; ?>
</div>

<main class="container">
    <?= \core\Views::yield('content') ?>
</main>

<?= \core\Views::yield('footer') ?>

<?= \core\Views::yield('scripts') ?>
</body>
</html>