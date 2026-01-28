<?php

class AdminReviewController
{
    private function db(): PDO
    {
        $pdo = Database::getConnection();
        $pdo->exec('PRAGMA foreign_keys = ON;');
        return $pdo;
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['admin_id'])) {
            $return = $_SERVER['REQUEST_URI'] ?? '/admin/reviews';
            header('Location: /admin/login?overlay=1&return=' . urlencode($return));
            exit;
        }
    }



    public function index(): void
    {
        $this->requireAdmin();

        $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $titleId = isset($_GET['title_id']) ? trim((string)$_GET['title_id']) : '';
        $rating = isset($_GET['rating']) ? trim((string)$_GET['rating']) : '';

        $pdo = $this->db();

        $sql = "
  SELECT
    r.id,
    r.title_id,
    t.name AS title_name,
    r.rating,
    r.content,
    r.created_at
  FROM reviews r
  LEFT JOIN titles t ON t.id = r.title_id
  WHERE 1=1
";

        $params = [];

        if ($q !== '') {
            $sql .= " AND r.content LIKE :q";
            $params[':q'] = '%' . $q . '%';
        }

        if ($titleId !== '' && ctype_digit($titleId)) {
            $sql .= " AND r.title_id = :tid";
            $params[':tid'] = (int)$titleId;
        }

        if ($rating !== '' && is_numeric($rating)) {
            $sql .= " AND r.rating = :r";
            $params[':r'] = $rating + 0;
        }

        $sql .= " ORDER BY r.created_at DESC, r.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../View/admin/reviews/reviewIndex.php';
    }

    public function delete(): void
    {
        $this->requireAdmin();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/reviews');
            exit;
        }

        $pdo = $this->db();
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header('Location: /admin/reviews');
        exit;
    }
}
