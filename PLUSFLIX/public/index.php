<?php

require_once __DIR__ . '/../src/Model/Database.php';
require_once __DIR__ . '/../src/Model/Title.php';
require_once __DIR__ . '/../src/Controller/SearchController.php';

$controller = new SearchController();
$controller->index();
