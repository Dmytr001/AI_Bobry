<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje Ulubione – PLUSFLIX</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .title { border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 10px; }
        .type { font-size: 0.9em; color: #666; }
        /* Скрываем всё по умолчанию */
        .title-link { text-decoration: none; color: inherit; display: none; }
        .title:hover { background-color: #f9f9f9; cursor: pointer; }
        a.btn { padding:8px 12px; background:#333; color:white; text-decoration:none; border-radius:4px; display:inline-block; }
        .favorite-indicator { color: #e74c3c; margin-left: 8px; }
        .empty-state { display: none; padding: 40px; border: 2px dashed #ccc; text-align: center; color: #666; margin-top: 20px; }
    </style>
</head>
<body>

<h1>Twoje Ulubione ❤️</h1>
<p>
    <a class="btn" href="/">← Strona główna</a>
    <a class="btn" href="/search">Wyszukiwarka</a>
</p>

<p>
    <?php if (empty($_SESSION['admin_id'])): ?>
        <a class="btn" href="/admin/login">Zaloguj (admin)</a>
    <?php else: ?>
    <a class="btn" href="/admin">Panel admina</a>

<form method="post" action="/admin/logout" style="display:inline;">
    <button type="submit" class="btn">Wyloguj</button>
</form>

<span style="margin-left:10px; color:#666;">
        Zalogowano jako: <?= htmlspecialchars($_SESSION['admin_login'] ?? '') ?>
    </span>
<?php endif; ?>
</p>

<div id="favoritesList">
    <?php if (!empty($results)): ?>
        <?php foreach ($results as $title): ?>
            <a href="/title?id=<?= (int)$title['id'] ?>" class="title-link" data-id="<?= (int)$title['id'] ?>">
                <div class="title">
                    <strong>
                        <?= htmlspecialchars($title['name']) ?>
                        <span class="favorite-indicator">❤️</span>
                    </strong>
                    <div class="type">
                        <?= htmlspecialchars($title['type']) ?> | ⭐ <?= htmlspecialchars($title['average_rating']) ?>
                    </div>
                    <p><?= htmlspecialchars($title['description']) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>

    <div id="noFavorites" class="empty-state">
        <p>Twoja lista ulubionych jest obecnie pusta.</p>
        <a href="/search" style="color: #007bff;">Znajdź filmy и seriale</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");
        const cards = document.querySelectorAll('.title-link');
        let count = 0;

        cards.forEach(card => {
            const id = card.getAttribute('data-id');
            if (favorites.includes(id)) {
                card.style.display = 'block';
                count++;
            }
        });

        if (count === 0) {
            document.getElementById('noFavorites').style.display = 'block';
        }
    });
</script>

</body>
</html>