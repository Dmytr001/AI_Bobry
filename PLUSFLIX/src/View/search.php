<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PLUSFLIX – wyszukiwarka</title>
    <style>
        body { font-family: Arial; margin: 40px; }

        .error-box { padding:10px; background:#ffd7d7; border:1px solid #ff9b9b; margin: 10px 0; }

        .title { border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 10px; }
        .type { font-size: 0.9em; color: #666; }

        .title-link { text-decoration: none; color: inherit; display: block; }
        .title:hover { background-color: #f9f9f9; cursor: pointer; }

        a.btn { padding:8px 12px; background:#333; color:white; text-decoration:none; border-radius:4px; display:inline-block; }
        a.reset { padding:8px 12px; background:#ccc; color:black; text-decoration:none; border-radius:4px; display:inline-block; }

        /* NEW layout: top row (q + buttons), filters below */
        .search-form { margin: 20px 0; }

        .row-top{
            display:flex;
            gap:12px;
            align-items:center;
        }
        .row-top .q{
            flex:1;
            min-width:220px;
        }
        .actions{
            display:flex;
            gap:10px;
            margin-left:auto;
            white-space:nowrap;
        }
        .row-filters{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            margin-top:10px;
        }

        input, select, button { padding:8px; margin:0; }
    </style>
</head>
<body>

<h1>Wyszukiwarka PLUSFLIX</h1>
<p><a class="btn" href="/">← Wróć do polecanych</a></p>
<a class="btn" href="/favorites">❤️ Moje Ulubione</a>
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

<form method="get" action="/search" id="searchForm" class="search-form">
    <div class="row-top">
        <input class="q" type="text" name="q" placeholder="Nazwa" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

        <div class="actions">
            <button type="submit">Szukaj</button>
            <button type="button" onclick="copyUrl()">Kopiuj link</button>
            <a class="reset" href="/search">Usuń filtry</a>
        </div>
    </div>

    <div class="row-filters">
        <select name="category">
    <option value="">Wszystkie kategorie</option>

    <?php foreach (($allCategories ?? []) as $cat): ?>
        <?php $selected = (isset($_GET['category']) && $_GET['category'] === $cat) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $selected ?>>
                <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="type">
            <option value="">Wszystkie</option>
            <option value="film" <?= (isset($_GET['type']) && $_GET['type']=='film') ? 'selected' : '' ?>>Film</option>
            <option value="series" <?= (isset($_GET['type']) && $_GET['type']=='series') ? 'selected' : '' ?>>Serial</option>
        </select>

        <select name="platform">
        <option value="">Wszystkie platformy</option>

        <?php foreach (($allPlatforms ?? []) as $p): ?>
            <?php $selected = (isset($_GET['platform']) && $_GET['platform'] === $p) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($p) ?>" <?= $selected ?>>
                <?= htmlspecialchars($p) ?>
                </option>
            <?php endforeach; ?>
        </select>


        <select name="language">
        <option value="">Wszystkie języki</option>

            <?php foreach (($allLanguages ?? []) as $l): ?>
                <?php $selected = (isset($_GET['language']) && $_GET['language'] === $l) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($l) ?>" <?= $selected ?>>
                <?= htmlspecialchars($l) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php $sortValue = $_GET['sort'] ?? 'relevance'; ?>
        <select name="sort">
            <option value="relevance" <?= $sortValue==='relevance' ? 'selected' : '' ?>>Domyślnie</option>
            <option value="rating_desc" <?= $sortValue==='rating_desc' ? 'selected' : '' ?>>Ocena: malejąco</option>
            <option value="rating_asc" <?= $sortValue==='rating_asc' ? 'selected' : '' ?>>Ocena: rosnąco</option>
            <option value="name_asc" <?= $sortValue==='name_asc' ? 'selected' : '' ?>>Nazwa: A–Z</option>
            <option value="name_desc" <?= $sortValue==='name_desc' ? 'selected' : '' ?>>Nazwa: Z–A</option>
        </select>

        <input type="number" step="0.1" name="min_rating" placeholder="min ocena" value="<?= htmlspecialchars($_GET['min_rating'] ?? '') ?>">
        <input type="number" step="0.1" name="max_rating" placeholder="max ocena" value="<?= htmlspecialchars($_GET['max_rating'] ?? '') ?>">
    </div>
</form>

<script>
    const form = document.getElementById('searchForm');
    // Auto-apply filters (NOT the text q)
    form.querySelectorAll('.row-filters select, .row-filters input[type="number"]').forEach(el => {
        el.addEventListener('change', () => form.submit());
    });

    function copyUrl() {
        navigator.clipboard.writeText(window.location.href);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // 1. Получаем список ID из localStorage
        const favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");

        if (favorites.length > 0) {
            // 2. Ищем все ссылки на фильмы на странице
            document.querySelectorAll('.title-link[data-id]').forEach(link => {
                const currentId = link.getAttribute('data-id');

                // 3. Если ID фильма есть в массиве избранного
                if (favorites.includes(currentId)) {
                    // Находим место для иконки внутри текущей карточки
                    const placeholder = link.querySelector('.fav-icon-placeholder');
                    if (placeholder) {
                        // Вставляем некликабельную иконку
                        placeholder.innerHTML = '<span class="favorite-indicator" title="Ulubione">❤️</span>';
                    }
                }
            });
        }
    });
</script>

<?php if (!empty($errors)): ?>
    <div class="error-box">
        <?php foreach ($errors as $e): ?>
            <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($results)): ?>
    <h2>Wyniki:</h2>
    <?php foreach ($results as $title): ?>
        <a href="/title?id=<?= (int)$title['id'] ?>" class="title-link" data-id="<?= (int)$title['id'] ?>">
            <div class="title">
                <strong>
                    <?= htmlspecialchars($title['name']) ?>
                    <span class="fav-icon-placeholder"></span>
                </strong>
                <div class="type">
                    <?= htmlspecialchars($title['type']) ?> | ⭐ <?= htmlspecialchars($title['average_rating']) ?> | Kategorie: <?= htmlspecialchars($title['categories']) ?>
                </div>
                <p><?= htmlspecialchars($title['description']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <?php
    $hasAnyFilter =
            (!empty($_GET['q'])) ||
            (!empty($_GET['category'])) ||
            (!empty($_GET['type'])) ||
            (!empty($_GET['platform'])) ||
            (!empty($_GET['language'])) ||
            (isset($_GET['min_rating']) && $_GET['min_rating'] !== '') ||
            (isset($_GET['max_rating']) && $_GET['max_rating'] !== '') ||
            (!empty($_GET['sort']) && $_GET['sort'] !== 'relevance');
    ?>
    <?php if ($hasAnyFilter && empty($errors)): ?>
        <p>Brak wyników.</p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>


