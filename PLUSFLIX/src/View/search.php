<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PLUSFLIX – wyszukiwarka</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        form { margin-bottom: 20px; }
        input, select, button { padding: 8px; margin-right: 10px; margin-bottom: 10px; }
        .title { border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 10px; }
        .type { font-size: 0.9em; color: #666; }
        .error-box { padding:10px; background:#ffd7d7; border:1px solid #ff9b9b; margin: 10px 0; }
    </style>
</head>
<body>

<h1>PLUSFLIX</h1>

<?php if (!empty($top5Films)): ?>
    <h2>Polecane filmy (Top 5)</h2>
    <?php foreach ($top5Films as $t): ?>
        <div class="title">
            <strong><?= htmlspecialchars($t['name']) ?></strong>
            <div class="type">
                <?= htmlspecialchars($t['type']) ?> | ⭐ <?= htmlspecialchars($t['average_rating']) ?> | Kategorie: <?= htmlspecialchars($t['categories']) ?>
            </div>
            <p><?= htmlspecialchars($t['description']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($top5Series)): ?>
    <h2>Polecane seriale (Top 5)</h2>
    <?php foreach ($top5Series as $t): ?>
        <div class="title">
            <strong><?= htmlspecialchars($t['name']) ?></strong>
            <div class="type">
                <?= htmlspecialchars($t['type']) ?> | ⭐ <?= htmlspecialchars($t['average_rating']) ?> | Kategorie: <?= htmlspecialchars($t['categories']) ?>
            </div>
            <p><?= htmlspecialchars($t['description']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<form method="get" action="/">
    <input type="text" name="q" placeholder="Nazwa" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

    <select name="category">
        <option value="">Wszystkie kategorie</option>
        <?php
        $allCategories = ['Action','Drama','Comedy','Fantasy','Sci-Fi','Animation','Romance','Biography','Thriller','Adventure','Sport','Mystery','History'];
        foreach ($allCategories as $cat):
            $selected = (isset($_GET['category']) && $_GET['category']==$cat) ? 'selected' : '';
            ?>
            <option value="<?= $cat ?>" <?= $selected ?>><?= $cat ?></option>
        <?php endforeach; ?>
    </select>

    <select name="type">
        <option value="">Wszystkie</option>
        <option value="film" <?= (isset($_GET['type']) && $_GET['type']=='film') ? 'selected' : '' ?>>Film</option>
        <option value="series" <?= (isset($_GET['type']) && $_GET['type']=='series') ? 'selected' : '' ?>>Serial</option>
    </select>

    <select name="platform">
        <option value="">Wszystkie platformy</option>
        <?php
        $allPlatforms = ['Netflix','Apple TV+'];
        foreach ($allPlatforms as $p):
            $selected = (isset($_GET['platform']) && $_GET['platform']==$p) ? 'selected' : '';
            ?>
            <option value="<?= $p ?>" <?= $selected ?>><?= $p ?></option>
        <?php endforeach; ?>
    </select>

    <select name="language">
        <option value="">Wszystkie języki</option>
        <?php
        $allLanguages = ['Polish','English', 'Italian', 'Spanish', 'French', 'German', 'Japanese', 'Russian', 'Irish'];
        foreach ($allLanguages as $l):
            $selected = (isset($_GET['language']) && $_GET['language']==$l) ? 'selected' : '';
            ?>
            <option value="<?= $l ?>" <?= $selected ?>><?= $l ?></option>
        <?php endforeach; ?>
    </select>

    <?php $sortValue = $_GET['sort'] ?? 'relevance'; ?>
    <select name="sort">
        <option value="relevance" <?= $sortValue==='relevance' ? 'selected' : '' ?>>Domyślnie</option>
        <option value="rating_desc" <?= $sortValue==='rating_desc' ? 'selected' : '' ?>>Ocena: malejąco</option>
        <option value="rating_asc" <?= $sortValue==='rating_asc' ? 'selected' : '' ?>>Ocena: rosnąco</option>
        <option value="name_asc" <?= $sortValue==='name_asc' ? 'selected' : '' ?>>Nazwa: A–Z</option>
        <option value="name_desc" <?= $sortValue==='name_desc' ? 'selected' : '' ?>>Nazwa: Z–A</option>
    </select>

    <input type="number" step="0.1" name="min_rating" placeholder="min ocena" value="<?= htmlspecialchars($_GET['min_rating'] ?? '') ?>">
    <input type="number" step="0.1" name="max_rating" placeholder="max ocena" value="<?= htmlspecialchars($_GET['max_rating'] ?? '') ?>">

    <button type="submit">Szukaj</button>
    <a href="/" style="padding:8px 12px; background:#ccc; color:black; text-decoration:none; border-radius:4px;">Usuń filtry</a>
</form>

<?php if (!empty($errors)): ?>
    <div class="error-box">
        <?php foreach ($errors as $e): ?>
            <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($results)): ?>
    <h2>Wyniki:</h2>
    <?php foreach ($results as $title): ?>
        <div class="title">
            <strong><?= htmlspecialchars($title['name']) ?></strong>
            <div class="type">
                <?= htmlspecialchars($title['type']) ?> | ⭐ <?= htmlspecialchars($title['average_rating']) ?> | Kategorie: <?= htmlspecialchars($title['categories']) ?>
            </div>
            <p><?= htmlspecialchars($title['description']) ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Brak wyników.</p>
<?php endif; ?>

</body>
</html>
