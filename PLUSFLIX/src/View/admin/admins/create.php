<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Admin – Dodaj administratora</title>
</head>
<body>

<?php $active = 'movies'; require __DIR__ . '/../partials/nav.php'; ?>

<h1>Dodaj administratora</h1>

<?php if (!empty($error)): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!empty($success)): ?>
  <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post" action="/admin/admins/create">
  <div>
    <label>Login *</label><br>
    <input type="text" name="login" required>
  </div>
  <br>
  <div>
    <label>Hasło *</label><br>
    <input type="text" name="password" required>
  </div>
  <br>
  <div>
    <label>Email *</label><br>
    <input type="email" name="email" required>
  </div>
  <br>
  <button type="submit">Utwórz admina</button>
</form>

<p><a href="/admin/movies">← Wróć do panelu</a></p>

</body>
</html>
