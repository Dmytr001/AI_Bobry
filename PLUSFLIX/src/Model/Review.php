<?php

class Review
{
    public static function create(int $titleId, float $rating, string $content): int
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(" INSERT INTO reviews (title_id, rating, content) VALUES (:title_id, :rating, :content) ");
        $stmt->execute([
            ':title_id' => $titleId,
            ':rating'    => $rating,
            ':content'   => $content
        ]);

        return (int)$db->lastInsertId();
    }

    public static function update(int $id, float $rating, string $content): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE reviews SET rating = :rating, content = :content  WHERE id = :id");
        $stmt->execute([
            ':id'      => $id,
            ':rating'  => $rating,
            ':content' => $content
        ]);
    }

    public static function getByTitleId(int $titleId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM reviews WHERE title_id = :title_id ORDER BY id DESC ");
        $stmt->execute([':title_id' => $titleId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}