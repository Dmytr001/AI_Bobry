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
    <h2>Najlepiej oceniane (Top 5)</h2>
</div>

<?php if (!empty($topRatedTitles)): ?>
    <?php foreach ($topRatedTitles as $t): ?>
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
        Brak wyników do wyświetlenia.
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
