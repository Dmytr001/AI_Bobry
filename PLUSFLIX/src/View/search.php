<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PLUSFLIX – wyszukiwarka</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        form { margin-bottom: 20px; }
        input, select, button { padding: 8px; margin-right: 10px; }
        .title { border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 10px; }
        .type { font-size: 0.9em; color: #666; }
    </style>
</head>
<body>

<h1>PLUSFLIX</h1>

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

    <input type="number" step="0.1" name="min_rating" placeholder="min ocena" value="<?= htmlspecialchars($_GET['min_rating'] ?? '') ?>">
    <input type="number" step="0.1" name="max_rating" placeholder="max ocena" value="<?= htmlspecialchars($_GET['max_rating'] ?? '') ?>">

    <button type="submit">Szukaj</button>
    <a href="/" style="padding:8px 12px; background:#ccc; color:black; text-decoration:none; border-radius:4px;">Usuń filtry</a>

</form>

<?php if (!empty($results)): ?>
    <h2>Wyniki:</h2>
    <?php foreach ($results as $title): ?>
        <div class="title">
            <strong><?= htmlspecialchars($title['name']) ?></strong>
            <div class="type">
                <?= htmlspecialchars($title['type']) ?> | ⭐ <?= $title['average_rating'] ?> | Kategorie: <?= htmlspecialchars($title['categories']) ?>
            </div>
            <p><?= htmlspecialchars($title['description']) ?></p>
        </div>

    <?php endforeach; ?>
<?php else: ?>
    <p>Brak wyników.</p>
<?php endif; ?>

</body>
</html>
