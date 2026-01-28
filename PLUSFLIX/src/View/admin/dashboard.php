<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Panel admina</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
  <h1>Panel admina</h1>
  <p>Zalogowano jako: <?= htmlspecialchars($_SESSION['admin_login'] ?? 'admin') ?></p>
  <p><a href="/admin/movies">Zarządzaj filmami</a></p>
  <p><a href="/admin/reviews">Zarządzaj ocenami</a></p>

  <form method="post" action="/admin/logout">
    <button type="submit">Wyloguj</button>
  </form>
</body>
</html>
