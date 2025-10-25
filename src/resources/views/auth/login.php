<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users-App | Login</title>
    <script src="https://kit.fontawesome.com/925f64a3cb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../resources/css/login.css">
</head>
<body>
    <div class="card">
        <h2>Admin Login</h2>

        <?php use core\Security;

        if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
