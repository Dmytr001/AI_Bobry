<header class="navbar">
    <a href="/" class="logo-link" aria-label="PLUSFLIX">
        <img src="/images/logo.png" alt="PLUSFLIX" class="logo-img">
        <span class="logo-text">PLUSFLIX</span>
    </a>

    <div class="search-wrapper">
        <form action="/search" method="get" class="search-form">
            <div class="search-container-inner">
                <input type="text" name="q" class="search-input-active" placeholder="Wyszukiwanie..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

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
        <a href="/favorites" class="btn btn-fav" aria-label="Ulubione">
            <span class="btn-text">Ulubione</span>
            <svg class="heart-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </a>
        <button class="theme-toggle-btn" id="themeToggle" type="button" aria-label="Toggle theme">🌓</button>
    </div>
</header>

<script>
    (function () {
        const key = 'plusflix-theme';
        const btn = document.getElementById('themeToggle');
        if (!btn) return;

        function syncIcon() {
            // Обновляем иконку в зависимости от режима
            btn.textContent = document.body.classList.contains('light-mode') ? '☀️' : '🌙';
        }

        const saved = localStorage.getItem(key);
        if (saved === 'light') document.body.classList.add('light-mode');
        syncIcon();

        btn.addEventListener('click', () => {
            document.body.classList.toggle('light-mode');
            localStorage.setItem(key, document.body.classList.contains('light-mode') ? 'light' : 'dark');
            syncIcon();
        });
    })();
</script>
