<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PLUSFLIX – Strona główna</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .card { border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 10px; }
        .meta { font-size: 0.9em; color: #666; }
        a.btn { padding:8px 12px; background:#333; color:#fff; text-decoration:none; border-radius:4px; display:inline-block; }
        a.title-link { text-decoration:none; color:inherit; display:block; }
        .card:hover { background:#f9f9f9; }
        button.btn { padding:8px 12px; background:#333; color:#fff; border:none; border-radius:4px; cursor:pointer; }
        .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
}
.section-header select {
    padding: 5px;
    font-size: 0.8em;
}
    </style>
</head>
<body>
<?php 
$allCategories = ['Action','Drama','Comedy','Fantasy','Sci-Fi','Animation','Romance','Biography','Thriller','Adventure','Sport','Mystery','History']; 
?>
<h1>PLUSFLIX</h1>

<p>
    <a class="btn" href="/search">Przejdź do wyszukiwarki</a>
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

<?php if (!empty($newestTitles)): ?>
    <div class="section-header">
        <h2>Nowości (Ostatnio dodane)</h2>
    </div>
    
    <?php foreach ($newestTitles as $t): ?>
        <a href="/title?id=<?= (int)$t['id'] ?>" class="title-link">
            <div class="card">
                <strong><?= htmlspecialchars($t['name']) ?></strong>
                <div class="meta">
                    <?= htmlspecialchars($t['type']) ?> | ⭐ <?= htmlspecialchars($t['average_rating']) ?> | Kategorie: <?= htmlspecialchars($t['categories']) ?>
                </div>
                <p><?= htmlspecialchars($t['description']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($trendyTitles)): ?>
    <div class="section-header">
        <h2>Trendy tygodnia 🔥</h2>
        <span style="color: #999; font-size: 0.8em;">Odświeża się w każdy poniedziałek</span>
    </div>
    
    <?php foreach ($trendyTitles as $t): ?>
        <a href="/title?id=<?= (int)$t['id'] ?>" class="title-link">
            <div class="card">
                <strong><?= htmlspecialchars($t['name']) ?></strong>
                <div class="meta">
                    <?= htmlspecialchars($t['type']) ?> | ⭐ <?= htmlspecialchars($t['average_rating']) ?> | Kategorie: <?= htmlspecialchars($t['categories']) ?>
                </div>
                <p><?= htmlspecialchars($t['description']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

    <div class="section-header">
    <h2>Polecane filmy (Top 5)</h2>
    <form method="get" action="/">
        <input type="hidden" name="cat_s" value="<?= htmlspecialchars($_GET['cat_s'] ?? '') ?>">
        <input type="hidden" name="sort_s" value="<?= htmlspecialchars($_GET['sort_s'] ?? 'rating_desc') ?>">
        <input type="hidden" name="plat_s" value="<?= htmlspecialchars($_GET['plat_s'] ?? '') ?>">
        <input type="hidden" name="lang_s" value="<?= htmlspecialchars($_GET['lang_s'] ?? '') ?>">

        <select name="cat_f" onchange="this.form.submit()">
            <option value="">Wszystkie kategorie</option>
            <?php foreach ($allCategories as $cat): ?>
                <option value="<?= $cat ?>" <?= (($_GET['cat_f'] ?? '') === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
        </select>

        <select name="plat_f" onchange="this.form.submit()">
            <option value="">Wszystkie platformy</option>
            <?php foreach (['Netflix','Apple TV+'] as $p): ?>
                <option value="<?= $p ?>" <?= (($_GET['plat_f'] ?? '') === $p) ? 'selected' : '' ?>><?= $p ?></option>
            <?php endforeach; ?>
        </select>

        <select name="lang_f" onchange="this.form.submit()">
            <option value="">Wszystkie języki</option>
            <?php foreach (['Polish','English', 'Italian', 'Spanish', 'French', 'German', 'Japanese', 'Russian', 'Irish'] as $l): ?>
                <option value="<?= $l ?>" <?= (($_GET['lang_f'] ?? '') === $l) ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>

        <select name="sort_f" onchange="this.form.submit()">
            <option value="relevance" <?= ($_GET['sort_f'] ?? '') === 'relevance' ? 'selected' : '' ?>>Domyślnie</option>
            <option value="rating_desc" <?= ($_GET['sort_f'] ?? '') === 'rating_desc' ? 'selected' : '' ?>>Ocena: malejąco</option>
            <option value="rating_asc" <?= ($_GET['sort_f'] ?? '') === 'rating_asc' ? 'selected' : '' ?>>Ocena: rosnąco</option>
            <option value="name_asc" <?= ($_GET['sort_f'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Nazwa: A–Z</option>
        </select>
    </form>
</div>

<?php if (!empty($top5Films)): ?>
    <?php foreach ($top5Films as $t): ?>
        <a href="/title?id=<?= (int)$t['id'] ?>" class="title-link">
            <div class="card">
                <strong><?= htmlspecialchars($t['name']) ?></strong>
                <div class="meta">
                    <?= htmlspecialchars($t['type']) ?> | ⭐ <?= htmlspecialchars($t['average_rating']) ?> | Kategorie: <?= htmlspecialchars($t['categories']) ?>
                </div>
                <p><?= htmlspecialchars($t['description']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <p style="padding: 20px; color: #666; background: #f0f0f0; border-radius: 4px;">
        Brak wyników spełniających wybrane kryteria. Spróbuj zmienić filtry.
    </p>
<?php endif; ?>

<div class="section-header">
    <h2>Polecane seriale (Top 5)</h2>
    <form method="get" action="/">
        <input type="hidden" name="cat_f" value="<?= htmlspecialchars($_GET['cat_f'] ?? '') ?>">
        <input type="hidden" name="sort_f" value="<?= htmlspecialchars($_GET['sort_f'] ?? 'rating_desc') ?>">
        <input type="hidden" name="plat_f" value="<?= htmlspecialchars($_GET['plat_f'] ?? '') ?>">
        <input type="hidden" name="lang_f" value="<?= htmlspecialchars($_GET['lang_f'] ?? '') ?>">

        <select name="cat_s" onchange="this.form.submit()">
            <option value="">Wszystkie kategorie</option>
            <?php foreach ($allCategories as $cat): ?>
                <option value="<?= $cat ?>" <?= (($_GET['cat_s'] ?? '') === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
        </select>

        <select name="plat_s" onchange="this.form.submit()">
            <option value="">Wszystkie platformy</option>
            <?php foreach (['Netflix','Apple TV+'] as $p): ?>
                <option value="<?= $p ?>" <?= (($_GET['plat_s'] ?? '') === $p) ? 'selected' : '' ?>><?= $p ?></option>
            <?php endforeach; ?>
        </select>

        <select name="lang_s" onchange="this.form.submit()">
            <option value="">Wszystkie języki</option>
            <?php foreach (['Polish','English', 'Italian', 'Spanish', 'French', 'German', 'Japanese', 'Russian', 'Irish'] as $l): ?>
                <option value="<?= $l ?>" <?= (($_GET['lang_s'] ?? '') === $l) ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>

        <select name="sort_s" onchange="this.form.submit()">
            <option value="relevance" <?= ($_GET['sort_s'] ?? '') === 'relevance' ? 'selected' : '' ?>>Domyślnie</option>
            <option value="rating_desc" <?= ($_GET['sort_s'] ?? '') === 'rating_desc' ? 'selected' : '' ?>>Ocena: malejąco</option>
            <option value="rating_asc" <?= ($_GET['sort_s'] ?? '') === 'rating_asc' ? 'selected' : '' ?>>Ocena: rosnąco</option>
            <option value="name_asc" <?= ($_GET['sort_s'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Nazwa: A–Z</option>
        </select>
    </form>
</div>

<?php if (!empty($top5Series)): ?>
    <?php foreach ($top5Series as $t): ?>
        <a href="/title?id=<?= (int)$t['id'] ?>" class="title-link">
            <div class="card">
                <strong><?= htmlspecialchars($t['name']) ?></strong>
                <div class="meta">
                    <?= htmlspecialchars($t['type']) ?> | ⭐ <?= htmlspecialchars($t['average_rating']) ?> | Kategorie: <?= htmlspecialchars($t['categories']) ?>
                </div>
                <p><?= htmlspecialchars($t['description']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <p style="padding: 20px; color: #666; background: #f0f0f0; border-radius: 4px;">
        Brak seriali spełniających wybrane kryteria. Spróbuj zmienić filtry.
    </p>
<?php endif; ?>

<script>
    window.onbeforeunload = function() {
        sessionStorage.setItem("scrollPos", window.scrollY);
    };

    window.onload = function() {
        if (sessionStorage.getItem("scrollPos")) {
            window.scrollTo(0, sessionStorage.getItem("scrollPos"));
            sessionStorage.removeItem("scrollPos");
        }
    };
</script>

</body>
</html>
