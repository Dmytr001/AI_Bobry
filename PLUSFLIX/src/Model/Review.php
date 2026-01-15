<?php

class Review
{
    public static function create(int $titleId, int $rating, string $content): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            INSERT INTO reviews (title_id, rating, content)
            VALUES (:title_id, :rating, :content)
        ");
        $stmt->execute([
            ':title_id' => $titleId,
            ':rating' => $rating,
            ':content' => $content
        ]);
    }

    public static function getByTitleId(int $titleId): array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT * FROM reviews
            WHERE title_id = :title_id
            ORDER BY id DESC
        ");
        $stmt->execute([':title_id' => $titleId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
