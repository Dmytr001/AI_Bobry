<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — <?= ($mode ?? 'create') === 'edit' ? 'Edytuj' : 'Dodaj' ?> produkcję</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="admin-page">

<?php require __DIR__ . '/../../partials/topbar.php'; ?>
<?php
$active = (($mode ?? 'create') === 'edit') ? 'movies' : 'movies_create';
require __DIR__ . '/../partials/nav.php';
?>

<h1 class="admin-title">Produkcje: <?= ($mode ?? 'create') === 'edit' ? 'edytuj' : 'dodaj' ?></h1>

<?php if (!empty($error)): ?>
    <div class="admin-alert"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form class="admin-form" method="post" enctype="multipart/form-data"
      action="<?= ($mode ?? 'create') === 'edit' ? '/admin/movies/edit' : '/admin/movies/create' ?>">

    <?php if (($mode ?? 'create') === 'edit'): ?>
        <input type="hidden" name="id" value="<?= (int)($movie['id'] ?? 0) ?>">
    <?php endif; ?>

    <label class="admin-label">Nazwa</label>
    <input type="text" name="name" required value="<?= htmlspecialchars($movie['name'] ?? '') ?>">

    <label class="admin-label">Typ</label>
    <select name="type" id="typeSelect">
        <option value="film" <?= (($movie['type'] ?? 'film') === 'film') ? 'selected' : '' ?>>film</option>
        <option value="series" <?= (($movie['type'] ?? '') === 'series') ? 'selected' : '' ?>>series</option>
    </select>

    <label class="admin-label">Opis</label>
    <textarea name="description" rows="5"><?= htmlspecialchars($movie['description'] ?? '') ?></textarea>

    <label class="admin-label">Kategorie</label>
    <input type="text" name="categories" list="categoriesList" value="<?= htmlspecialchars($movie['categories'] ?? '') ?>">
    <datalist id="categoriesList">
        <?php foreach (($categoriesAll ?? []) as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"></option>
        <?php endforeach; ?>
    </datalist>
    <div class="admin-hint">Format: np. Action, Drama, Sci‑Fi</div>

    <label class="admin-label">Niedostępne w krajach</label>
    <input type="text" name="blockedcountries" value="<?= htmlspecialchars($movie['blockedcountries'] ?? '') ?>">

    <label class="admin-label">Obrazek</label>
    <?php if (!empty($movie['imagepath'])): ?>
        <div class="admin-preview">
            <div class="admin-muted">Aktualny: <?= htmlspecialchars($movie['imagepath']) ?></div>
            <img src="<?= htmlspecialchars($movie['imagepath']) ?>" alt="okładka">
        </div>
    <?php endif; ?>
    <input type="file" name="image" accept="image/*">
    <?php if (($mode ?? 'create') === 'edit'): ?>
        <div class="admin-hint">Jeśli nie wybierzesz nowego pliku, zostanie obecny obrazek.</div>
    <?php endif; ?>

    <div class="admin-split"></div>

    <h3 class="admin-subtitle">Języki</h3>
    <div id="languagesWrap">
        <?php
        $langValues = !empty($selectedLanguageIds) ? $selectedLanguageIds : [0];
        foreach ($langValues as $lid):
            ?>
            <div class="admin-row langRow">
                <select name="languages[]">
                    <option value="0">wybierz</option>
                    <?php foreach (($languagesAll ?? []) as $lang): ?>
                        <option value="<?= (int)$lang['id'] ?>" <?= ((int)$lid === (int)$lang['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lang['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="admin-row-btn" type="button" onclick="removeRow(this)">Usuń</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="admin-add-btn" type="button" onclick="addLanguage()">Dodaj język</button>

    <div class="admin-split"></div>

    <h3 class="admin-subtitle">Platformy + link</h3>
    <div id="platformsWrap">
        <?php
        $platValues = !empty($selectedPlatforms) ? $selectedPlatforms : [['platformid'=>0,'watchlink'=>'']];
        foreach ($platValues as $p):
            ?>
            <div class="admin-row platRow">
                <select name="platformid[]">
                    <option value="0">wybierz</option>
                    <?php foreach (($platformsAll ?? []) as $pl): ?>
                        <option value="<?= (int)$pl['id'] ?>" <?= ((int)($p['platformid'] ?? 0) === (int)$pl['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pl['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="text" name="watchlink[]" placeholder="link" value="<?= htmlspecialchars($p['watchlink'] ?? '') ?>">
                <button class="admin-row-btn" type="button" onclick="removeRow(this)">Usuń</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="admin-add-btn" type="button" onclick="addPlatform()">Dodaj platformę</button>

    <div class="admin-split"></div>

    <h3 class="admin-subtitle">Odcinki (tylko dla series)</h3>
    <div id="episodesSection">
        <div id="episodesWrap">
            <?php
            $epValues = !empty($episodes) ? $episodes : [['episodenumber'=>'','name'=>'']];
            foreach ($epValues as $e):
                ?>
                <div class="admin-row epRow">
                    <input type="text" name="episodenumber[]" placeholder="nr" value="<?= htmlspecialchars((string)($e['episodenumber'] ?? '')) ?>" style="max-width:90px;">
                    <input type="text" name="episodename[]" placeholder="nazwa odcinka" value="<?= htmlspecialchars($e['name'] ?? '') ?>">
                    <button class="admin-row-btn" type="button" onclick="removeRow(this)">Usuń</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="admin-add-btn" type="button" onclick="addEpisode()">Dodaj odcinek</button>
    </div>

    <div class="admin-actions admin-actions--sticky">
        <button class="admin-primary-btn" type="submit">Zatwierdzić</button>
    </div>

</form>

<script>
    function removeRow(btn){
        const row = btn.parentElement;
        row.parentElement.removeChild(row);
    }
    function addLanguage(){
        const wrap = document.getElementById('languagesWrap');
        const first = wrap.querySelector('.langRow');
        const clone = first.cloneNode(true);
        clone.querySelector('select').value = '0';
        wrap.appendChild(clone);
    }
    function addPlatform(){
        const wrap = document.getElementById('platformsWrap');
        const first = wrap.querySelector('.platRow');
        const clone = first.cloneNode(true);
        const sel = clone.querySelector('select'); if (sel) sel.value = '0';
        const inp = clone.querySelector('input'); if (inp) inp.value = '';
        wrap.appendChild(clone);
    }
    function addEpisode(){
        const wrap = document.getElementById('episodesWrap');
        const first = wrap.querySelector('.epRow');
        const clone = first.cloneNode(true);
        clone.querySelectorAll('input').forEach(i => i.value = '');
        wrap.appendChild(clone);
    }
    function toggleEpisodes(){
        const type = document.getElementById('typeSelect').value;
        document.getElementById('episodesSection').style.display = (type === 'series') ? 'block' : 'none';
    }
    document.getElementById('typeSelect').addEventListener('change', toggleEpisodes);
    toggleEpisodes();
</script>

</main></div>
</body>
</html>
