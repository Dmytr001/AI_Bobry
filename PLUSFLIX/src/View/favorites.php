<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje Ulubione – PLUSFLIX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;900&display=swap" rel="stylesheet">
    <style>
        /* БАЗОВЫЕ СТИЛИ */
        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            background-color: #000;
            color: #fff;
        }

        header.header-1 {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            /* Измени второе число (сейчас там 24px) */
            padding: 24px 64px 24px 64px;
            width: 100%;
            height: 63px;
            background: #000000;
            box-sizing: border-box;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* LOGO */
        .logo-group {
            display: flex;
            flex-direction: row;
            align-items: center;
            text-decoration: none;
            width: 304px;
        }

        .logo-rect {
            width: 34px;
            height: 37px;
            background: url('logo.png') no-repeat center; /* Сюда подгружай файл */
            background-size: contain;
            background-color: #878787; /* Заглушка */
            margin-right: 10px;
            flex-shrink: 0;
        }
        .logo-text {
            font-weight: 900;
            font-size: 32px;
            line-height: 110%;
            letter-spacing: -0.03em;
            color: #FFFFFF;
            text-transform: uppercase;
        }

        /* SEARCH BAR (Точно 405x36) */
        .search-container {
            width: 405px;
            height: 36px;
            background: #D9D9D9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            padding: 0 15px;
        }
        .search-input {
            background: transparent;
            border: none;
            width: 100%;
            font-family: 'Inter';
            font-weight: 500;
            font-size: 16px;
            color: #000;
            outline: none;
        }

        /* NAV GROUP */
        .nav-group {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 14px;
        }

        /* Кнопки 102x32 */
        .btn-figma {
            width: 102px;
            height: 32px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-family: 'Inter';
            font-weight: 500;
            font-size: 16px;
            border: none;
            cursor: pointer;
            background: #878787;
            color: #fff;
        }

        /* Переключатель темы (Component 6) */
        .theme-toggle-btn {
            width: 27px;
            height: 26px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            position: relative;
        }
        .theme-circle-white {
            position: absolute;
            width: 20px;
            height: 19px;
            background: #FFFFFF;
            border-radius: 50%;
            left: 4px;
            top: 3px;
        }
        .theme-circle-black {
            position: absolute;
            width: 14px;
            height: 14px;
            background: #000000;
            border-radius: 50%;
            left: 2px;
            top: 3px;
        }

        /* КОНТЕНТ */
        .content-body { padding: 40px 64px; }
        .title-page { font-size: 32px; font-weight: 900; margin-bottom: 24px; }

        /* Твои старые стили для карточек (без изменений) */
        .title-link { text-decoration: none; color: inherit; display: none; }
        .title { border-bottom: 1px solid #333; margin-bottom: 15px; padding: 15px; }
        .type { font-size: 0.9em; color: #878787; }
        .favorite-indicator { color: #e74c3c; margin-left: 8px; }
        .empty-state { display: none; padding: 40px; border: 2px dashed #333; text-align: center; color: #666; }
    </style>
</head>
<body>

<header class="header-1">
    <a href="/" class="logo-group">
        <div class="logo-rect"></div>
        <span class="logo-text">PLUSFLIX</span>
    </a>

    <div class="search-container">
        <form action="/search" method="get" style="width: 100%; display: flex;">
            <input type="text" name="q" class="search-input" placeholder="Wyszukiwanie…">
        </form>
    </div>

    <nav class="nav-group">
        <?php if (empty($_SESSION['admin_id'])): ?>
            <a href="/admin/login" class="btn-figma">Login</a>
        <?php else: ?>
            <a href="/admin" class="btn-figma" style="width: auto; padding: 0 10px;">Admin</a>
            <form method="post" action="/admin/logout" style="display:inline;">
                <button type="submit" class="btn-figma" style="margin-left: 5px;">Logout</button>
            </form>
        <?php endif; ?>

        <button class="theme-toggle-btn" onclick="toggleTheme()">
            <div class="theme-circle-white">
                <div class="theme-circle-black"></div>
            </div>
        </button>
    </nav>
</header>

<div class="content-body">
    <h1 class="title-page">Twoje Ulubione ❤️</h1>

    <?php if (!empty($_SESSION['admin_login'])): ?>
        <p style="color: #878787; font-size: 14px; margin-top: -20px; margin-bottom: 20px;">
            Zalogowano jako: <?= htmlspecialchars($_SESSION['admin_login']) ?>
        </p>
    <?php endif; ?>

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
            <a href="/search" class="search-container" style="text-decoration: none;">
                <span class="search-input" style="display: flex; align-items: center;">Wyszukiwanie…</span>
            </a>
    </div>
</div>

<script>
    // ТВОЙ ОРИГИНАЛЬНЫЙ СКРИПТ (БЕЗ ИЗМЕНЕНИЙ)
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

    // Функция переключения темы (только визуал)
    function toggleTheme() {
        const isDark = document.body.style.backgroundColor === 'white';
        document.body.style.backgroundColor = isDark ? 'black' : 'white';
        document.body.style.color = isDark ? 'white' : 'black';
    }
</script>

</body>
</html>