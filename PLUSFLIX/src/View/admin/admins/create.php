<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Dodaj admina</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="admin-page">

<?php require __DIR__ . '/../../partials/topbar.php'; ?>
<?php $active = 'admins'; require __DIR__ . '/../partials/nav.php'; ?>

<h1 class="admin-title">Użytkownik: dodaj</h1>

<?php if (!empty($error)): ?>
    <div class="admin-alert"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="admin-alert admin-alert--ok"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form class="admin-form" method="post" action="/admin/admins/create">
    <label class="admin-label">Login</label>
    <input type="text" name="login" required>

    <label class="admin-label">Hasło</label>
    <input type="text" name="password" required>

    <label class="admin-label">Email</label>
    <input type="email" name="email" required>

    <div class="admin-actions">
        <button class="admin-primary-btn" type="submit">Zatwierdzić</button>
    </div>
</form>

</main></div>
</body>
</html>
