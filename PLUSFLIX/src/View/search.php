<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PLUSFLIX — Wyszukiwarka</title>

    <link rel="stylesheet" href="style.css" />
</head>

<body class="search-page">

<header class="navbar">
    <a href="/" class="logo-link" aria-label="PLUSFLIX">
        <img src="/images/logo.png" alt="PLUSFLIX" class="logo-img">
        <span class="logo-text">PLUSFLIX</span>
    </a>

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

<?php
$vType = $_GET['type'] ?? '';
$vCat  = $_GET['category'] ?? '';
$vPlat = $_GET['platform'] ?? '';
$vLang = $_GET['language'] ?? '';
$sortValue = $_GET['sort'] ?? 'relevance';
$vMin = $_GET['min_rating'] ?? ''; // Поправлено имя
$vMax = $_GET['max_rating'] ?? ''; // Поправлено имя

$hasAnyFilter =
        !empty($_GET['q']) ||
        !empty($vCat) ||
        !empty($vType) ||
        !empty($vPlat) ||
        !empty($vLang) ||
        ($vMin !== '') ||
        ($vMax !== '') ||
        ($sortValue !== 'relevance');
?>

<div class="mobile-search-wrapper" id="mobileSearchWrapper">
    <form method="get" action="search" class="mobile-search-form">
        <div class="mobile-search-header">
            <input type="text" name="q" class="mobile-input-trigger" placeholder="Nazwa..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" readonly>

            <div class="mobile-header-actions">
                <?php if ($hasAnyFilter): ?>
                    <a class="icon-btn mobile-action-btn" href="search" aria-label="Wyczyść filtry" title="Wyczyść filtry">
                        <svg viewBox="0 0 24 24" aria-hidden="true" class="icon">
                            <path d="M12 5a7 7 0 1 1-6.32 4H3l3.5-3.5L10 9H7.76A5 5 0 1 0 12 7c1.13 0 2.18.37 3.03 1l1.42-1.42A6.97 6.97 0 0 0 12 5z" fill="currentColor"/>
                        </svg>
                    </a>

                    <button class="icon-btn mobile-action-btn" type="button" id="copyLinkBtn" aria-label="Kopiuj link" title="Kopiuj link">
                        <svg viewBox="0 0 24 24" aria-hidden="true" class="icon">
                            <path d="M16 1H6a2 2 0 0 0-2 2v12h2V3h10V1zm3 4H10a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H10V7h9v14z" fill="currentColor"/>
                        </svg>
                    </button>
                <?php endif; ?>

                <button class="icon-btn mobile-action-btn submit-trigger" type="submit" aria-label="Search">
                    <svg viewBox="0 0 24 24" aria-hidden="true" class="icon" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div class="mobile-search-expandable" id="mobileExpandable">
            <div class="mobile-content-inner">

                <div class="mobile-filter-section">
                    <div class="mobile-rating-row">
                        <input type="number" name="min_rating" step="0.1" placeholder="Ocena min" value="<?= htmlspecialchars($vMin) ?>">
                        <input type="number" name="max_rating" step="0.1" placeholder="Ocena max" value="<?= htmlspecialchars($vMax) ?>">
                    </div>
                </div>

                <?php
                $filters = [
                        'type' => ['label' => 'Typ', 'options' => ['film' => 'Film', 'series' => 'Serial']],
                        'category' => ['label' => 'Gatunki', 'options' => array_combine($allCategories ?? [], $allCategories ?? [])],
                        'platform' => ['label' => 'Platformy', 'options' => array_combine($allPlatforms ?? [], $allPlatforms ?? [])],
                        'language' => ['label' => 'Języki', 'options' => array_combine($allLanguages ?? [], $allLanguages ?? [])],
                        'sort' => ['label' => 'Sortowanie', 'options' => ['rating_desc'=>'Ocena malejąco', 'rating_asc'=>'Ocena rosnąco', 'name_asc'=>'A-Z', 'name_desc'=>'Z-A']]
                ];

                foreach ($filters as $name => $data): ?>
                    <div class="mobile-filter-group">
                        <span class="mobile-filter-label"><?= $data['label'] ?></span>
                        <div class="mobile-options-grid">
                            <?php foreach ($data['options'] as $val => $text):
                                $current = $_GET[$name] ?? '';
                                $isActive = ($current == $val || ($name == 'sort' && $val == 'relevance' && $current == ''));
                                ?>
                                <label class="mobile-opt-chip <?= $isActive ? 'is-active' : '' ?>">
                                    <input type="radio" name="<?= $name ?>" value="<?= $val ?>" <?= $isActive ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <?= htmlspecialchars($text) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </form>
</div>

<div class="search-redesign-container">
    <form method="get" action="search" id="searchForm">
        <div class="search-top-row">
            <input
                    class="new-search-input"
                    type="text"
                    name="q"
                    placeholder="Nazwa..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
            />

            <!-- Typ -->
            <div class="pf-select" data-name="type">
                <button type="button" class="pf-select__btn">Typ</button>
                <select name="type" class="pf-select__native" aria-label="Typ">
                    <option value="" <?= $vType==='' ? 'selected' : '' ?>>Typ</option>
                    <option value="film" <?= $vType==='film' ? 'selected' : '' ?>>Film</option>
                    <option value="series" <?= $vType==='series' ? 'selected' : '' ?>>Serial</option>
                </select>
            </div>

            <!-- Gatunki -->
            <div class="pf-select" data-name="category">
                <button type="button" class="pf-select__btn">Gatunki</button>
                <select name="category" class="pf-select__native" aria-label="Gatunki">
                    <option value="" <?= $vCat==='' ? 'selected' : '' ?>>Gatunki</option>
                    <?php foreach (($allCategories ?? []) as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= ($vCat === $cat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Platformy -->
            <div class="pf-select" data-name="platform">
                <button type="button" class="pf-select__btn">Platformy</button>
                <select name="platform" class="pf-select__native" aria-label="Platformy">
                    <option value="" <?= $vPlat==='' ? 'selected' : '' ?>>Platformy</option>
                    <?php foreach (($allPlatforms ?? []) as $p): ?>
                        <option value="<?= htmlspecialchars($p) ?>" <?= ($vPlat === $p) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Języki -->
            <div class="pf-select" data-name="language">
                <button type="button" class="pf-select__btn">Języki</button>
                <select name="language" class="pf-select__native" aria-label="Języki">
                    <option value="" <?= $vLang==='' ? 'selected' : '' ?>>Języki</option>
                    <?php foreach (($allLanguages ?? []) as $l): ?>
                        <option value="<?= htmlspecialchars($l) ?>" <?= ($vLang === $l) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($l) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="pf-select" data-name="sort">
                <button type="button" class="pf-select__btn">Sortowanie</button>
                <select name="sort" class="pf-select__native" aria-label="Sortowanie">
                    <option value="relevance" <?= $sortValue==='relevance' ? 'selected' : '' ?>>Domyślnie</option>
                    <option value="rating_desc" <?= $sortValue==='rating_desc' ? 'selected' : '' ?>>Ocena malejąco</option>
                    <option value="rating_asc" <?= $sortValue==='rating_asc' ? 'selected' : '' ?>>Ocena rosnąco</option>
                    <option value="name_asc" <?= $sortValue==='name_asc' ? 'selected' : '' ?>>Nazwa A–Z</option>
                    <option value="name_desc" <?= $sortValue==='name_desc' ? 'selected' : '' ?>>Nazwa Z–A</option>
                </select>
            </div>

            <input
                    class="rating-input-style"
                    type="number"
                    step="0.1"
                    min="0"
                    max="5"
                    name="min_rating"
                    placeholder="Ocena min..."
                    value="<?= htmlspecialchars($vMin) ?>"
                    aria-label="Ocena min"
            />

            <input
                    class="rating-input-style"
                    type="number"
                    step="0.1"
                    min="0"
                    max="5"
                    name="max_rating"
                    placeholder="Ocena max..."
                    value="<?= htmlspecialchars($vMax) ?>"
                    aria-label="Ocena max"
            />

            <?php if ($hasAnyFilter): ?>
                <a class="icon-btn" href="search" aria-label="Wyczyść filtry" title="Wyczyść filtry">
                    <svg viewBox="0 0 24 24" aria-hidden="true" class="icon">
                        <path d="M12 5a7 7 0 1 1-6.32 4H3l3.5-3.5L10 9H7.76A5 5 0 1 0 12 7c1.13 0 2.18.37 3.03 1l1.42-1.42A6.97 6.97 0 0 0 12 5z"
                              fill="currentColor"/>
                    </svg>
                </a>

                <button class="icon-btn" type="button" id="copyLinkBtn" aria-label="Kopiuj link" title="Kopiuj link">
                    <svg viewBox="0 0 24 24" aria-hidden="true" class="icon">
                        <path d="M16 1H6a2 2 0 0 0-2 2v12h2V3h10V1zm3 4H10a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H10V7h9v14z"
                              fill="currentColor"/>
                    </svg>
                </button>
            <?php endif; ?>

            <!-- right overlay area: icons + search (search always visible) -->
            <button class="icon-btn" type="submit" aria-label="Search" title="Szukaj">
                <svg viewBox="0 0 24 24" aria-hidden="true" class="icon" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </div>

        <!-- second row inside the gray panel -->
        <div class="pf-panel-row" id="pfPanelRow" aria-label="Filter options row"></div>
    </form>
</div>

<?php if (!empty($errors)): ?>
    <div class="search-redesign-container">
        <div class="error-box">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($results)): ?>
    <div class="container" style="padding-top:20px;">
        <div class="movie-grid">
            <?php foreach ($results as $t): ?>
                <a href="title?id=<?= (int)$t['id'] ?>" class="card" data-id="<?= (int)$t['id'] ?>">
                    <div class="card-img" style="background-image: url('<?= !empty($t['imagepath']) ? htmlspecialchars($t['imagepath']) : 'https://via.placeholder.com/300x450' ?>');">
                        <div class="rating"><span>★</span> <?= number_format((float)($t['average_rating'] ?? 0), 1) ?>/5</div>
                        <div class="fav-badge" title="Ulubione">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="card-info">
                        <span class="card-name"><?= htmlspecialchars($t['name'] ?? '') ?></span>

                        <div class="badges-container" style="display: flex; flex-direction: column; gap: 6px; width: 100%; margin-top: auto;">

                            <?php if (!empty($t['categories'])): ?>
                                <div class="badge-row-fill">
                                    <?php
                                    $tags = explode(',', $t['categories']);
                                    foreach (array_slice($tags, 0, 3) as $tag): ?>
                                        <span class="badge"><?= htmlspecialchars(trim($tag)) ?></span>
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
    </div>
<?php else: ?>
    <?php if ($hasAnyFilter && empty($errors)): ?>
        <div class="search-redesign-container">
            <p style="opacity:.85;">Brak wyników.</p>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
    const form = document.getElementById('searchForm');

    // auto apply rating only
    form.querySelectorAll('input[type="number"]').forEach(el => {
        el.addEventListener('change', () => form.submit());
    });


    // Copy link button (only exists when filters are active)
    (function () {
        const btn = document.getElementById('copyLinkBtn');
        if (!btn) return;

        btn.addEventListener('click', async () => {
            try{
                await navigator.clipboard.writeText(window.location.href);
                btn.style.opacity = '0.7';
                setTimeout(() => btn.style.opacity = '1', 600);
            }catch(e){
                const ta = document.createElement('textarea');
                ta.value = window.location.href;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                ta.remove();
                btn.style.opacity = '0.7';
                setTimeout(() => btn.style.opacity = '1', 600);
            }
        });
    })();


    // Favorites marker
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

    // Theme toggle + icon
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

    (function () {
        const panel = document.getElementById('pfPanelRow');
        const allSelectWraps = document.querySelectorAll('.pf-select');

        function setActiveBtnState() {
            allSelectWraps.forEach(wrap => {
                const native = wrap.querySelector('.pf-select__native');
                const btn = wrap.querySelector('.pf-select__btn');

                // Сохраняем исходное название (Typ, Gatunki и т.д.), если его еще нет
                if (!btn.dataset.defaultText) {
                    btn.dataset.defaultText = btn.textContent;
                }

                const val = native.value;
                // Условие активности: значение не пустое И не является дефолтной сортировкой
                const hasValue = (val !== "" && val !== "relevance");

                // 1. Управляем текстом кнопки
                if (hasValue) {
                    // Показываем текст выбранного элемента
                    btn.textContent = native.options[native.selectedIndex].text;
                } else {
                    // Возвращаем исходный текст (Typ, Gatunki, Sortowanie...)
                    btn.textContent = btn.dataset.defaultText;
                }

                // 2. Управляем классом активного состояния (для красного текста)
                wrap.classList.toggle('is-active', hasValue);

                // При обновлении состояний всегда убираем подсветку открытого меню (фон)
                wrap.classList.remove('is-menu-open');
            });
        }

        function buildOptionsFromNative(nativeSelect) {
            return Array.from(nativeSelect.options).map(o => ({
                value: o.value,
                text: o.text
            }));
        }

        function closePanel() {
            panel.classList.remove('is-open');
            panel.dataset.openFor = '';
            panel.innerHTML = '';
            // Убираем красный фон со всех кнопок при закрытии
            allSelectWraps.forEach(w => w.classList.remove('is-menu-open'));
        }

        function openPanelFor(selectWrap) {
            const native = selectWrap.querySelector('.pf-select__native');
            const options = buildOptionsFromNative(native);

            // Убираем подсветку у других кнопок
            allSelectWraps.forEach(w => w.classList.remove('is-menu-open'));
            // Добавляем красный фон текущей нажатой кнопке
            selectWrap.classList.add('is-menu-open');

            panel.innerHTML = '';
            panel.classList.add('is-open');
            panel.dataset.openFor = selectWrap.dataset.name || '';

            options.forEach(opt => {
                const b = document.createElement('button');
                b.type = 'button';
                // Подсвечиваем выбранный пункт в выпадающем списке
                b.className = 'pf-panel-opt' + ((native.value === opt.value) ? ' is-selected' : '');
                b.textContent = opt.text;

                b.addEventListener('click', () => {
                    native.value = opt.value;
                    setActiveBtnState();
                    closePanel();
                    form.submit();
                });

                panel.appendChild(b);
            });
        }

        // Инициализация при загрузке
        setActiveBtnState();

        // Клики по кнопкам фильтров
        allSelectWraps.forEach(wrap => {
            const btn = wrap.querySelector('.pf-select__btn');
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const name = wrap.dataset.name || '';
                if (panel.classList.contains('is-open') && panel.dataset.openFor === name) {
                    closePanel();
                    return;
                }
                openPanelFor(wrap);
            });
        });

        // Закрытие по клику вне или ESC
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-redesign-container')) closePanel();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePanel();
        });
    })();
    form.querySelectorAll('.rating-input-style').forEach(el => {
        el.addEventListener('input', function() {
            if (this.value > 5) this.value = 5;
            if (this.value < 0 && this.value !== "") this.value = 0;
        });

        el.addEventListener('change', () => {
            if (this.value !== "") form.submit();
        });
    });

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

    document.addEventListener('DOMContentLoaded', () => {
        const wrapper = document.getElementById('mobileSearchWrapper');
        const trigger = document.querySelector('.mobile-input-trigger');

        if (trigger) {
            trigger.addEventListener('click', (e) => {
                // Если панель закрыта, открываем её
                if (!wrapper.classList.contains('is-open')) {
                    wrapper.classList.add('is-open');
                    trigger.removeAttribute('readonly');
                    trigger.focus();
                }
            });

            // Отправка формы при нажатии Enter в поле ввода
            trigger.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    trigger.form.submit();
                }
            });
        }

        // Авто-отправка для инпутов рейтинга при потере фокуса или изменении
        const ratingInputs = document.querySelectorAll('.mobile-rating-row input');
        ratingInputs.forEach(input => {
            input.addEventListener('change', () => {
                if (input.value !== "") input.form.submit();
            });
        });
    });
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
