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

    public static function getTopRatedByType($type, $limit = 5)
    {
        $db = Database::getConnection();

        $sql = "SELECT t.* FROM titles t
                WHERE t.type = :type
                ORDER BY t.average_rating DESC
                LIMIT :limit";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

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

}
