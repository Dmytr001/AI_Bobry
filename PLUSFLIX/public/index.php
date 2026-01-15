<?php

require_once __DIR__ . '/../src/Model/Database.php';
require_once __DIR__ . '/../src/Model/Title.php';
require_once __DIR__ . '/../src/Model/Review.php';
require_once __DIR__ . '/../src/Controller/SearchController.php';
require_once __DIR__ . '/../src/Controller/TitleController.php';
require_once __DIR__ . '/../src/Controller/HomeController.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

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
