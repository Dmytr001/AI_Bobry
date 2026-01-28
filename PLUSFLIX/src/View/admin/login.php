<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin login</title>
    <link rel="stylesheet" href="/style.css">
</head>

<?php
$return = $return ?? ($_GET['return'] ?? '/admin/movies');
$overlay = ($overlay ?? (($_GET['overlay'] ?? '0') === '1'));
?>

<body class="admin-login-overlay-page">


<div class="admin-login-backdrop"></div>

<div class="admin-login-wrap" role="dialog" aria-modal="true">
    <div class="admin-login-card admin-login-anim">

        <?php if (!empty($error)): ?>
            <div class="admin-login-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form class="admin-login-form" method="post" action="/admin/login">
            <input type="hidden" name="return" value="<?= htmlspecialchars($return) ?>">

            <input class="admin-login-input" type="text" name="login" placeholder="Imię" required>
            <input class="admin-login-input" type="password" name="password" placeholder="Hasło" required>

            <button class="admin-login-btn" type="submit">Login</button>
            <a class="admin-login-back" href="/">Powrót</a>

        </form>

    </div>
</div>

<script>
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') window.location.href = '/';

    });
</script>
</body>
</html>
