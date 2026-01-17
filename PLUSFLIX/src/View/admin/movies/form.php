<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Admin – <?= $mode === 'edit' ? 'Edytuj' : 'Dodaj' ?> film</title>
</head>
<body>
  <h1><?= $mode === 'edit' ? 'Edytuj' : 'Dodaj' ?> film</h1>

  <p>
    <a href="/admin/movies">← Lista filmów</a>
  </p>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" action="<?= $mode === 'edit' ? '/admin/movies/edit' : '/admin/movies/create' ?>">
    <?php if ($mode === 'edit'): ?>
      <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
    <?php endif; ?>

    <div>
      <label>Tytuł *</label><br>
      <input type="text" name="name" required value="<?= htmlspecialchars($movie['title'] ?? '') ?>" style="width: 420px;">
    </div>
    <br>

    <div>
      <label>Typ</label><br>
      <input type="text" name="type" required value="<?= htmlspecialchars($movie['type'] ?? '') ?>" style="width: 420px;">
    </div>
    <br>

    <div>
      <label>Opis</label><br>
      <textarea name="description" rows="6" style="width: 420px;"><?= htmlspecialchars($movie['description'] ?? '') ?></textarea>
    </div>
    <br>

    <div>
      <label>Kategoria</label><br>
      <input type="text" name="categories" value="<?= htmlspecialchars((string)($movie['categories'] ?? '')) ?>" style="width: 120px;">
    </div>
    <br>

    <div>
      <label>Niedostępne w Krajach</label><br>
      <input type="text" name="blocked_countries" value="<?= htmlspecialchars((string)($movie['blocked_countries'] ?? '')) ?>" style="width: 120px;">
    </div>
    <br>

    <div>
      <label>Poster URL</label><br>
      <input type="text" name="image_path" value="<?= htmlspecialchars($movie['image_path'] ?? '') ?>" style="width: 420px;">
    </div>
    <br>

    <button type="submit"><?= $mode === 'edit' ? 'Zapisz zmiany' : 'Dodaj film' ?></button>
  </form>
</body>
</html>
