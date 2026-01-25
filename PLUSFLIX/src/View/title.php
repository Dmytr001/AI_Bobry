<?php
$title = $title ?? null;
$errors = $errors ?? [];
$reviews = $reviews ?? [];
$success = $success ?? null;
$platforms = $platforms ?? [];
$languages = $languages ?? [];
$episodes = $episodes ?? [];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= $title ? htmlspecialchars($title['name']) : 'Szczegóły' ?></title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .error-box { padding:10px; background:#ffd7d7; border:1px solid #ff9b9b; margin: 10px 0; }
        .success-box { padding:10px; background:#d7ffe1; border:1px solid #7fd69a; margin: 10px 0; }
        .platform-btn { display: inline-block; padding: 10px 15px; background: #222; color: #fff; text-decoration: none; margin: 5px 5px 5px 0; border-radius: 4px; font-size: 0.9em; }
        .platform-btn:hover { background: #444; }
        .review-box { border: 1px solid #ccc; padding: 15px; margin-top: 10px; cursor: pointer; transition: background 0.2s; position: relative; }
        .review-box:hover { background: #f9f9f9; }
        .my-review-badge { color: #27ae60; font-weight: bold; display: none; font-size: 0.8em; margin-bottom: 5px; }
        #reviewModal { display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); }
        .modal-content { background:#fff; width:90%; max-width:500px; margin: 10% auto; padding:20px; border-radius:8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        textarea { width: 100%; box-sizing: border-box; }
        .btn-main { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .muted { color:#666; font-style: italic; }
        .title-header-container {
            display: flex;
            align-items: center;
            gap: 15px; 
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div style="margin-bottom: 15px;">
    <a href="/" style="margin-right:10px;">← Polecane</a>
    <a href="/search">Wyszukiwarka</a>
    <a class="btn" href="/favorites">❤️ Moje Ulubione</a>
</div>

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

<?php if ($title): ?>
    <?php if (!empty($errors)): ?>
    <div class="error-box">
        <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="success-box"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($title['image_path'])): ?>
    <div style="margin: 20px 0;">
        <img src="<?= htmlspecialchars($title['image_path']) ?>" style="max-width: 300px; border-radius: 8px;">
    </div>
<?php endif; ?>

    <div class="title-header-container">
        <h1 style="margin: 0;"><?= htmlspecialchars($title['name']) ?></h1>

        <button id="favoriteBtn" class="favorite-btn" onclick="toggleFavorite()">
            <span id="favIcon">★</span>
            <span id="favText">Dodaj do ulubionych</span>
        </button>
    </div>

    <div>⭐ <?= htmlspecialchars($title['average_rating']) ?> | <?= htmlspecialchars($title['type']) ?></div>
    <p><?= htmlspecialchars($title['description']) ?></p>

<?php if (!empty($languages)): ?>
    <p><strong>Języki:</strong> <?= implode(', ', array_map(fn($l) => htmlspecialchars($l['name']), $languages)) ?></p>
<?php endif; ?>

<?php if (!empty($platforms)): ?>
    <h3>Gdzie oglądać:</h3>
    <?php foreach ($platforms as $p): ?>
    <a href="<?= htmlspecialchars($p['watch_link']) ?>" class="platform-btn" target="_blank">
        <?= htmlspecialchars($p['name']) ?>
    </a>
<?php endforeach; ?>
<?php endif; ?>

<?php if ($title['type'] === 'series' && !empty($episodes)): ?>
<hr><h2>Lista odcinków</h2>
    <ul style="list-style: none; padding: 0;">
        <?php foreach ($episodes as $ep): ?>
            <li style="padding: 10px; border-bottom: 1px solid #eee;">
                <strong>Odcinek <?= (int)$ep['episode_number'] ?>:</strong>
                <?= htmlspecialchars($ep['name']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

    <hr>
    <h2>Komentarze</h2>
    <button class="btn-main" onclick="handleMainButtonClick()">Dodaj opinię</button>

    <div id="reviews-container">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $r): ?>
                <?php
                $contentTrim = trim((string)($r['content'] ?? ''));
                if ($contentTrim === '') continue; // не показываем пустые, но в БД они есть
                ?>

                <?php $contentTrim = trim((string)($r['content'] ?? '')); ?>
                <div class="review-box" id="rev-<?= (int)$r['id'] ?>" onclick="handleReviewClick(<?= (int)$r['id'] ?>, <?= (float)$r['rating'] ?>)">
                    <div class="my-review-badge" id="badge-<?= (int)$r['id'] ?>">(Twoja opinia)</div>
                    <strong>⭐ <span id="rat-<?= (int)$r['id'] ?>"><?= number_format((float)$r['rating'], 1) ?></span></strong>
                    <?php if ($contentTrim !== ''): ?>
                        <p id="cont-<?= (int)$r['id'] ?>"><?= nl2br(htmlspecialchars($r['content'])) ?></p>
                    <?php else: ?>
                        <p id="cont-<?= (int)$r['id'] ?>" class="muted"><i>Brak opinii</i></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Brak komentarzy.</p>
        <?php endif; ?>
    </div>

    <div id="reviewModal">
        <div class="modal-content">
            <h2 id="modalTitle">Dodaj opinię</h2>
            <form method="POST" action="/title?id=<?= (int)$title['id'] ?>">
                <input type="hidden" name="review_id" id="field_id">

                <label>Ocena:</label><br>
                <select name="rating" id="field_rating" required>
                    <?php for ($i=1; $i<=5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select><br><br>

                <label>Komentarz (opcjonalnie):</label><br>
                <textarea name="content" id="field_content" rows="5"></textarea><br><br>

                <button type="submit" class="btn-main">Zapisz</button>
                <button type="button" id="delete_btn"
                        onclick="deleteReview()"
                        style="background:#dc3545; color:#fff; border:none; padding:10px; border-radius:4px; margin-left:10px;">
                    Usuń
                </button>
                <button type="button" onclick="closeModal()">Anuluj</button>
            </form>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const currentTitleId = urlParams.get('id');

        // init localStorage after create (new_id)
        const newIdFromUrl = urlParams.get('new_id');
        if (newIdFromUrl && currentTitleId) {
            localStorage.setItem('my_review_for_title_' + currentTitleId, newIdFromUrl);
            localStorage.setItem('my_review_' + newIdFromUrl, 'true');
            const cleanUrl = window.location.origin + window.location.pathname + "?id=" + currentTitleId;
            window.history.replaceState({}, '', cleanUrl);
        }

        // show badge
        document.querySelectorAll('.review-box').forEach(box => {
            const rid = box.id.replace('rev-', '');
            if (localStorage.getItem('my_review_' + rid)) {
                const badge = document.getElementById('badge-' + rid);
                if (badge) badge.style.display = 'block';
            }
        });

        function handleMainButtonClick() {
            const existingReviewId = localStorage.getItem('my_review_for_title_' + currentTitleId);
            if (existingReviewId) {
                handleReviewClick(existingReviewId, 0);
            } else {
                openModal();
            }
        }

        function handleReviewClick(id, rating) {
            const isMyReview = localStorage.getItem('my_review_' + id) ||
                (localStorage.getItem('my_review_for_title_' + currentTitleId) == id);

            if (!isMyReview) return;

            if (confirm("Już oceniłeś ten tytuł. Czy chcesz edytować lub usunąć swoją opinię?")) {
                const contElem = document.getElementById('cont-' + id);

                // если там "Brak opinii" в <i>, то считаем контент пустым
                const content = (contElem && !contElem.querySelector('i')) ? contElem.innerText : '';

                let finalRating = rating;
                if (rating === 0) {
                    const ratElem = document.getElementById('rat-' + id);
                    finalRating = ratElem ? parseFloat(ratElem.innerText) : 5;
                }

                openModal(id, finalRating, content);
            }
        }

        function openModal(id = null, rating = 5, content = '') {
            document.getElementById('field_id').value = id || '';
            document.getElementById('field_rating').value = Math.round(rating);
            document.getElementById('field_content').value = content;

            document.getElementById('delete_btn').style.display = id ? 'inline-block' : 'none';
            document.getElementById('modalTitle').innerText = id ? "Edytuj opinię" : "Dodaj opinię";
            document.getElementById('reviewModal').style.display = 'block';
        }

        function deleteReview() {
            const id = document.getElementById('field_id').value;
            if (id && confirm("Czy na pewno chcesz usunąć tę opinię?")) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/title?id=' + currentTitleId;
                form.innerHTML = `<input type="hidden" name="delete_id" value="${id}">`;
                document.body.appendChild(form);

                localStorage.removeItem('my_review_' + id);
                localStorage.removeItem('my_review_for_title_' + currentTitleId);

                form.submit();
            }
        }

        function closeModal() { document.getElementById('reviewModal').style.display = 'none'; }

        window.onclick = function(event) {
            if (event.target == document.getElementById('reviewModal')) closeModal();
        }

        // Получаем ID текущего фильма из PHP
        const titleId = "<?= (int)$title['id'] ?>";

        // Функция для обновления внешнего вида кнопки
        function updateFavoriteButton() {
            const btn = document.getElementById('favoriteBtn');
            const text = document.getElementById('favText');
            const icon = document.getElementById('favIcon');

            // Получаем массив избранного из localStorage
            let favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");

            if (favorites.includes(titleId)) {
                btn.classList.add('active');
                text.innerText = "Usuń z ulubionych";
                icon.innerText = "❤️";
            } else {
                btn.classList.remove('active');
                text.innerText = "Dodaj do ulubionych";
                icon.innerText = "★";
            }
        }

        // Функция переключения (добавить/удалить)
        function toggleFavorite() {
            let favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");
            const index = favorites.indexOf(titleId);

            if (index > -1) {
                // Если уже есть — удаляем
                favorites.splice(index, 1);
            } else {
                // Если нет — добавляем
                favorites.push(titleId);
            }

            // Сохраняем обновленный массив
            localStorage.setItem('plusflix_favorites', JSON.stringify(favorites));

            // Обновляем кнопку
            updateFavoriteButton();
        }

        // Запускаем проверку при загрузке страницы
        document.addEventListener('DOMContentLoaded', updateFavoriteButton);
    </script>
<?php endif; ?>

</body>
</html>
