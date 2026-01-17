<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Admin – Oceny</title>
</head>
<body>
  <h1>Oceny / Recenzje (Admin)</h1>

  <p>
    <a href="/admin">← Panel</a> |
    <a href="/admin/movies">Filmy</a> |
    <form method="post" action="/admin/logout" style="display:inline;">
      <button type="submit">Wyloguj</button>
    </form>
  </p>

  <form method="get" action="/admin/reviews" style="margin: 12px 0;">
    <input type="text" name="q" placeholder="Szukaj po treści..." value="<?= htmlspecialchars($q ?? '') ?>">
    <input type="text" name="title_id" placeholder="title_id" value="<?= htmlspecialchars($titleId ?? '') ?>" style="width:90px;">
    <input type="text" name="rating" placeholder="rating" value="<?= htmlspecialchars($rating ?? '') ?>" style="width:90px;">
    <button type="submit">Szukaj</button>

    <?php if (!empty($q) || !empty($titleId) || !empty($rating)): ?>
      <a href="/admin/reviews">Wyczyść</a>
    <?php endif; ?>
  </form>

  <?php if (empty($reviews)): ?>
    <p>Brak wyników.</p>
  <?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
      <thead>
        <tr>
          <th>ID</th>
          <th>tytuł</th>
          <th>rating</th>
          <th>treść</th>
          <th>data</th>
          <th>akcje</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reviews as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['title_name'] ?? '') ?>
            <br>
            <small style="color:gray;">(ID: <?= (int)$r['title_id'] ?>)</small></td>
            <td><?= htmlspecialchars((string)$r['rating']) ?></td>
            <td style="max-width:520px;">
              <?= htmlspecialchars((string)$r['content']) ?>
            </td>
            <td><?= htmlspecialchars((string)$r['created_at']) ?></td>
            <td>
              <form method="post" action="/admin/reviews/delete" onsubmit="return confirm('Usunąć tę ocenę?');" style="display:inline;">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
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
