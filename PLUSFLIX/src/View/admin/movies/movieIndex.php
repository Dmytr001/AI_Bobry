<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Produkcje</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="admin-page">

<?php require __DIR__ . '/../../partials/topbar.php'; ?>
<?php $active = 'movies'; require __DIR__ . '/../partials/nav.php'; ?>

<h1 class="admin-title">Produkcje: szukaj</h1>

<form class="admin-toolbar" method="get" action="/admin/movies">
    <input type="text" name="q" placeholder="Wyszukiwanie..." value="<?= htmlspecialchars($q ?? '') ?>">
    <button class="admin-tool-btn" type="submit">Szukaj</button>

    <?php if (!empty($q)): ?>
        <a class="admin-tool-link" href="/admin/movies">Wyczyść</a>
    <?php endif; ?>
</form>

<?php if (empty($movies)): ?>
    <p class="admin-muted">Brak filmów.</p>
<?php else: ?>
    <table class="admin-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tytuł</th>
            <th>Typ</th>
            <th>Ocena</th>
            <th>Akcje</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($movies as $m): ?>
            <tr>
                <td><?= (int)$m['id'] ?></td>
                <td><?= htmlspecialchars($m['name'] ?? '') ?></td>
                <td><?= htmlspecialchars($m['type'] ?? '') ?></td>
                <td><?= htmlspecialchars((string)($m['average_rating'] ?? '')) ?></td>
                <td class="admin-table__actions">
                    <a class="admin-small-link" href="/admin/movies/edit?id=<?= (int)$m['id'] ?>">Edytuj</a>

                    <form method="post" action="/admin/movies/delete" style="display:inline;"
                          onsubmit="return confirm('Na pewno usunąć film?')">
                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                        <button class="admin-small-danger" type="submit">Usuń</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</main></div>
</body>
</html>
