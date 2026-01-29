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
            <a href="admin/login" class="btn btn-login">Login</a>
        <?php else: ?>
            <a href="admin" class="btn btn-login">Panel Admina</a>
            <form method="post" action="admin/logout" style="display:inline;">
                <button type="submit" class="btn btn-login">Wyloguj</button>
            </form>
        <?php endif; ?>

        <a href="favorites" class="btn btn-fav">Ulubione</a>
        <button class="theme-toggle-btn" id="themeToggle" type="button" aria-label="Toggle theme">🌙</button>
    </div>
</header>

<?php
$vType = $_GET['type'] ?? '';
$vCat  = $_GET['category'] ?? '';
$vPlat = $_GET['platform'] ?? '';
$vLang = $_GET['language'] ?? '';
$sortValue = $_GET['sort'] ?? 'relevance';

$hasAnyFilter =
        !empty($_GET['q']) ||
        !empty($vCat) ||
        !empty($vType) ||
        !empty($vPlat) ||
        !empty($vLang) ||
        (isset($_GET['minrating']) && $_GET['minrating'] !== '') ||
        (isset($_GET['maxrating']) && $_GET['maxrating'] !== '') ||
        (!empty($_GET['sort']) && $_GET['sort'] !== 'relevance');
?>

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

            <input
                    class="rating-input-style"
                    type="number"
                    step="0.1"
                    name="minrating"
                    placeholder="Ocena min..."
                    value="<?= htmlspecialchars($_GET['minrating'] ?? '') ?>"
                    aria-label="Ocena min"
            />

            <input
                    class="rating-input-style"
                    type="number"
                    step="0.1"
                    name="maxrating"
                    placeholder="Ocena max..."
                    value="<?= htmlspecialchars($_GET['maxrating'] ?? '') ?>"
                    aria-label="Ocena max"
            />

            <!-- right overlay area: icons + search (search always visible) -->
            <div class="search-right">
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

                <button class="icon-btn" type="submit" aria-label="Search" title="Szukaj">
                    <svg viewBox="0 0 24 24" aria-hidden="true" class="icon">
                        <path d="M10 2a8 8 0 1 1 5.29 14l4.2 4.2-1.4 1.4-4.2-4.2A8 8 0 0 1 10 2zm0 2a6 6 0 1 0 0 12a6 6 0 0 0 0-12z"
                              fill="currentColor"/>
                    </svg>
                </button>
            </div>

            <select class="filter-btn-style" name="sort" aria-label="Sortowanie" style="display:none;">
                <option value="relevance" <?= ($sortValue === 'relevance') ? 'selected' : '' ?>>Domyślnie</option>
                <option value="ratingdesc" <?= ($sortValue === 'ratingdesc') ? 'selected' : '' ?>>Ocena malejąco</option>
                <option value="ratingasc" <?= ($sortValue === 'ratingasc') ? 'selected' : '' ?>>Ocena rosnąco</option>
                <option value="nameasc" <?= ($sortValue === 'nameasc') ? 'selected' : '' ?>>Nazwa A–Z</option>
                <option value="namedesc" <?= ($sortValue === 'namedesc') ? 'selected' : '' ?>>Nazwa Z–A</option>
            </select>
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
                    </div>

                    <div class="card-info">
                        <span class="card-name"><?= htmlspecialchars($t['name'] ?? '') ?></span>

                        <div class="badges-container">
                            <div class="badge-list">
                                <?php
                                if (!empty($t['categories'])) {
                                    $tags = explode(',', $t['categories']);
                                    foreach (array_slice($tags, 0, 2) as $tag) {
                                        echo '<span class="badge">' . htmlspecialchars(trim($tag)) . '</span>';
                                    }
                                }
                                ?>
                            </div>

                            <div class="badge-list">
                                <span class="badge">Eng</span>
                                <span class="badge">Pl</span>
                                <span class="badge">Rus</span>
                            </div>

                            <div class="badge-list">
                                <span class="badge">Disney+</span>
                                <span class="badge">Netflix</span>
                            </div>
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
        const favorites = JSON.parse(localStorage.getItem('plusflixfavorites') || '[]');
        document.querySelectorAll('.card[data-id]').forEach(link => {
            const currentId = link.getAttribute('data-id');
            if (favorites.includes(currentId)) {
                const titleSpan = link.querySelector('.card-name');
                if (titleSpan && !titleSpan.innerHTML.includes('♥')) {
                    titleSpan.innerHTML = '♥ ' + titleSpan.innerHTML;
                }
            }
        });
    }
    window.onload = checkFavorites;

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

    // Figma-like filters: open options in second row inside gray panel
    (function () {
        const panel = document.getElementById('pfPanelRow');

        function setActiveBtnState() {
            document.querySelectorAll('.pf-select').forEach(wrap => {
                const native = wrap.querySelector('.pf-select__native');
                wrap.classList.toggle('is-active', (native.value || '') !== '');
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
        }

        function openPanelFor(selectWrap) {
            const native = selectWrap.querySelector('.pf-select__native');
            const options = buildOptionsFromNative(native);

            panel.innerHTML = '';
            panel.classList.add('is-open');
            panel.dataset.openFor = selectWrap.dataset.name || '';

            options.forEach(opt => {
                const b = document.createElement('button');
                b.type = 'button';
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

        // init from GET
        setActiveBtnState();

        // top buttons
        document.querySelectorAll('.pf-select').forEach(wrap => {
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

        // close on outside click / ESC
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-redesign-container')) closePanel();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePanel();
        });
    })();
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
</body>
</html>
