<?php
session_start();

require_once __DIR__ . '/../src/Model/Database.php';
require_once __DIR__ . '/../src/Model/Title.php';
require_once __DIR__ . '/../src/Model/Review.php';
require_once __DIR__ . '/../src/Controller/SearchController.php';
require_once __DIR__ . '/../src/Controller/TitleController.php';
require_once __DIR__ . '/../src/Controller/HomeController.php';
require_once __DIR__ . '/../src/Controller/AdminController.php';
require_once __DIR__ . '/../src/Controller/AdminMovieController.php';
require_once __DIR__ . '/../src/Controller/AdminReviewController.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/admin/login' && $method === 'GET') {
    (new AdminController())->loginForm();
    exit;
}

if ($path === '/admin/login' && $method === 'POST') {
    (new AdminController())->login();
    exit;
}

if ($path === '/admin/logout') {
    (new AdminController())->logout();
    exit;
}

if ($path === '/admin' && $method === 'GET') {
    (new AdminController())->dashboard();
    exit;
}

if ($path === '/admin/movies' && $method === 'GET') {
    (new AdminMovieController())->index();
    exit;
}

if ($path === '/admin/movies/create' && $method === 'GET') {
    (new AdminMovieController())->createForm();
    exit;
}

if ($path === '/admin/movies/create' && $method === 'POST') {
    (new AdminMovieController())->store();
    exit;
}

if ($path === '/admin/movies/edit' && $method === 'GET') {
    (new AdminMovieController())->editForm();
    exit;
}

if ($path === '/admin/movies/edit' && $method === 'POST') {
    (new AdminMovieController())->update();
    exit;
}

if ($path === '/admin/movies/delete' && $method === 'POST') {
    (new AdminMovieController())->delete();
    exit;
}

if ($path === '/admin/reviews' && $method === 'GET') {
    (new AdminReviewController())->index();
    exit;
}

if ($path === '/admin/reviews/delete' && $method === 'POST') {
    (new AdminReviewController())->delete();
    exit;
}

if ($path === '/title') {
    (new TitleController())->show();
    exit;
}

if ($path === '/' || $path === '') {
    (new HomeController())->index();
    exit;
}

if ($path === '/search') {
    (new SearchController())->index();
    exit;
}

(new SearchController())->index();

http_response_code(404);
echo "404 Not Found";
