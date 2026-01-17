<?php

class AdminMovieController
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
            header('Location: /admin/login');
            exit;
        }
    }

    public function index(): void
    {
        $this->requireAdmin();

        $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $pdo = $this->db();

        if ($q !== '') {
            $stmt = $pdo->prepare("
                SELECT id, name, description, categories, blocked_countries, image_path, average_rating
                FROM titles
                WHERE type = 'film' AND name LIKE :q
                ORDER BY id DESC
            ");
            $stmt->execute([':q' => '%' . $q . '%']);
        } else {
            $stmt = $pdo->query("
                SELECT id, name, description, categories, blocked_countries, image_path, average_rating
                FROM titles
                WHERE type = 'film'
                ORDER BY id DESC
            ");
        }

        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../View/admin/movies/movieIndex.php';
    }

    public function createForm(): void
    {
        $this->requireAdmin();

        $movie = [
            'id' => null,
            'name' => '',
            'description' => '',
            'categories' => '',
            'blocked_countries' => '',
            'image_path' => '',
            'average_rating' => 0
        ];

        $mode = 'create';
        $error = $_SESSION['admin_movie_error'] ?? null;
        unset($_SESSION['admin_movie_error']);

        require __DIR__ . '/../View/admin/movies/form.php';
    }

    public function store(): void
    {
        $this->requireAdmin();

        $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
        $description = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
        $categories = isset($_POST['categories']) ? trim((string)$_POST['categories']) : '';
        $blockedCountries = isset($_POST['blocked_countries']) ? trim((string)$_POST['blocked_countries']) : '';
        $imagePath = isset($_POST['image_path']) ? trim((string)$_POST['image_path']) : '';

        if ($name === '') {
            $_SESSION['admin_movie_error'] = 'Nazwa filmu (name) jest wymagana.';
            header('Location: /admin/movies/create');
            exit;
        }

        $pdo = $this->db();

       $stmt = $pdo->prepare("
            INSERT INTO titles (name, type, description, categories, blocked_countries, image_path, average_rating)
            VALUES (:name, 'film', :description, :categories, :blocked_countries, :image_path, :avg)
        ");

        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':categories' => $categories,
            ':blocked_countries' => $blockedCountries,
            ':image_path' => $imagePath,
            ':avg' => 0
        ]);

        header('Location: /admin/movies');
        exit;
    }

    public function editForm(): void
    {
        $this->requireAdmin();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/movies');
            exit;
        }

        $pdo = $this->db();
        $stmt = $pdo->prepare("
            SELECT id, name, description, categories, blocked_countries, image_path, average_rating
            FROM titles
            WHERE id = :id AND type = 'film'
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);

        $movie = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$movie) {
            header('Location: /admin/movies');
            exit;
        }

        $mode = 'edit';
        $error = $_SESSION['admin_movie_error'] ?? null;
        unset($_SESSION['admin_movie_error']);

        require __DIR__ . '/../View/admin/movies/form.php';
    }

    public function update(): void
    {
        $this->requireAdmin();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
        $description = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
        $categories = isset($_POST['categories']) ? trim((string)$_POST['categories']) : '';
        $blockedCountries = isset($_POST['blocked_countries']) ? trim((string)$_POST['blocked_countries']) : '';
        $imagePath = isset($_POST['image_path']) ? trim((string)$_POST['image_path']) : '';

        if ($id <= 0) {
            header('Location: /admin/movies');
            exit;
        }

        if ($name === '') {
            $_SESSION['admin_movie_error'] = 'Nazwa filmu (name) jest wymagana.';
            header('Location: /admin/movies/edit?id=' . $id);
            exit;
        }

        $pdo = $this->db();

        $stmt = $pdo->prepare("
            UPDATE titles
            SET name = :name,
                description = :description,
                categories = :categories,
                blocked_countries = :blocked_countries,
                image_path = :image_path
            WHERE id = :id AND type = 'film'
        ");

        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':categories' => $categories,
            ':blocked_countries' => $blockedCountries,
            ':image_path' => $imagePath,
            ':id' => $id
        ]);

        header('Location: /admin/movies');
        exit;
    }

    public function delete(): void
    {
        $this->requireAdmin();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/movies');
            exit;
        }

        $pdo = $this->db();
        $stmt = $pdo->prepare("DELETE FROM titles WHERE id = :id AND type = 'film'");
        $stmt->execute([':id' => $id]);

        header('Location: /admin/movies');
        exit;
    }
}
