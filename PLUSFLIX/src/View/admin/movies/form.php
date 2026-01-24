<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Admin – <?= $mode === 'edit' ? 'Edytuj' : 'Dodaj' ?> produkcję</title>
</head>
<body>

<?php $active = 'movies'; require __DIR__ . '/../partials/nav.php'; ?>

<h1><?= $mode === 'edit' ? 'Edytuj' : 'Dodaj' ?> produkcję</h1>

<p><a href="/admin/movies">← Lista produkcji</a></p>

<?php if (!empty($error)): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" action="<?= $mode === 'edit' ? '/admin/movies/edit' : '/admin/movies/create' ?>">
  <?php if ($mode === 'edit'): ?>
    <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
  <?php endif; ?>

  <div>
    <label>Nazwa *</label><br>
    <input type="text" name="name" required value="<?= htmlspecialchars($movie['name'] ?? '') ?>" style="width:420px;">
  </div>
  <br>

  <div>
    <label>Typ *</label><br>
    <select name="type" id="typeSelect">
      <option value="film" <?= (($movie['type'] ?? 'film') === 'film') ? 'selected' : '' ?>>film</option>
      <option value="series" <?= (($movie['type'] ?? '') === 'series') ? 'selected' : '' ?>>series</option>
    </select>
  </div>
  <br>

  <div>
    <label>Opis</label><br>
    <textarea name="description" rows="6" style="width:420px;"><?= htmlspecialchars($movie['description'] ?? '') ?></textarea>
  </div>
  <br>

  <div>
  <label>Kategorie</label><br>
  <input type="text"
         name="categories"
         list="categoriesList"
         value="<?= htmlspecialchars($movie['categories'] ?? '') ?>"
         style="width:420px;">
  <datalist id="categoriesList">
    <?php foreach (($categoriesAll ?? []) as $c): ?>
      <option value="<?= htmlspecialchars($c) ?>"></option>
    <?php endforeach; ?>
  </datalist>
  <p style="color:#666; margin:6px 0;">Format: np. Action, Drama, Sci-Fi</p>
</div>

  <br>

  <div>
    <label>Niedostępne w krajach</label><br>
    <input type="text" name="blocked_countries" value="<?= htmlspecialchars($movie['blocked_countries'] ?? '') ?>" style="width:420px;">
  </div>
  <br>

  <div>
  <label>Obrazek</label><br>

  <?php if (!empty($movie['image_path'])): ?>
    <div style="margin:6px 0;">
      Aktualny: <?= htmlspecialchars($movie['image_path']) ?><br>
      <img src="<?= htmlspecialchars($movie['image_path']) ?>" alt="okładka" style="max-width:200px;">
    </div>
  <?php endif; ?>

  <input type="file" name="image" accept="image/*">
  <?php if ($mode === 'edit'): ?>
    <p style="color:#666; margin:6px 0;">Jeśli nie wybierzesz nowego pliku, zostanie obecny obrazek.</p>
  <?php endif; ?>
</div>

  <hr>

  <h3>Języki</h3>
  <div id="languagesWrap">
    <?php
      $langValues = !empty($selectedLanguageIds) ? $selectedLanguageIds : [0];
      foreach ($langValues as $lid):
    ?>
      <div class="langRow" style="margin-bottom:6px;">
        <select name="languages[]">
          <option value="0">— wybierz —</option>
          <?php foreach ($languagesAll as $lang): ?>
            <option value="<?= (int)$lang['id'] ?>" <?= ((int)$lid === (int)$lang['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($lang['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="button" onclick="removeRow(this)">Usuń</button>
      </div>
    <?php endforeach; ?>
  </div>
  <button type="button" onclick="addLanguage()">+ Dodaj język</button>
  <hr>
  <h3>Platformy + link</h3>
  <div id="platformsWrap">
    <?php
      $platValues = !empty($selectedPlatforms) ? $selectedPlatforms : [['platform_id'=>0,'watch_link'=>'']];
      foreach ($platValues as $p):
    ?>
      <div class="platRow" style="margin-bottom:6px;">
        <select name="platform_id[]">
          <option value="0">— wybierz —</option>
          <?php foreach ($platformsAll as $pl): ?>
            <option value="<?= (int)$pl['id'] ?>" <?= ((int)($p['platform_id'] ?? 0) === (int)$pl['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($pl['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <input type="text" name="watch_link[]" placeholder="link" value="<?= htmlspecialchars($p['watch_link'] ?? '') ?>" style="width:380px;">
        <button type="button" onclick="removeRow(this)">Usuń</button>
      </div>
    <?php endforeach; ?>
  </div>
  <button type="button" onclick="addPlatform()">+ Dodaj platformę</button>

  <hr>
  <hr>
    <h4>Dodaj nowy język (opcjonalnie)</h4>
    <div id="newLanguagesWrap">
      <div style="margin-bottom:6px;">
        <input type="text" name="new_language[]" >
        <button type="button" onclick="removeRow(this)">Usuń</button>
      </div>
    </div>
    <button type="button" onclick="addNewLanguage()">+ Dodaj kolejny nowy język</button>

    <h4>Dodaj nową platformę (opcjonalnie)</h4>
    <div id="newPlatformsWrap">
      <div style="margin-bottom:6px;">
       <input type="text" name="new_platform_name[]" >
       <input type="text" name="new_platform_link[]" placeholder="link" style="width:380px;">
        <button type="button" onclick="removeRow(this)">Usuń</button>
      </div>
    </div>
    <button type="button" onclick="addNewPlatform()">+ Dodaj kolejną nową platformę</button>
  <div id="episodesSection">
    <h3>Odcinki (tylko dla serialu)</h3>
    <div id="episodesWrap">
      <?php
        $epValues = !empty($episodes) ? $episodes : [['episode_number'=>'','name'=>'']];
        foreach ($epValues as $e):
      ?>
        <div class="epRow" style="margin-bottom:6px;">
          <input type="text" name="episode_number[]" placeholder="nr" value="<?= htmlspecialchars((string)($e['episode_number'] ?? '')) ?>" style="width:60px;">
          <input type="text" name="episode_name[]" placeholder="nazwa odcinka" value="<?= htmlspecialchars($e['name'] ?? '') ?>" style="width:420px;">
          <button type="button" onclick="removeRow(this)">Usuń</button>
        </div>
      <?php endforeach; ?>
    </div>
    <button type="button" onclick="addEpisode()">+ Dodaj odcinek</button>
  </div>

  <hr>

  <button type="submit"><?= $mode === 'edit' ? 'Zapisz zmiany' : 'Dodaj' ?></button>
</form>

<script>
function removeRow(btn) {
  var row = btn.parentElement;
  row.parentElement.removeChild(row);
}

function addLanguage() {
  var wrap = document.getElementById('languagesWrap');
  var first = wrap.querySelector('.langRow');
  var clone = first.cloneNode(true);
  clone.querySelector('select').value = "0";
  wrap.appendChild(clone);
}

function addNewLanguage() {
  var wrap = document.getElementById('newLanguagesWrap');
  var first = wrap.querySelector('div');
  var clone = first.cloneNode(true);
  clone.querySelector('input').value = "";
  wrap.appendChild(clone);
}

function addPlatform() {
  var wrap = document.getElementById('platformsWrap');
  var first = wrap.querySelector('.platRow');
  var clone = first.cloneNode(true);
  clone.querySelector('select').value = "0";
  clone.querySelector('input').value = "";
  wrap.appendChild(clone);
}

function addNewPlatform() {
  var wrap = document.getElementById('newPlatformsWrap');
  var first = wrap.querySelector('div');
  var clone = first.cloneNode(true);
  clone.querySelectorAll('input')[0].value = "";
  clone.querySelectorAll('input')[1].value = "";
  wrap.appendChild(clone);
}

function addEpisode() {
  var wrap = document.getElementById('episodesWrap');
  var first = wrap.querySelector('.epRow');
  var clone = first.cloneNode(true);
  clone.querySelectorAll('input')[0].value = "";
  clone.querySelectorAll('input')[1].value = "";
  wrap.appendChild(clone);
}

function toggleEpisodes() {
  var type = document.getElementById('typeSelect').value;
  document.getElementById('episodesSection').style.display = (type === 'series') ? 'block' : 'none';
}

document.getElementById('typeSelect').addEventListener('change', toggleEpisodes);
toggleEpisodes();
</script>

</body>
</html>
