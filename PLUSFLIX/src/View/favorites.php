<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLUSFLIX – Twoje Ulubione</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Ukrywamy karty domyślnie - JS je pokaże tylko jeśli są w localStorage */
        .card {
            display: none;
        }

        .empty-state {
            display: none;
            text-align: center;
            padding: 100px 20px;
            color: var(--theme-text);
        }

        .section-header-fav {
            margin: 40px 0 20px 0;
        }
    </style>
</head>

<body>

    <header class="navbar">
        <a href="/" class="logo-link" aria-label="PLUSFLIX">
            <img src="/images/logo.png" alt="PLUSFLIX" class="logo-img">
            <span class="logo-text">PLUSFLIX</span>
        </a>

        <div class="nav-actions">
            <input type="text" class="search-input" placeholder="Wyszukiwanie...">
            <?php if (empty($_SESSION['admin_id'])): ?>
                <a href="/admin/login" class="btn btn-login">Login</a>
            <?php else: ?>
                <a href="/admin" class="btn btn-login">Panel Admina</a>
            <?php endif; ?>
            <a href="/" class="btn btn-fav">Strona Główna</a>
            <button class="theme-toggle-btn" id="themeToggle" type="button" aria-label="Toggle theme">🌓</button>
        </div>
    </header>

    <div class="container">

        <div class="section-header-fav">
            <h2 class="section-title">Twoje <span>Ulubione</span> ❤️</h2>
        </div>

        <div id="favoritesGrid" class="movie-grid">
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $t): ?>
                    <a href="/title?id=<?= (int) $t['id'] ?>" class="card" data-id="<?= (int) $t['id'] ?>">

                        <?php
                        $img = !empty($t['imagepath']) ? $t['imagepath'] : 'https://via.placeholder.com/300x450';
                        // Wymuszamy ścieżkę od katalogu głównego
                        if (!str_starts_with($img, 'http') && !str_starts_with($img, '/')) {
                            $img = '/' . $img;
                        }
                        ?>

                        <div class="card-img" style="background-image: url('<?= htmlspecialchars($img) ?>')">
                            <div class="rating"><span>★</span> <?= number_format($t['average_rating'], 1) ?>/5</div>
                        </div>

                        <div class="card-info">
                            <span class="card-name"><?= htmlspecialchars($t['name']) ?></span>

                            <div class="badges-container">
                                <div class="badge-list">
                                    <?php
                                    if (!empty($t['categories'])):
                                        $tags = explode(',', $t['categories']);
                                        foreach (array_slice($tags, 0, 2) as $tag): ?>
                                            <span class="badge"><?= htmlspecialchars(trim($tag)) ?></span>
                                        <?php endforeach;
                                    endif; ?>
                                </div>
                                <div class="badge-list">
                                    <span class="badge">Eng</span>
                                    <span class="badge">Pl</span>
                                </div>
                                <div class="badge-list">
                                    <span class="badge">Disney+</span>
                                    <span class="badge">Netflix</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="noFavorites" class="empty-state">
            <h2>Twoja lista jest pusta</h2>
            <p style="margin-bottom: 20px;">Nie dodałeś jeszcze żadnych filmów do ulubionych.</p>
            <a href="/search" class="btn-hero">Szukaj filmów</a>
        </div>

    </div>

    <footer class="footer">
        <div class="footer-col">
            <h3 class="logo" style="color: white;">Namely</h3>
            <p>Descriptive line about what your company does.</p>
            <div class="social-icons">
                <span>📷</span>
                <span>🔗</span>
                <span>✖</span>
            </div>
        </div>
        <div class="footer-col">
            <h4>Features</h4>
            <ul>
                <li>Core features</li>
                <li>Pro experience</li>
                <li>Integrations</li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Learn more</h4>
            <ul>
                <li>Blog</li>
                <li>Case studies</li>
                <li>Best practices</li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Support</h4>
            <ul>
                <li>Contact</li>
                <li>Support</li>
                <li>Legal</li>
            </ul>
        </div>
    </footer>

    <script>
        // Logika sprawdzania ulubionych z localStorage
        function loadFavorites() {
            const favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");
            const cards = document.querySelectorAll('.card[data-id]');
            let found = 0;

            cards.forEach(card => {
                const currentId = card.getAttribute('data-id');
                if (favorites.includes(currentId)) {
                    card.style.display = 'flex';
                    found++;
                }
            });

            if (found === 0) {
                document.getElementById('noFavorites').style.display = 'block';
            }
        }

        window.onload = loadFavorites;

        // Theme toggle (kopiuj-wklej z Twojego home.php)
        (function () {
            const key = 'plusflix-theme';
            const btn = document.getElementById('themeToggle');

            function syncIcon() {
                if (!btn) return;
                btn.textContent = document.body.classList.contains('light-mode') ? '☀️' : '🌙';
            }

            const saved = localStorage.getItem(key);
            if (saved === 'light') document.body.classList.add('light-mode');
            syncIcon();

            if (btn) {
                btn.addEventListener('click', () => {
                    document.body.classList.toggle('light-mode');
                    localStorage.setItem(key, document.body.classList.contains('light-mode') ? 'light' : 'dark');
                    syncIcon();
                });
            }
        })();
    </script>

</body>

</html>