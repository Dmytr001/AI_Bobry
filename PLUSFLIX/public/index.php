<?php

require_once __DIR__ . '/../src/Model/Database.php';
require_once __DIR__ . '/../src/Model/Title.php';
require_once __DIR__ . '/../src/Model/Review.php';
require_once __DIR__ . '/../src/Controller/SearchController.php';
require_once __DIR__ . '/../src/Controller/TitleController.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/title') {
    (new TitleController())->show();
    exit;
}

(new SearchController())->index();
