<?php

class SearchController
{
    public function index()
    {
        $query = $_GET['q'] ?? null;
        $category = $_GET['category'] ?? null;
        $type = $_GET['type'] ?? null;
        $min_rating = $_GET['min_rating'] ?? null;
        $max_rating = $_GET['max_rating'] ?? null;
        $platform = $_GET['platform'] ?? null;
        $language = $_GET['language'] ?? null;

        $results = Title::search($query, $category, $min_rating, $max_rating, $platform, $type, $language);

        require __DIR__ . '/../View/search.php';
    }
}

