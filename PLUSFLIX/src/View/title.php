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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ? htmlspecialchars($title['name']) : 'Szczegóły' ?> – PLUSFLIX</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="title.css">
</head>
<body>

    <header class="navbar">
        <a href="/" class="logo">PLUSFLIX</a>
        <div class="nav-actions">
            <a href="/" class="btn btn-login">Powrót</a>
            <a href="/favorites" class="btn btn-fav">Ulubione</a>
        </div>
    </header>

    <?php if ($title): ?>
        <div class="movie-backdrop" style="background-image: url('<?= htmlspecialchars($title['image_path']) ?>');"></div>

        <main class="title-container">
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

            <div class="title-content">
                <div class="poster-section">
                    <?php if (!empty($title['image_path'])): ?>
                        <img src="<?= htmlspecialchars($title['image_path']) ?>" alt="<?= htmlspecialchars($title['name']) ?>" class="main-poster">
                    <?php endif; ?>
                    <div class="rating-badge">★ <?= number_format((float) $title['average_rating'], 1) ?></div>
                </div>

                <div class="info-section">
                    <div class="title-header-row">
                        <h1 class="movie-title"><?= htmlspecialchars($title['name']) ?></h1>
                        <button id="favoriteBtn" class="btn-fav-action" onclick="toggleFavorite()">
                            <span id="favIcon">★</span> <span id="favText">Dodaj do ulubionych</span>
                        </button>
                    </div>

                    <div class="meta-row">
                        <span class="type-tag"><?= htmlspecialchars($title['type']) ?></span>
                        <div class="genre-list">
                            <?php if (!empty($title['categories'])):
                                foreach (explode(',', $title['categories']) as $cat): ?>
                                    <span class="genre-pill"><?= htmlspecialchars(trim($cat)) ?></span>
                                <?php endforeach;
                            endif; ?>
                        </div>
                    </div>

                    <p class="movie-description"><?= htmlspecialchars($title['description']) ?></p>

                    <?php if (!empty($languages)): ?>
                        <div class="detail-group">
                            <h3>Języki:</h3>
                            <div class="badge-row">
                                <?php foreach ($languages as $lang): ?>
                                    <span class="badge-lang"><?= htmlspecialchars($lang['name']) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($platforms)): ?>
                        <div class="detail-group">
                            <h3>Gdzie oglądać:</h3>
                            <div class="platform-grid">
                                <?php foreach ($platforms as $p): ?>
                                    <a href="<?= htmlspecialchars($p['watch_link']) ?>" class="platform-btn" target="_blank">
                                        <?= htmlspecialchars($p['name']) ?> <span>↗</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($title['type'] === 'series' && !empty($episodes)): ?>
                <section class="episodes-section">
                    <h2 class="section-title-red">Odcinki</h2>
                    <div class="episodes-scroll-container">
                        <?php foreach ($episodes as $ep): ?>
                            <div class="episode-card" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('<?= htmlspecialchars($title['image_path']) ?>');">
                                <div class="episode-number-overlay"><?= (int) $ep['episode_number'] ?></div>
                                <div class="episode-info-hover">
                                    <span class="ep-name-hover"><?= htmlspecialchars($ep['name']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <section class="reviews-section">
                <div class="section-header">
                    <h2>Opinie użytkowników</h2>
                    <button class="btn-main" onclick="handleMainButtonClick()">+ Dodaj opinię</button>
                </div>

                <div id="reviews-container" class="reviews-grid">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $r):
                            $contentTrim = trim((string) ($r['content'] ?? ''));
                            if ($contentTrim === '') continue; ?>

                            <div class="review-card" id="rev-<?= (int) $r['id'] ?>" onclick="handleReviewClick(<?= (int) $r['id'] ?>, <?= (float) $r['rating'] ?>)">
                                <div class="review-header">
                                    <div class="my-review-badge" id="badge-<?= (int) $r['id'] ?>">Twoja opinia</div>
                                    <span class="review-stars">⭐ <span id="rat-<?= (int) $r['id'] ?>"><?= number_format((float) $r['rating'], 1) ?></span></span>
                                    <span class="review-date"><?= date('d.m.Y', strtotime($r['created_at'] ?? 'now')) ?></span>
                                </div>
                                <p id="cont-<?= (int) $r['id'] ?>"><?= nl2br(htmlspecialchars($r['content'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="muted">Brak komentarzy. Bądź pierwszy!</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <div id="reviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modalTitle">Dodaj opinię</h2>
                    <span class="close-modal" onclick="closeModal()">&times;</span>
                </div>
                <form method="POST" action="/title?id=<?= (int) $title['id'] ?>">
                    <input type="hidden" name="review_id" id="field_id">
                    <div class="form-group">
                        <label>Twoja ocena:</label>
                        <select name="rating" id="field_rating" required>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>"><?= str_repeat('★', $i) ?> (<?= $i ?>/5)</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Komentarz:</label>
                        <textarea name="content" id="field_content" rows="5" placeholder="Twoja opinia..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-submit">Zapisz</button>
                        <button type="button" id="delete_btn" onclick="deleteReview()" class="btn-delete">Usuń</button>
                        <button type="button" class="btn-cancel" onclick="closeModal()">Anuluj</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const currentTitleId = urlParams.get('id');
            const titleId = "<?= (int) $title['id'] ?>";

            const newIdFromUrl = urlParams.get('new_id');
            if (newIdFromUrl && currentTitleId) {
                localStorage.setItem('my_review_for_title_' + currentTitleId, newIdFromUrl);
                localStorage.setItem('my_review_' + newIdFromUrl, 'true');
                window.history.replaceState({}, '', window.location.origin + window.location.pathname + "?id=" + currentTitleId);
            }

            document.querySelectorAll('.review-card').forEach(box => {
                const rid = box.id.replace('rev-', '');
                if (localStorage.getItem('my_review_' + rid)) {
                    const badge = document.getElementById('badge-' + rid);
                    if (badge) badge.style.display = 'inline-block';
                }
            });

            function handleMainButtonClick() {
                const existingReviewId = localStorage.getItem('my_review_for_title_' + currentTitleId);
                if (existingReviewId) { handleReviewClick(existingReviewId, 0); } else { openModal(); }
            }

            function handleReviewClick(id, rating) {
                const isMyReview = localStorage.getItem('my_review_' + id) || (localStorage.getItem('my_review_for_title_' + currentTitleId) == id);
                if (!isMyReview) return;
                if (confirm("Chcesz edytować swoją opinię?")) {
                    const contElem = document.getElementById('cont-' + id);
                    const content = (contElem && !contElem.querySelector('i')) ? contElem.innerText : '';
                    let finalRating = rating === 0 ? parseFloat(document.getElementById('rat-' + id).innerText) : rating;
                    openModal(id, finalRating, content);
                }
            }

            function openModal(id = null, rating = 5, content = '') {
                document.getElementById('field_id').value = id || '';
                document.getElementById('field_rating').value = Math.round(rating);
                document.getElementById('field_content').value = content;
                document.getElementById('delete_btn').style.display = id ? 'inline-block' : 'none';
                document.getElementById('modalTitle').innerText = id ? "Edytuj opinię" : "Dodaj opinię";
                document.getElementById('reviewModal').style.display = 'flex';
            }

            function closeModal() { document.getElementById('reviewModal').style.display = 'none'; }

            function deleteReview() {
                const id = document.getElementById('field_id').value;
                if (id && confirm("Usunąć opinię?")) {
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

            function updateFavoriteButton() {
                const btn = document.getElementById('favoriteBtn');
                const text = document.getElementById('favText');
                const icon = document.getElementById('favIcon');
                let favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");
                if (favorites.includes(titleId)) {
                    btn.classList.add('active');
                    text.innerText = "W ulubionych";
                    icon.innerText = "❤️";
                } else {
                    btn.classList.remove('active');
                    text.innerText = "Dodaj do ulubionych";
                    icon.innerText = "★";
                }
            }

            function toggleFavorite() {
                let favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");
                const index = favorites.indexOf(titleId);
                if (index > -1) { favorites.splice(index, 1); } else { favorites.push(titleId); }
                localStorage.setItem('plusflix_favorites', JSON.stringify(favorites));
                updateFavoriteButton();
            }

            window.onclick = (e) => { if (e.target == document.getElementById('reviewModal')) closeModal(); }
            document.addEventListener('DOMContentLoaded', updateFavoriteButton);
        </script>
    <?php endif; ?>

</body>
</html>