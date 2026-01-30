<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLUSFLIX – Znajdź idealny film</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<header class="navbar">
    <a href="/" class="logo-link" aria-label="PLUSFLIX">
        <img src="/images/logo.png" alt="PLUSFLIX" class="logo-img">
        <span class="logo-text">PLUSFLIX</span>
    </a>

    <div class="search-wrapper">
        <form action="/search" method="get" class="search-form">
            <div class="search-container-inner">
                <input type="text" name="q" class="search-input-active" placeholder="Wyszukiwanie...">

                <input type="hidden" name="type" value="">
                <input type="hidden" name="category" value="">
                <input type="hidden" name="platform" value="">
                <input type="hidden" name="language" value="">
                <input type="hidden" name="minrating" value="">
                <input type="hidden" name="maxrating" value="">
                <input type="hidden" name="sort" value="relevance">

                <button type="submit" class="search-submit-btn" aria-label="Szukaj">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <div class="nav-actions">
        <?php if (empty($_SESSION['admin_id'])): ?>
            <a href="javascript:void(0)" onclick="openLoginModal()" class="btn btn-login">Login</a>
        <?php else: ?>
            <a href="/admin" class="btn btn-login">Panel Admina</a>
        <?php endif; ?>

        <a href="/favorites" class="btn btn-fav" aria-label="Ulubione">
            <span class="btn-text">Ulubione</span>
            <svg class="heart-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </a>
        <button class="theme-toggle-btn" id="themeToggle" type="button" aria-label="Toggle theme">🌓</button>
    </div>
</header>

<section class="hero">
    <div class="hero-content">
        <p class="hero-subtitle" style="color: #ff0000">Rozpocznij wyszukiwanie</p>
        <h1 class="hero-title">Znajdź idealny film na dziś</h1>
        <a href="/search" class="btn-hero">
            Rozpocznij wyszukiwanie

            <svg class="icon-search" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </a>
    </div>
</section>

<div class="container">

    <?php if (!empty($newestTitles)): ?>
        <h2 class="section-title">Nowości w <span>PLUSFLIX</span></h2>
        <div class="movie-grid">
            <?php foreach ($newestTitles as $t): ?>
                <a href="/title?id=<?= (int)$t['id'] ?>" class="card" data-id="<?= (int)$t['id'] ?>">
                    <div class="card-img" style="background-image: url('<?= !empty($t['image_path']) ? htmlspecialchars($t['image_path']) : 'https://via.placeholder.com/300x450' ?>')">
                        <div class="rating"><span>★</span> <?= number_format($t['average_rating'], 1) ?>/5</div>
                        <div class="fav-badge" title="Ulubione">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </div>                    </div>

                    <div class="card-info">
                        <span class="card-name"><?= htmlspecialchars($t['name']) ?></span>
                        <p class="card-desc"><?= htmlspecialchars($t['description']) ?></p>

                        <div class="badges-container" style="display: flex; flex-direction: column; gap: 6px; width: 100%; margin-top: auto;">

                            <?php if (!empty($t['categories'])): ?>
                                <div class="badge-row-fill">
                                    <?php foreach (array_slice(array_map('trim', explode(',', $t['categories'])), 0, 3) as $tag): ?>
                                        <span class="badge"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($t['languages_list'])): ?>
                                <div class="badge-row-fill">
                                    <?php foreach (array_slice($t['languages_list'], 0, 3) as $lang): ?>
                                        <span class="badge"><?= htmlspecialchars($lang) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($t['platforms_list'])): ?>
                                <div class="badge-row-fill">
                                    <?php foreach (array_slice($t['platforms_list'], 0, 3) as $plat): ?>
                                        <span class="badge color-platform"><?= htmlspecialchars($plat) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($trendyTitles)): ?>
            <h2 class="section-title">Trendy tygodnia 🔥</h2>
        <div class="movie-grid">
            <?php foreach ($trendyTitles as $t): ?>
                <a href="/title?id=<?= (int)$t['id'] ?>" class="card" data-id="<?= (int)$t['id'] ?>">
                    <div class="card-img" style="background-image: url('<?= !empty($t['image_path']) ? htmlspecialchars($t['image_path']) : 'https://via.placeholder.com/300x450' ?>')">
                        <div class="rating"><span>★</span> <?= number_format($t['average_rating'], 1) ?>/5</div>
                        <div class="fav-badge" title="Ulubione">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="card-info">
                        <span class="card-name"><?= htmlspecialchars($t['name']) ?></span>
                        <p class="card-desc"><?= htmlspecialchars($t['description']) ?></p>

                        <div class="badges-container" style="display: flex; flex-direction: column; gap: 6px; width: 100%; margin-top: auto;">

                            <?php if (!empty($t['categories'])): ?>
                                <div class="badge-row-fill">
                                    <?php foreach (array_slice(array_map('trim', explode(',', $t['categories'])), 0, 3) as $tag): ?>
                                        <span class="badge"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($t['languages_list'])): ?>
                                <div class="badge-row-fill">
                                    <?php foreach (array_slice($t['languages_list'], 0, 3) as $lang): ?>
                                        <span class="badge"><?= htmlspecialchars($lang) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($t['platforms_list'])): ?>
                                <div class="badge-row-fill">
                                    <?php foreach (array_slice($t['platforms_list'], 0, 3) as $plat): ?>
                                        <span class="badge color-platform"><?= htmlspecialchars($plat) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($topRatedTitles)): ?>
        <h2 class="section-title">Najwyżej oceniane</h2>
        <div class="movie-grid">
            <?php foreach ($topRatedTitles as $t): ?>
                <a href="/title?id=<?= (int)$t['id'] ?>" class="card" data-id="<?= (int)$t['id'] ?>">
                    <div class="card-img" style="background-image: url('<?= !empty($t['image_path']) ? htmlspecialchars($t['image_path']) : 'https://via.placeholder.com/300x450' ?>')">
                        <div class="rating"><span>★</span> <?= number_format($t['average_rating'], 1) ?>/5</div>
                        <div class="fav-badge" title="Ulubione">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="card-info">
                        <span class="card-name"><?= htmlspecialchars($t['name']) ?></span>
                        <p class="card-desc"><?= htmlspecialchars($t['description']) ?></p>

                        <div class="badges-container" style="display: flex; flex-direction: column; gap: 6px; width: 100%; margin-top: auto;">

                            <?php if (!empty($t['categories'])): ?>
                                <div class="badge-row-fill">
                                    <?php foreach (array_slice(array_map('trim', explode(',', $t['categories'])), 0, 3) as $tag): ?>
                                        <span class="badge"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($t['languages_list'])): ?>
                                <div class="badge-row-fill">
                                    <?php foreach (array_slice($t['languages_list'], 0, 3) as $lang): ?>
                                        <span class="badge"><?= htmlspecialchars($lang) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($t['platforms_list'])): ?>
                                <div class="badge-row-fill">
                                    <?php foreach (array_slice($t['platforms_list'], 0, 3) as $plat): ?>
                                        <span class="badge color-platform"><?= htmlspecialchars($plat) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
    function checkFavorites() {
        const favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");

        document.querySelectorAll('.card[data-id]').forEach(card => {
            const currentId = card.getAttribute('data-id');
            const favBadge = card.querySelector('.fav-badge');

            if (favBadge) {
                // Если ID фильма есть в массиве избранного — показываем красный квадрат
                if (favorites.includes(currentId)) {
                    favBadge.style.display = 'flex';
                } else {
                    favBadge.style.display = 'none';
                }
            }
        });
    }

    // Запуск при загрузке страницы
    window.addEventListener('load', checkFavorites);

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

    document.addEventListener('DOMContentLoaded', () => {
        const loginForm = document.getElementById('ajaxLoginForm');
        const errorDiv = document.getElementById('loginError');

        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Останавливаем перезагрузку страницы

                errorDiv.style.display = 'none'; // Скрываем прошлые ошибки
                const formData = new FormData(this);

                // Отправляем данные на ваш существующий обработчик
                fetch('/admin/login', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Помечаем запрос как AJAX
                    }
                })
                    .then(response => {
                        // Если сервер сделал редирект (код 302), Fetch сам пойдет по нему
                        // Если URL изменился на /admin..., значит вход успешен
                        if (response.url.includes('/admin') && !response.url.includes('login')) {
                            window.location.href = response.url; // Переходим в админку
                        } else {
                            // Если мы остались на странице логина — значит данные неверны
                            showError("Błędny login lub hasło");
                        }
                    })
                    .catch(error => {
                        showError("Błąd połączenia z serwerem");
                    });
            });
        }

        function showError(text) {
            errorDiv.textContent = text;
            errorDiv.style.display = 'block';
            // Легкая тряска окна при ошибке
            const card = document.querySelector('.admin-login-card');
            card.style.animation = 'none';
            card.offsetHeight; /* trigger reflow */
            card.style.animation = 'shake 0.4s';
        }
    });

    // Функции открытия и закрытия
    function openLoginModal() {
        document.getElementById('loginModal').style.display = 'flex';
        document.getElementById('loginError').style.display = 'none';
    }

    function closeLoginModal() {
        document.getElementById('loginModal').style.display = 'none';
    }
</script>

<footer class="pf-footer">
    <div class="pf-footer__inner">
        <div class="pf-footer__brand">
            <div class="pf-footer__logo">Namely</div>
            <div class="pf-footer__desc">Descriptive line about what your company does.</div>

            <div class="pf-footer__social">
                <a class="pf-footer__icon" href="#" aria-label="Instagram">
                    <svg class="pf-ico" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                    </svg>
                </a>

                <a class="pf-footer__icon" href="#" aria-label="LinkedIn">in</a>
                <a class="pf-footer__icon" href="#" aria-label="X">X</a>
            </div>
        </div>

        <div class="pf-footer__cols">
            <div class="pf-footer__col">
                <div class="pf-footer__title">Features</div>
                <a class="pf-footer__link" href="#">Core features</a>
                <a class="pf-footer__link" href="#">Pro experience</a>
                <a class="pf-footer__link" href="#">Integrations</a>
            </div>

            <div class="pf-footer__col">
                <div class="pf-footer__title">Learn more</div>
                <a class="pf-footer__link" href="#">Blog</a>
                <a class="pf-footer__link" href="#">Case studies</a>
                <a class="pf-footer__link" href="#">Customer stories</a>
                <a class="pf-footer__link" href="#">Best practices</a>
            </div>

            <div class="pf-footer__col">
                <div class="pf-footer__title">Support</div>
                <a class="pf-footer__link" href="#">Contact</a>
                <a class="pf-footer__link" href="#">Support</a>
                <a class="pf-footer__link" href="#">Legal</a>
            </div>
        </div>
    </div>
</footer>

<div id="loginModal" class="admin-login-backdrop">
    <div class="admin-login-card admin-login-anim">
        <button class="admin-login-close" onclick="closeLoginModal()">&times;</button>
        <h2 class="admin-login-title">Admin Login</h2>

        <div id="loginError"></div>

        <form id="ajaxLoginForm">
            <input type="hidden" name="return" value="/admin/movies">
            <input class="admin-login-input" type="text" name="login" placeholder="Imię" required>
            <input class="admin-login-input" type="password" name="password" placeholder="Hasło" required>
            <button class="admin-login-btn" type="submit">Login</button>
        </form>
    </div>
</div>

</body>
</html>