<?php
$active = $active ?? 'movies'; // movies | movies_create | reviews | admins
$login = $_SESSION['adminlogin'] ?? '';
?>

<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="admin-group">
            <div class="admin-group__title">Produkcje</div>

            <a class="admin-link <?= $active==='movies' ? 'is-active' : '' ?>" href="/admin/movies">Szukaj</a>
            <a class="admin-link <?= $active==='movies_create' ? 'is-active' : '' ?>" href="/admin/movies/create">Dodaj</a>
        </div>

        <div class="admin-group">
            <div class="admin-group__title">Ocene</div>

            <a class="admin-link <?= $active==='reviews' ? 'is-active' : '' ?>" href="/admin/reviews">Szukaj</a>
        </div>

        <div class="admin-group admin-group--compact">
            <div class="admin-group__title">Użytkownik</div>

            <a class="admin-link <?= $active==='admins' ? 'is-active' : '' ?>" href="/admin/admins/create">Dodaj admina</a>

            <form method="post" action="/admin/logout" class="admin-logout">
                <button type="submit" class="admin-logout__btn">Logout</button>
            </form>

        </div>
    </aside>

    <main class="admin-main">
