<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Ocene</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="admin-page">

<?php require __DIR__ . '/../../partials/topbar.php'; ?>
<?php $active = 'reviews'; require __DIR__ . '/../partials/nav.php'; ?>

<h1 class="admin-title">Ocene: szukaj</h1>

<form class="admin-toolbar admin-toolbar--reviews" method="get" action="/admin/reviews">
    <input type="text" name="q" placeholder="Szukaj po treści..." value="<?= htmlspecialchars($q ?? '') ?>">
    <input type="text" name="title_id" placeholder="title_id" value="<?= htmlspecialchars($titleId ?? '') ?>">
    <input type="text" name="rating" placeholder="rating" value="<?= htmlspecialchars($rating ?? '') ?>">
    <button class="admin-tool-btn" type="submit">Szukaj</button>

    <?php if (!empty($q) || !empty($titleId) || !empty($rating)): ?>
        <a class="admin-tool-link" href="/admin/reviews">Wyczyść</a>
    <?php endif; ?>
</form>

<?php if (empty($reviews)): ?>
    <p class="admin-muted">Brak wyników.</p>
<?php else: ?>
    <table class="admin-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tytuł</th>
            <th>Rating</th>
            <th>Treść</th>
            <th>Data</th>
            <th>Akcje</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reviews as $r): ?>
            <tr>
                <td><?= (int)($r['id'] ?? 0) ?></td>
                <td>
                    <?= htmlspecialchars($r['title_name'] ?? 'Brak tytułu') ?><br>

                    <?php $tid = (int)($r['title_id'] ?? 0); ?>
                    <small class="admin-muted">
                        <?= $tid ? ('ID: ' . $tid) : '' ?>
                    </small>
                </td>

                <td><?= htmlspecialchars((string)($r['rating'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string)($r['content'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string)($r['created_at'] ?? '')) ?></td>
                <td class="admin-table__actions">
                    <form method="post" action="/admin/reviews/delete"
                          onsubmit="return confirm('Usunąć tę ocenę?')" style="display:inline;">
                        <input type="hidden" name="id" value="<?= (int)($r['id'] ?? 0) ?>">
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
