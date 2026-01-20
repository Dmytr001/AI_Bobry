<?php

class AdminUserController
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

    public function createForm(): void
    {
        $this->requireAdmin();

        $error = $_SESSION['admin_create_error'] ?? null;
        $success = $_SESSION['admin_create_success'] ?? null;
        unset($_SESSION['admin_create_error'], $_SESSION['admin_create_success']);

        require __DIR__ . '/../View/admin/admins/create.php';
    }
    public function store(): void
    {
        $this->requireAdmin();

        $login = isset($_POST['login']) ? trim((string)$_POST['login']) : '';
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

    if ($login === '' || $email === '' || $password === '') {
        $_SESSION['admin_create_error'] = 'Podaj login, email i hasło.';
        header('Location: /admin/admins/create');
        exit;
    }

        $pdo = $this->db();

        $stmt = $pdo->prepare("SELECT id FROM admins WHERE login = :l LIMIT 1");
        $stmt->execute([':l' => $login]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['admin_create_error'] = 'Taki login już istnieje.';
        header('Location: /admin/admins/create');
        exit;
    }

        $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = :e LIMIT 1");
        $stmt->execute([':e' => $email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['admin_create_error'] = 'Taki email już istnieje.';
        header('Location: /admin/admins/create');
        exit;
    }

        $stmt = $pdo->prepare("INSERT INTO admins (login, email, password) VALUES (:l, :e, :p)");
        $stmt->execute([
        ':l' => $login,
        ':e' => $email,
        ':p' => $password,
    ]);

        $_SESSION['admin_create_success'] = 'Utworzono nowego administratora: ' . $login;
        header('Location: /admin/admins/create');
    exit;
}
}
