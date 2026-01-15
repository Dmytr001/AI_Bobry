<?php

class SearchController
{
    public function index()
    {
        $errors = [];

        $query      = trim($_GET['q'] ?? '');
        $category   = $_GET['category'] ?? null;
        $type       = $_GET['type'] ?? null;
        $min_rating = $_GET['min_rating'] ?? null;
        $max_rating = $_GET['max_rating'] ?? null;
        $platform   = $_GET['platform'] ?? null;
        $language   = $_GET['language'] ?? null;
        $sort       = $_GET['sort'] ?? 'relevance';

        // Walidacja danych i komunikaty (FR-3.1.6)
        if ($min_rating !== null && $min_rating !== '' && !is_numeric($min_rating)) {
            $errors[] = 'Niepoprawna wartość: min ocena.';
        }
        if ($max_rating !== null && $max_rating !== '' && !is_numeric($max_rating)) {
            $errors[] = 'Niepoprawna wartość: max ocena.';
        }
        if (is_numeric($min_rating) && is_numeric($max_rating) && (float)$min_rating > (float)$max_rating) {
            $errors[] = 'Min ocena nie może być większa niż max ocena.';
        }

        // Sortowanie wyników (WBS: sortowanie wyników)
        $allowedSort = ['relevance', 'rating_desc', 'rating_asc', 'name_asc', 'name_desc'];
        if (!in_array($sort, $allowedSort, true)) {
            $errors[] = 'Niepoprawne sortowanie.';
            $sort = 'relevance';
        }

        // Polecane treści (FR-3.3.1) – Top 5 filmów i Top 5 seriali
        $top5Films = Title::getTopRatedByType('film', 5);
        $top5Series = Title::getTopRatedByType('series', 5);

        $results = [];
        if (empty($errors)) {
            $results = Title::search(
                $query ?: null,
                $category ?: null,
                ($min_rating !== '' ? $min_rating : null),
                ($max_rating !== '' ? $max_rating : null),
                $platform ?: null,
                $type ?: null,
                $language ?: null,
                $sort
            );
        }

        require __DIR__ . '/../View/search.php';
    }
}
