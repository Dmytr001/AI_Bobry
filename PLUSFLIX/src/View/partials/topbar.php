<header class="navbar">
    <a href="/" class="logo-link" aria-label="PLUSFLIX">
        <img src="/images/logo.png" alt="PLUSFLIX" class="logo-img">
        <span class="logo-text">PLUSFLIX</span>
    </a>

    <div class="nav-actions">
        <form method="get" action="/search" style="display:flex; gap:10px; align-items:center;">
            <input type="text" class="search-input" name="q" placeholder="Wyszukiwanie...">
        </form>

        <?php
        $isAdminPage = (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin') === 0);
        ?>

        <?php if (!$isAdminPage): ?>
            <?php if (empty($_SESSION['adminid'])): ?>
                <a href="/admin/login" class="btn btn-login">Login</a>
            <?php else: ?>
                <a href="/admin" class="btn btn-login">Panel Admina</a>
            <?php endif; ?>
        <?php endif; ?>



        <a href="/favorites" class="btn btn-fav">Ulubione</a>
        <button class="theme-toggle-btn" id="themeToggle" type="button" aria-label="Toggle theme">🌙</button>
    </div>
</header>

<script>
    (function () {
        const key = 'plusflix-theme';
        const btn = document.getElementById('themeToggle');
        if (!btn) return;

        function syncIcon() {
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
