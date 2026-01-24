<?php

class SearchController
{

private function db(): PDO
    {
        $pdo = Database::getConnection();
        $pdo->exec('PRAGMA foreign_keys = ON;');
        return $pdo;
    }

    private function getAllPlatformNames(PDO $pdo): array
    {
        $rows = $pdo->query("SELECT name FROM platforms ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => (string)$r['name'], $rows);
    }

    private function getAllLanguageNames(PDO $pdo): array
    {
        $rows = $pdo->query("SELECT name FROM languages ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => (string)$r['name'], $rows);
    }

    private function getAllCategories(PDO $pdo): array
    {
        $rows = $pdo->query("SELECT categories FROM titles WHERE categories IS NOT NULL AND TRIM(categories) <> ''")
                    ->fetchAll(PDO::FETCH_ASSOC);

        $set = [];
        foreach ($rows as $r) {
            $cats = (string)$r['categories'];
            foreach (preg_split('/,/', $cats) as $c) {
                $c = trim($c);
                if ($c === '') continue;
                $set[$c] = true;
            }
        }

        $cats = array_keys($set);
        sort($cats, SORT_NATURAL | SORT_FLAG_CASE);
        return $cats;
    }

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

        // Walidacja q: запрещаем < > / \ [ ] { } ;  (точка и запятая разрешены)
        if ($query !== '' && preg_match('/[<>\/\\\\\[\]\{\};]/u', $query)) {
            $errors[] = 'Pole wyszukiwania zawiera niedozwolone znaki (np. <, >, /).';
        }

        if ($min_rating !== null && $min_rating !== '' && !is_numeric($min_rating)) {
            $errors[] = 'Niepoprawna wartość: min ocena.';
        }
        if ($max_rating !== null && $max_rating !== '' && !is_numeric($max_rating)) {
            $errors[] = 'Niepoprawna wartość: max ocena.';
        }
        if (is_numeric($min_rating) && is_numeric($max_rating) && (float)$min_rating > (float)$max_rating) {
            $errors[] = 'Min ocena nie może być większa niż max ocena.';
        }

        $allowedSort = [
            'relevance',
            'rating_desc', 'rating_asc',
            'name_asc', 'name_desc'
        ];
        if (!in_array($sort, $allowedSort, true)) {
            $errors[] = 'Niepoprawne sortowanie.';
            $sort = 'relevance';
        }

        $pdo = $this->db();
        $allPlatforms  = $this->getAllPlatformNames($pdo);
        $allLanguages  = $this->getAllLanguageNames($pdo);
        $allCategories = $this->getAllCategories($pdo);
        
        $results = [];
        $hasAnyFilter = ($query !== '')
            || ($category !== null && $category !== '')
            || ($type !== null && $type !== '')
            || ($platform !== null && $platform !== '')
            || ($language !== null && $language !== '')
            || ($min_rating !== null && $min_rating !== '')
            || ($max_rating !== null && $max_rating !== '')
            || ($sort !== 'relevance');

        $results = [];

        if (empty($errors)) {
            // Всегда показываем результаты, даже если q пустое и фильтры пустые
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
