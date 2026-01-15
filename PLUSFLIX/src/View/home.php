<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PLUSFLIX – Strona główna</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .card { border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 10px; }
        .meta { font-size: 0.9em; color: #666; }
        a.btn { padding:8px 12px; background:#333; color:#fff; text-decoration:none; border-radius:4px; display:inline-block; }
        a.title-link { text-decoration:none; color:inherit; display:block; }
        .card:hover { background:#f9f9f9; }
    </style>
</head>
<body>

<h1>PLUSFLIX</h1>

<p>
    <a class="btn" href="/search">Przejdź do wyszukiwarki</a>
</p>

<?php if (!empty($top5Films)): ?>
    <h2>Polecane filmy (Top 5)</h2>
    <?php foreach ($top5Films as $t): ?>
        <a href="/title?id=<?= (int)$t['id'] ?>" class="title-link">
            <div class="card">
                <strong><?= htmlspecialchars($t['name']) ?></strong>
                <div class="meta">
                    <?= htmlspecialchars($t['type']) ?> | ⭐ <?= htmlspecialchars($t['average_rating']) ?> | Kategorie: <?= htmlspecialchars($t['categories']) ?>
                </div>
                <p><?= htmlspecialchars($t['description']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($top5Series)): ?>
    <h2>Polecane seriale (Top 5)</h2>
    <?php foreach ($top5Series as $t): ?>
        <a href="/title?id=<?= (int)$t['id'] ?>" class="title-link">
            <div class="card">
                <strong><?= htmlspecialchars($t['name']) ?></strong>
                <div class="meta">
                    <?= htmlspecialchars($t['type']) ?> | ⭐ <?= htmlspecialchars($t['average_rating']) ?> | Kategorie: <?= htmlspecialchars($t['categories']) ?>
                </div>
                <p><?= htmlspecialchars($t['description']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
