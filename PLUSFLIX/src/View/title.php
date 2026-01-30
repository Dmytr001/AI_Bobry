<?php
$title = $title ?? null;
$errors = $errors ?? [];
$reviews = $reviews ?? [];
$success = $success ?? null;
$platforms = $platforms ?? [];
$languages = $languages ?? [];
$episodes = $episodes ?? [];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ? htmlspecialchars($title['name']) : 'Szczegóły' ?> – PLUSFLIX</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="title.css">
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

<?php if ($title): ?>
    <section class="movie-hero" style="--hero-img: url('<?= htmlspecialchars($title['image_path']) ?>');">
       <div class="hero-container">

            <div class="hero-flex-layout" style="justify-content: space-between; align-items: flex-start; width: 100%;">
                <div class="rating-badge-hero" >
                    <span style="color: #ffcc00;">★</span>
                    <span style="color: #fff;"><?= number_format((float) $title['average_rating'], 1) ?></span><span style="color: rgba(255,255,255,0.6); font-size: 0.8em;">/ 5</span>
                </div>

                <button class="btn-back" onclick="window.location.href='/search'" style="margin: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Powrót</span>
                </button>
            </div>

            <div class="hero-info-box">
                <div class="hero-title-row" style="margin: 0;">
                    <h1 class="movie-title-large" style="font-size: 3.5rem; margin: 0; line-height: 1;"><?= htmlspecialchars($title['name']) ?></h1>
                </div>

                <div class="hero-actions" style="margin: 0;">
                    <button id="favoriteBtn" class="btn-hero-fav" onclick="toggleFavorite()">
                        <span id="favText">Dodaj do ulubionych</span>
                    </button>
                </div>
            </div>

        </div>
    </section>

    <main class="title-container-v2">
        <div class="main-info-grid">

            <div class="grid-description">
                <div class="description-header-tags">
                    <span class="type-tag-pill"><?= htmlspecialchars($title['type'] === 'series' ? 'Serial' : 'Film') ?></span>
                    <?php if (!empty($title['categories'])):
                        foreach (explode(',', $title['categories']) as $cat): ?>
                            <span class="genre-tag-pill"><?= htmlspecialchars(trim($cat)) ?></span>
                        <?php endforeach;
                    endif; ?>
                </div>

                <h2 class="grid-title">O czym jest „<?= htmlspecialchars($title['name']) ?>”</h2>
                <p class="movie-description-text">
                    <?= nl2br(htmlspecialchars($title['description'])) ?>
                </p>
            </div>

            <div class="grid-sidebar">

                <div class="sidebar-top">
                    <?php if (!empty($platforms)): ?>
                        <div class="detail-group-card">
                            <h3>Gdzie oglądać</h3>
                            <div class="platform-grid-compact">
                                <?php foreach ($platforms as $p): ?>
                                    <a href="<?= htmlspecialchars($p['watch_link']) ?>" class="platform-pill" target="_blank">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="sidebar-bottom">
                    <?php if (!empty($languages)): ?>
                        <div class="detail-group-card">
                            <h3>Dostępne języki</h3>
                            <div class="badge-row-compact">
                                <?php foreach ($languages as $lang): ?>
                                    <span class="badge-lang-pill"><?= htmlspecialchars($lang['name']) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

            <?php if ($title['type'] === 'series' && !empty($episodes)): ?>
                <section class="episodes-section">
                    <h2 class="section-title-red">Odcinki</h2>
                    <div class="episodes-scroll-container">
                        <?php foreach ($episodes as $ep): ?>
                            <div class="episode-card" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('<?= htmlspecialchars($title['image_path']) ?>');">
                                <div class="episode-number-overlay"><?= (int) $ep['episode_number'] ?></div>
                                <div class="episode-info-hover">
                                    <span class="ep-name-hover"><?= htmlspecialchars($ep['name']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <section class="reviews-section">
                <div class="section-header">
                    <div class="rating-input-wrapper">
                        <div class="star-rating-action" id="starRatingAction">
                            <span class="star-clickable" data-value="5">★</span>
                            <span class="star-clickable" data-value="4">★</span>
                            <span class="star-clickable" data-value="3">★</span>
                            <span class="star-clickable" data-value="2">★</span>
                            <span class="star-clickable" data-value="1">★</span>
                        </div>
                    </div>
                </div>

                <div id="reviews-container" class="reviews-grid">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $r):
                            $contentTrim = trim((string) ($r['content'] ?? ''));
                            if ($contentTrim === '') continue;
                            $rating = (int)$r['rating'];
                            ?>
                            <div class="review-card" id="rev-<?= (int) $r['id'] ?>" onclick="handleReviewClick(<?= (int) $r['id'] ?>, <?= (float) $r['rating'] ?>)">
                                <div class="review-header">
                                    <div class="my-review-badge" id="badge-<?= (int) $r['id'] ?>">Twoja opinia</div>
                                    <div class="review-stars-row">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star-static <?= $i <= $rating ? 'filled' : '' ?>">★</span>
                                        <?php endfor; ?>
                                        <span class="rating-number-hidden" id="rat-<?= (int) $r['id'] ?>" style="display:none;"><?= $rating ?></span>
                                    </div>
                                </div>
                                <div class="review-content-body">
                                    <p id="cont-<?= (int) $r['id'] ?>"><?= nl2br(htmlspecialchars($r['content'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <div id="reviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modalTitle">Dodaj opinię</h2>
                    <span class="close-modal" onclick="closeModal()">&times;</span>
                </div>
                <form method="POST" action="/title?id=<?= (int) $title['id'] ?>">
                    <input type="hidden" name="review_id" id="field_id">
                    <div class="form-group">
                        <input type="hidden" name="rating" id="field_rating" value="5">

                        <div class="star-rating-modal" id="modalStarRating">
                            <span class="star-modal-item" data-value="5">★</span>
                            <span class="star-modal-item" data-value="4">★</span>
                            <span class="star-modal-item" data-value="3">★</span>
                            <span class="star-modal-item" data-value="2">★</span>
                            <span class="star-modal-item" data-value="1">★</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="content" id="field_content" rows="5" placeholder="Twoja opinia..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-submit">Zapisz</button>
                        <button type="button" id="delete_btn" onclick="deleteReview()" class="btn-delete">Usuń</button>
                        <button type="button" class="btn-cancel" onclick="closeModal()">Anuluj</button>
                    </div>
                </form>
            </div>
        </div>

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


        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const currentTitleId = urlParams.get('id');
            const titleId = "<?= (int) $title['id'] ?>";

            const newIdFromUrl = urlParams.get('new_id');
            if (newIdFromUrl && currentTitleId) {
                localStorage.setItem('my_review_for_title_' + currentTitleId, newIdFromUrl);
                localStorage.setItem('my_review_' + newIdFromUrl, 'true');
                window.history.replaceState({}, '', window.location.origin + window.location.pathname + "?id=" + currentTitleId);
            }

            document.querySelectorAll('.review-card').forEach(box => {
                const rid = box.id.replace('rev-', '');
                if (localStorage.getItem('my_review_' + rid)) {
                    const badge = document.getElementById('badge-' + rid);
                    if (badge) badge.style.display = 'inline-block';
                }
            });

            function handleMainButtonClick() {
                const existingReviewId = localStorage.getItem('my_review_for_title_' + currentTitleId);
                if (existingReviewId) { handleReviewClick(existingReviewId, 0); } else { openModal(); }
            }

            // Функция вызова кастомного подтверждения
            function showConfirm(title, message) {
                return new Promise((resolve) => {
                    const modal = document.getElementById('confirmModal');
                    document.getElementById('confirmTitle').innerText = title;
                    document.getElementById('confirmMessage').innerText = message;

                    modal.style.display = 'flex';

                    const yesBtn = document.getElementById('confirmYes');
                    const noBtn = document.getElementById('confirmNo');

                    const cleanUp = () => {
                        modal.style.display = 'none';
                        yesBtn.onclick = null;
                        noBtn.onclick = null;
                    };

                    yesBtn.onclick = () => { cleanUp(); resolve(true); };
                    noBtn.onclick = () => { cleanUp(); resolve(false); };
                });
            }

            function closeConfirmModal() {
                document.getElementById('confirmModal').style.display = 'none';
            }

            // ОБНОВЛЕННАЯ функция клика по отзыву
            async function handleReviewClick(id, rating) {
                const isMyReview = localStorage.getItem('my_review_' + id) || (localStorage.getItem('my_review_for_title_' + currentTitleId) == id);
                if (!isMyReview) return;

                const confirmed = await showConfirm("Edycja opinii", "Czy chcesz edytować swoją opinię?");

                if (confirmed) {
                    const contElem = document.getElementById('cont-' + id);
                    const content = (contElem && !contElem.querySelector('i')) ? contElem.innerText : '';
                    let finalRating = rating === 0 ? parseFloat(document.getElementById('rat-' + id).innerText) : rating;
                    openModal(id, finalRating, content);
                }
            }

            // ОБНОВЛЕННАЯ функция удаления
            async function deleteReview() {
                const id = document.getElementById('field_id').value;
                if (!id) return;

                const confirmed = await showConfirm("Usuwanie opinii", "Czy na pewno chcesz trwale usunąć swoją opinię?");

                if (confirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/title?id=' + currentTitleId;
                    form.innerHTML = `<input type="hidden" name="delete_id" value="${id}">`;
                    document.body.appendChild(form);
                    localStorage.removeItem('my_review_' + id);
                    localStorage.removeItem('my_review_for_title_' + currentTitleId);
                    form.submit();
                }
            }


            function openModal(id = null, rating = 5, content = '') {
                document.getElementById('field_id').value = id || '';
                document.getElementById('field_content').value = content;
                document.getElementById('delete_btn').style.display = id ? 'inline-block' : 'none';
                document.getElementById('modalTitle').innerText = id ? "Edytuj opinię" : "Dodaj opinię";

                // Установка значения оценки
                setModalStars(Math.round(rating));

                document.getElementById('reviewModal').style.display = 'flex';
            }

            // Функция для визуальной отрисовки выбранных звезд в модалке
            function setModalStars(val) {
                document.getElementById('field_rating').value = val;
                const items = document.querySelectorAll('.star-modal-item');
                items.forEach(item => {
                    item.classList.remove('selected');
                    if (parseInt(item.getAttribute('data-value')) === val) {
                        item.classList.add('selected');
                    }
                });
            }

            function closeModal() { document.getElementById('reviewModal').style.display = 'none'; }

            function updateFavoriteButton() {
                const btn = document.getElementById('favoriteBtn');
                const text = document.getElementById('favText');
                let favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");
                if (favorites.includes(titleId)) {
                    btn.classList.add('active');
                    text.innerText = "W ulubionych";
                } else {
                    btn.classList.remove('active');
                    text.innerText = "Dodaj do ulubionych";
                }
            }

            function toggleFavorite() {
                let favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");
                const index = favorites.indexOf(titleId);
                if (index > -1) { favorites.splice(index, 1); } else { favorites.push(titleId); }
                localStorage.setItem('plusflix_favorites', JSON.stringify(favorites));
                updateFavoriteButton();
            }

            window.onclick = (e) => { if (e.target == document.getElementById('reviewModal')) closeModal(); }
            document.addEventListener('DOMContentLoaded', updateFavoriteButton);

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
                const stars = document.querySelectorAll('.star-clickable');

                stars.forEach(star => {
                    star.addEventListener('click', function() {
                        const ratingValue = this.getAttribute('data-value');

                        // Проверяем, есть ли уже отзыв
                        const existingReviewId = localStorage.getItem('my_review_for_title_' + currentTitleId);

                        if (existingReviewId) {
                            // Если отзыв есть, вызываем редактирование
                            handleReviewClick(existingReviewId, ratingValue);
                        } else {
                            // Если отзыва нет, открываем модалку с предустановленной оценкой
                            openModal(null, ratingValue, '');
                        }
                    });

                    // Обработка кликов по звездам ВНУТРИ модального окна
                    const modalStars = document.querySelectorAll('.star-modal-item');
                    modalStars.forEach(star => {
                        star.addEventListener('click', function() {
                            const val = parseInt(this.getAttribute('data-value'));
                            setModalStars(val);
                        });
                    });
                });
            });
        </script>
    <?php endif; ?>

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

<div id="confirmModal" class="modal pf-confirm-modal">
    <div class="modal-content confirm-content">
        <h2 id="confirmTitle">Potwierdzenie</h2>
        <p id="confirmMessage">Czy na pewno chcesz wykonać tę akcję?</p>
        <div class="modal-footer confirm-footer">
            <button id="confirmYes" class="btn-submit">Tak</button>
            <button id="confirmNo" class="btn-cancel" onclick="closeConfirmModal()">Nie</button>
        </div>
    </div>
</div>

</body>
</html>