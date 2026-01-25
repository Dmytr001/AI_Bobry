<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLUSFLIX – Znajdź idealny film</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<header class="navbar">
    <a href="/" class="logo">PLUSFLIX</a>
    <div class="nav-actions">
        <input type="text" class="search-input" placeholder="Wyszukiwanie...">
        <?php if (empty($_SESSION['admin_id'])): ?>
            <a href="/admin/login" class="btn btn-login">Login</a>
        <?php else: ?>
            <a href="/admin" class="btn btn-login">Panel Admina</a>
        <?php endif; ?>
        <a href="/favorites" class="btn btn-fav">Ulubione</a>
    </div>
</header>

<section class="hero" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/Interstellar.jpg') center/cover;">
    <p style="color: #ff0000; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">
        Rozpocznij wyszukiwanie
    </p>
    <h1>Znajdź idealny film na dziś</h1>
    <a href="/search" class="btn-hero">Rozpocznij wyszukiwanie</a>
</section>

<div class="container">

    <?php if (!empty($newestTitles)): ?>
        <h2 class="section-title">Nowości w <span>PLUSFLIX</span></h2>
        <div class="movie-grid">
            <?php foreach ($newestTitles as $t): ?>
                <a href="/title?id=<?= (int)$t['id'] ?>" class="card" data-id="<?= (int)$t['id'] ?>">
                    <div class="card-img" style="background-image: url('<?= !empty($t['image_path']) ? htmlspecialchars($t['image_path']) : 'https://via.placeholder.com/300x450' ?>')">
                        <div class="rating"><span>★</span> <?= number_format($t['average_rating'], 1) ?>/5</div>
                    </div>
                    
                    <div class="card-info">
                        <span class="card-name"><?= htmlspecialchars($t['name']) ?></span>
                        <p class="card-desc"><?= htmlspecialchars($t['description']) ?></p>
                        
                        <div class="badges-container">
                            <div class="badge-list">
                                <?php 
                                if(!empty($t['categories'])):
                                    $tags = explode(',', $t['categories']);
                                    foreach(array_slice($tags, 0, 2) as $tag): ?>
                                        <span class="badge"><?= htmlspecialchars(trim($tag)) ?></span>
                                    <?php endforeach; 
                                endif; ?>
                            </div>
                            <div class="badge-list">
                                <span class="badge">Eng</span>
                                <span class="badge">Pl</span>
                            </div>
                            <div class="badge-list">
                                <span class="badge">Disney+</span>
                                <span class="badge">Netflix</span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($topRatedTitles)): ?>
        <h2 class="section-title">Najwyżej oceniane</h2>
        <div class="movie-grid">
            <?php foreach ($topRatedTitles as $t): ?>
                <a href="/title?id=<?= (int)$t['id'] ?>" class="card" data-id="<?= (int)$t['id'] ?>">
                    <div class="card-img" style="background-image: url('<?= !empty($t['image_path']) ? htmlspecialchars($t['image_path']) : 'https://via.placeholder.com/300x450' ?>')">
                        <div class="rating"><span>★</span> <?= number_format($t['average_rating'], 1) ?>/5</div>
                    </div>
                    
                    <div class="card-info">
                        <span class="card-name"><?= htmlspecialchars($t['name']) ?></span>
                        <p class="card-desc"><?= htmlspecialchars($t['description']) ?></p>
                        
                        <div class="badges-container">
                            <div class="badge-list">
                                <?php 
                                if(!empty($t['categories'])):
                                    $tags = explode(',', $t['categories']);
                                    foreach(array_slice($tags, 0, 2) as $tag): ?>
                                        <span class="badge"><?= htmlspecialchars(trim($tag)) ?></span>
                                    <?php endforeach; 
                                endif; ?>
                            </div>
                            <div class="badge-list">
                                <span class="badge">Eng</span>
                                <span class="badge">Pl</span>
                            </div>
                            <div class="badge-list">
                                <span class="badge">Disney+</span>
                                <span class="badge">Netflix</span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<footer class="footer">
    <div class="footer-col">
        <h3 class="logo" style="color: white;">Namely</h3>
        <p>Descriptive line about what your company does.</p>
        <div class="social-icons">
            <span>📷</span>
            <span>🔗</span>
            <span>✖</span>
        </div>
    </div>
    <div class="footer-col">
        <h4>Features</h4>
        <ul>
            <li>Core features</li>
            <li>Pro experience</li>
            <li>Integrations</li>
        </ul>
    </div>
    <div class="footer-col">
        <h4>Learn more</h4>
        <ul>
            <li>Blog</li>
            <li>Case studies</li>
            <li>Best practices</li>
        </ul>
    </div>
    <div class="footer-col">
        <h4>Support</h4>
        <ul>
            <li>Contact</li>
            <li>Support</li>
            <li>Legal</li>
        </ul>
    </div>
</footer>

<script>
    function checkFavorites() {
        const favorites = JSON.parse(localStorage.getItem('plusflix_favorites') || "[]");
        document.querySelectorAll('.card[data-id]').forEach(link => {
            const currentId = link.getAttribute('data-id');
            if (favorites.includes(currentId)) {
                const titleSpan = link.querySelector('.card-name');
                if (titleSpan && !titleSpan.innerHTML.includes('❤️')) {
                    titleSpan.innerHTML += ' ❤️';
                }
            }
        });
    }
    window.onload = checkFavorites;
</script>

</body>
</html>