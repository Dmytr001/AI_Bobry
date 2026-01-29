<?php

class HomeController
{
    public function index()
    {
        $pdo = Database::getConnection();

        $catF   = $_GET['cat_f'] ?? null;
        $platF  = $_GET['plat_f'] ?? null;
        $langF  = $_GET['lang_f'] ?? null;
        $sortF  = $_GET['sort_f'] ?? 'rating_desc';

        $catS   = $_GET['cat_s'] ?? null;
        $platS  = $_GET['plat_s'] ?? null;
        $langS  = $_GET['lang_s'] ?? null;
        $sortS  = $_GET['sort_s'] ?? 'rating_desc';

        $top5Films = Title::getTopRatedByType('film', 5, $catF, $sortF, $platF, $langF);
        $top5Series = Title::getTopRatedByType('series', 5, $catS, $sortS, $platS, $langS);

        $topRatedTitles = Title::getTopRatedGeneral(5);
        $newestTitles = Title::getNewest(5);
        $trendyTitles = Title::getTrendyWeekly(5);

        // --- ОБОГАЩЕНИЕ ДАННЫМИ ---
        $this->attachMetadata($pdo, $top5Films);
        $this->attachMetadata($pdo, $top5Series);
        $this->attachMetadata($pdo, $topRatedTitles);
        $this->attachMetadata($pdo, $newestTitles);
        $this->attachMetadata($pdo, $trendyTitles);

        require __DIR__ . '/../View/home.php';
    }

    private function attachMetadata(PDO $pdo, array &$results): void
    {
        if (empty($results)) return;

        $ids = array_map(fn($r) => (int)$r['id'], $results);
        $placeholders = implode(',', $ids);

        // Запрос языков
        $langRows = $pdo->query("
            SELECT tl.title_id, l.name 
            FROM title_languages tl
            JOIN languages l ON tl.language_id = l.id
            WHERE tl.title_id IN ($placeholders)
        ")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

        // Запрос платформ
        $platRows = $pdo->query("
            SELECT tp.title_id, p.name 
            FROM title_platforms tp
            JOIN platforms p ON tp.platform_id = p.id
            WHERE tp.title_id IN ($placeholders)
        ")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

        // Пришиваем данные к результатам
        foreach ($results as &$title) {
            $id = $title['id'];
            $title['languages_list'] = isset($langRows[$id]) ? array_column($langRows[$id], 'name') : [];
            $title['platforms_list'] = isset($platRows[$id]) ? array_column($platRows[$id], 'name') : [];
        }
    }
}