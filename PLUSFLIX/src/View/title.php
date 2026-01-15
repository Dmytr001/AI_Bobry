<?php
$title = $title ?? null;
$errors = $errors ?? [];
$reviews = $reviews ?? [];
$success = $success ?? null;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Szczegóły</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .error-box { padding:10px; background:#ffd7d7; border:1px solid #ff9b9b; margin: 10px 0; }
        .success-box { padding:10px; background:#d7ffe1; border:1px solid #7fd69a; margin: 10px 0; }
        .review { border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px; }
        textarea { width: 100%; max-width: 700px; }
        input, select, button, textarea { padding: 8px; margin-bottom: 10px; }
    </style>
</head>
<body>

<a href="/">← Wróć</a>

<?php if (!empty($errors)): ?>
    <div class="error-box">
        <?php foreach ($errors as $e): ?>
            <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="success-box"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($title): ?>
    <h1><?= htmlspecialchars($title['name']) ?></h1>
    <div><?= htmlspecialchars($title['type']) ?> | ⭐ <?= htmlspecialchars($title['average_rating']) ?></div>
    <p><?= htmlspecialchars($title['description']) ?></p>

    <h2>Dodaj ocenę i komentarz</h2>
    <form method="post" action="/title?id=<?= (int)$title['id'] ?>">
        <label>Ocena (1–5):</label><br>
        <select name="rating">
            <?php for ($i=1; $i<=5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select><br>

        <label>Komentarz / recenzja:</label><br>
        <textarea name="content" rows="5" placeholder="Napisz komentarz..."></textarea><br>

        <button type="submit">Dodaj</button>
    </form>

    <h2>Komentarze</h2>
    <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $r): ?>
            <div class="review">
                <div>⭐ <?= htmlspecialchars($r['rating']) ?> | <?= htmlspecialchars($r['created_at']) ?></div>
                <div><?= nl2br(htmlspecialchars($r['content'])) ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Brak komentarzy.</p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
