<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Logowanie administratora</title>
</head>
<body>
  <h1>Logowanie administratora</h1>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" action="/admin/login">
    <div>
      <label>Imię / login</label><br>
      <input type="text" name="login" required>
    </div>
    <br>
    <div>
      <label>Hasło</label><br>
      <input type="password" name="password" required>
    </div>
    <br>
    <button type="submit">Login</button>
  </form>

  <p><a href="/">Powrót</a></p>
</body>
</html>

