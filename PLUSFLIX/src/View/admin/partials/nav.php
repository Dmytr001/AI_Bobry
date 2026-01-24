<?php
$active = $active ?? '';
?>
<div style="border-bottom:1px solid #ccc; padding-bottom:10px; margin-bottom:15px;">
  <strong>Panel admina</strong>
  <span style="margin-left:10px; color:#666;">
    Zalogowano jako: <?= htmlspecialchars($_SESSION['admin_login'] ?? '') ?>
  </span>

  <div style="margin-top:10px;">
    <a href="/admin/movies" <?= $active === 'movies' ? 'style="font-weight:bold;"' : '' ?>>Filmy</a>
    <span> | </span>
    <a href="/admin/movies/create" <?= $active === 'movies' ? 'style="font-weight:bold;"' : '' ?>>Dodaj</a>
    <span> | </span>
    <a href="/admin/reviews" <?= $active === 'reviews' ? 'style="font-weight:bold;"' : '' ?>>Oceny</a>
    <span> | </span>
    <a href="/admin/admins/create" <?= $active === 'admins' ? 'style="font-weight:bold;"' : '' ?>>Admini</a>
    <span> | </span>

    <form method="post" action="/admin/logout" style="display:inline;">
      <button type="submit">Wyloguj</button>
    </form>
  </div>
</div>