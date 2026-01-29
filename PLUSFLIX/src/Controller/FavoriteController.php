<?php

class FavoriteController
{
    public function index()
    {
        $pdo = Database::getConnection();

        // 1. Получаем базовые данные фильмов
        $results = Title::search(
            null, null, null, null, null, null, null, 'name_asc'
        );

        // 2. Добавляем языки и платформы (вызываем новый метод)
        if (!empty($results)) {
            $results = $this->attachMetadata($pdo, $results);
        }

        require __DIR__ . '/../View/favorites.php';
    }

    /**
     * Универсальный метод для подгрузки языков и платформ.
     * Можно скопировать в другие контроллеры или вынести в Helper.
     */
    private function attachMetadata(PDO $pdo, array $results): array
    {
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

        // Привязываем данные к результатам
        foreach ($results as &$title) {
            $id = $title['id'];
            $title['languages_list'] = isset($langRows[$id]) ? array_column($langRows[$id], 'name') : [];
            $title['platforms_list'] = isset($platRows[$id]) ? array_column($platRows[$id], 'name') : [];
        }

        return $results;
    }
}