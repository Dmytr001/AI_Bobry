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

        .nav a { padding:8px 12px; text-decoration:none; border-radius:4px; display:inline-block; margin-right:8px; }
        .nav a.home { background:#333; color:#fff; }
        .nav a.search { background:#555; color:#fff; }

        .platform-btn { display: inline-block; padding: 10px 15px; background: #222; color: #fff; text-decoration: none; margin: 5px 5px 5px 0; border-radius: 4px; font-size: 0.9em; }
        .platform-btn:hover { background: #444; }

        .review-box { border: 1px solid #ccc; padding: 15px; margin-top: 10px; cursor: pointer; transition: background 0.2s; position: relative; }
        .review-box:hover { background: #f9f9f9; }

        .my-review-badge { color: #27ae60; font-weight: bold; display: none; font-size: 0.8em; margin-bottom: 5px; }

        #reviewModal { display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); }
        .modal-content { background:#fff; width:90%; max-width:500px; margin: 10% auto; padding:20px; border-radius:8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }

        textarea { width: 100%; box-sizing: border-box; }
        .btn-main { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="nav">
    <a class="home" href="/">← Polecane</a>
    <a class="search" href="/search">Wyszukiwarka</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="error-box">
        <?php foreach ($errors as $e): ?>
            <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="success-box"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($title && !empty($title['image_path'])): ?>
    <div class="title-poster" style="margin-bottom: 20px;">
        <img src="/<?= htmlspecialchars($title['image_path']) ?>"
             alt="<?= htmlspecialchars($title['name']) ?>"
             style="max-width: 300px; height: auto; border-radius: 8px;">
    </div>
<?php elseif ($title): ?>
    <div style="width: 300px; height: 450px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 20px;">
        <span>Brak okładki</span>
    </div>
<?php endif; ?>

<?php if ($title): ?>
    <h1><?= htmlspecialchars($title['name']) ?></h1>
    <div>
        <strong>Typ:</strong> <?= htmlspecialchars($title['type']) ?> |
        <strong>Rating:</strong> ⭐ <?= htmlspecialchars($title['average_rating']) ?>
    </div>
    <p><?= htmlspecialchars($title['description']) ?></p>

<?php if (!empty($languages)): ?>
    <div style="margin-top: 10px;">
        <strong>Dostępne języki:</strong>
        <?php
        $langNames = array_map(fn($l) => htmlspecialchars($l['name']), $languages);
        echo implode(', ', $langNames);
        ?>
    </div>
<?php endif; ?>

<?php if (!empty($platforms)): ?>
    <h3>Gdzie oglądać:</h3>
    <div class="platforms-list">
        <?php foreach ($platforms as $p): ?>
            <a href="<?= htmlspecialchars($p['watch_link']) ?>" class="platform-btn" target="_blank">
                <?= htmlspecialchars($p['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($title['type'] === 'series'): ?>
<hr>
    <h2>Lista odcinków</h2>
    <?php if (!empty($episodes)): ?>
    <ul style="list-style: none; padding: 0;">
        <?php foreach ($episodes as $ep): ?>
            <li style="padding: 10px; border-bottom: 1px solid #eee;">
                <strong>Odcinek <?= (int)$ep['episode_number'] ?>:</strong>
                <?= htmlspecialchars($ep['name']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Brak informacji o odcinkach.</p>
<?php endif; ?>
<?php endif; ?>

    <hr>

    <h2>Komentarze</h2>
    <button class="btn-main" onclick="openModal()">Dodaj opinię</button>

    <div id="reviews-container">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $r): ?>
                <div class="review-box" id="rev-<?= (int)$r['id'] ?>" onclick="handleReviewClick(<?= (int)$r['id'] ?>, <?= (int)$r['rating'] ?>)">
                    <div class="my-review-badge" id="badge-<?= (int)$r['id'] ?>">(Twoja opinia - kliknij, aby edytować)</div>
                    <div>⭐ <span id="rat-<?= (int)$r['id'] ?>"><?= (int)$r['rating'] ?></span> | <small><?= htmlspecialchars($r['created_at'] ?? '') ?></small></div>
                    <p id="cont-<?= (int)$r['id'] ?>" style="margin-top: 10px;"><?= nl2br(htmlspecialchars($r['content'])) ?></p>
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

                <label>Komentarz:</label><br>
                <textarea name="content" id="field_content" rows="5" required></textarea><br><br>

                <button type="submit" class="btn-main">Zapisz</button>

                <button type="button" id="delete_btn" onclick="deleteReview()"
                        style="background:#dc3545; color:#fff; border:none; padding:10px; border-radius:4px; cursor:pointer; margin-left:10px;">
                    Usuń opinię
                </button>

                <button type="button" onclick="closeModal()" style="padding: 10px;">Anuluj</button>
            </form>
        </div>
    </div>

    <script>
        // Метки "моя opinia" оставляем как у тебя: localStorage по id
        document.querySelectorAll('.review-box').forEach(box => {
            const id = box.id.replace('rev-', '');
            if (localStorage.getItem('my_review_' + id)) {
                const badge = document.getElementById('badge-' + id);
                if (badge) badge.style.display = 'block';
            }
        });

        function handleReviewClick(id, rating) {
            if (localStorage.getItem('my_review_' + id)) {
                if (confirm("Chcesz edytować lub usunąć tę opinię?")) {
                    const content = document.getElementById('cont-' + id).innerText;
                    openModal(id, rating, content);
                }
            }
        }

        function openModal(id = null, rating = 5, content = '') {
            document.getElementById('field_id').value = id || '';
            document.getElementById('field_rating').value = Math.round(rating);
            document.getElementById('field_content').value = content;

            const deleteBtn = document.getElementById('delete_btn');

            if (id) {
                document.getElementById('modalTitle').innerText = "Edytuj lub usuń opinię";
                deleteBtn.style.display = 'inline-block';
            } else {
                document.getElementById('modalTitle').innerText = "Dodaj opinię";
                deleteBtn.style.display = 'none';
            }

            document.getElementById('reviewModal').style.display = 'block';
        }

        function deleteReview() {
            const id = document.getElementById('field_id').value;
            if (!id) return;

            if (confirm("Na pewno usunąć tę opinię?")) {
                const form = document.createElement('form');
                form.method = 'POST';

                const urlParams = new URLSearchParams(window.location.search);
                form.action = '/title?id=' + urlParams.get('id');

                form.innerHTML = `<input type="hidden" name="delete_id" value="${id}">`;
                document.body.appendChild(form);

                localStorage.removeItem('my_review_' + id);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('reviewModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('reviewModal');
            if (event.target === modal) closeModal();
        }
    </script>

<?php endif; ?>

</body>
</html>
