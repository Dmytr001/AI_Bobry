<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Admin – Filmy</title>
</head>
<body>
  <h1>Filmy (Admin)</h1>

  <p>
    <a href="/admin">← Panel</a> |
    <a href="/admin/movies/create">+ Dodaj film</a> |
    <a href="/admin/logout">Wyloguj</a>
  </p>

  <form method="get" action="/admin/movies" style="margin: 12px 0;">
    <input type="text" name="q" placeholder="Szukaj po tytule..." value="<?= htmlspecialchars($q ?? '') ?>">
    <button type="submit">Szukaj</button>
    <?php if (!empty($q)): ?>
      <a href="/admin/movies">Wyczyść</a>
    <?php endif; ?>
  </form>

  <?php if (empty($movies)): ?>
    <p>Brak filmów.</p>
  <?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tytuł</th>
          <th>Akcje</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($movies as $m): ?>
          <tr>
            <td><?= (int)$m['id'] ?></td>
            <td><?= htmlspecialchars($m['name'] ?? '') ?></td>
            <td>
              <a href="/admin/movies/edit?id=<?= (int)$m['id'] ?>">Edytuj</a>

              <form method="post" action="/admin/movies/delete" style="display:inline;" onsubmit="return confirm('Na pewno usunąć film?');">
                <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                <button type="submit">Usuń</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
