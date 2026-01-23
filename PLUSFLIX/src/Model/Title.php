<?php

class Title
{
    public static function search($query=null, $category=null, $min_rating=null, $max_rating=null, $platform=null, $type=null, $language=null, $sort='relevance')
    {
        $db = Database::getConnection();
        $sql = "SELECT DISTINCT t.* FROM titles t";
        $params = [];

        if ($platform) {
            $sql .= " INNER JOIN title_platforms tp ON t.id = tp.title_id
                  INNER JOIN platforms p ON tp.platform_id = p.id";
        }

        if ($language) {
            $sql .= " INNER JOIN title_languages tl ON t.id = tl.title_id
                  INNER JOIN languages l ON tl.language_id = l.id";
        }

        $sql .= " WHERE 1=1";

        if ($type) {
            $sql .= " AND t.type = :type";
            $params[':type'] = $type;
        }

        if ($query) {
            $sql .= " AND (t.name LIKE :query OR t.description LIKE :query)";
            $params[':query'] = "%$query%";
        }

        if ($category) {
            $sql .= " AND t.categories LIKE :category";
            $params[':category'] = "%$category%";
        }

        if ($min_rating !== null && $min_rating !== '') {
            $sql .= " AND t.average_rating >= :min_rating";
            $params[':min_rating'] = $min_rating;
        }

        if ($max_rating !== null && $max_rating !== '') {
            $sql .= " AND t.average_rating <= :max_rating";
            $params[':max_rating'] = $max_rating;
        }

        if ($platform) {
            $sql .= " AND p.name = :platform";
            $params[':platform'] = $platform;
        }

        if ($language) {
            $sql .= " AND l.name = :language";
            $params[':language'] = $language;
        }

        // Sortowanie wyników (WBS: sortowanie wyników)
        switch ($sort) {
            case 'rating_desc':
                $sql .= " ORDER BY t.average_rating DESC";
                break;
            case 'rating_asc':
                $sql .= " ORDER BY t.average_rating ASC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY t.name ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY t.name DESC";
                break;
            
            default:
                // relevance: без доп. логики ранжирования
                break;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTopRatedByType($type, $limit = 5, $category = null, $sort = 'relevance', $platform = null, $language = null)
{
    $db = Database::getConnection();

    $sql = "SELECT DISTINCT t.* FROM titles t";

    if ($platform) {
        $sql .= " INNER JOIN title_platforms tp ON t.id = tp.title_id
                  INNER JOIN platforms p ON tp.platform_id = p.id";
    }

    if ($language) {
        $sql .= " INNER JOIN title_languages tl ON t.id = tl.title_id
                  INNER JOIN languages l ON tl.language_id = l.id";
    }

    $sql .= " WHERE t.type = :type";

    if ($category) {
        $sql .= " AND t.categories LIKE :category";
    }

    if ($platform) {
        $sql .= " AND p.name = :platform";
    }

    if ($language) {
        $sql .= " AND l.name = :language";
    }

    switch ($sort) {
        case 'rating_desc': $sql .= " ORDER BY t.average_rating DESC"; break;
        case 'rating_asc':  $sql .= " ORDER BY t.average_rating ASC"; break;
        case 'name_asc':    $sql .= " ORDER BY t.name ASC"; break;
        case 'name_desc':   $sql .= " ORDER BY t.name DESC"; break;
        default:            $sql .= " ORDER BY t.average_rating DESC";
    }

    $sql .= " LIMIT :limit";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':type', $type, PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

    if ($category) {
        $stmt->bindValue(':category', '%' . $category . '%', PDO::PARAM_STR);
    }
    if ($platform) {
        $stmt->bindValue(':platform', $platform, PDO::PARAM_STR);
    }
    if ($language) {
        $stmt->bindValue(':language', $language, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public static function getTopRatedGeneral($limit)
    {
        $db = Database::getConnection();

        // Нам нужен максимально простой запрос для производительности
        $sql = "SELECT * FROM titles 
            ORDER BY average_rating DESC 
            LIMIT :limit";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getNewest($limit = 5)
    {
        $db = Database::getConnection();

        $sql = "SELECT * FROM titles 
            ORDER BY id DESC 
            LIMIT :limit";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTrendyWeekly($limit = 5)
{
    $db = Database::getConnection();

    $sqlAll = "SELECT id FROM titles";
    $stmtAll = $db->query($sqlAll);
    $allIds = $stmtAll->fetchAll(PDO::FETCH_COLUMN);

    if (empty($allIds)) return [];

    $seed = (int)date('Y') + (int)date('W');
    srand($seed);
    
    shuffle($allIds);
    $randomIds = array_slice($allIds, 0, $limit);

    $idsString = implode(',', array_map('intval', $randomIds));
    
    $sql = "SELECT * FROM titles WHERE id IN ($idsString)";
    $stmt = $db->query($sql);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM titles WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function getPlatforms(int $titleId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.name, tp.watch_link FROM platforms p JOIN title_platforms tp ON p.id = tp.platform_id WHERE tp.title_id = :title_id ");
        $stmt->execute([':title_id' => $titleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLanguages(int $titleId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT l.name FROM languages l JOIN title_languages tl ON l.id = tl.language_id WHERE tl.title_id = :title_id");
        $stmt->execute([':title_id' => $titleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEpisodes(int $titleId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(" SELECT episode_number, name  FROM episodes WHERE title_id = :title_id ORDER BY episode_number ASC ");
        $stmt->execute([':title_id' => $titleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
