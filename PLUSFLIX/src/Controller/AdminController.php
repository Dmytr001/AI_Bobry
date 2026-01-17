<?php

class AdminController
{
    private function db(): PDO
    {
        $pdo = Database::getConnection();
        $pdo->exec('PRAGMA foreign_keys = ON;');
        return $pdo;
    }

    private function isLoggedIn(): bool
    {
        return !empty($_SESSION['admin_id']);
    }

    private function requireAdmin(): void
    {
        if (!$this->isLoggedIn()) {
            header('Location: /admin/login');
            exit;
        }
    }

    public function loginForm(): void
    {
        if ($this->isLoggedIn()) {
            header('Location: /admin');
            exit;
        }

        $error = $_SESSION['admin_login_error'] ?? null;
        unset($_SESSION['admin_login_error']);

        require __DIR__ . '/../View/admin/login.php';
    }

    public function login(): void
    {
        $login = isset($_POST['login']) ? trim((string)$_POST['login']) : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

        if ($login === '' || $password === '') {
            $_SESSION['admin_login_error'] = 'Podaj login i hasło.';
            header('Location: /admin/login');
            exit;
        }

        $pdo = $this->db();

        $stmt = $pdo->prepare('SELECT id, login FROM admins WHERE login = :l AND password = :p LIMIT 1');
        $stmt->execute([
            ':l' => $login,
            ':p' => $password,
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            $_SESSION['admin_login_error'] = 'Niepoprawny login lub hasło.';
            header('Location: /admin/login');
            exit;
        }

        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_login'] = (string)$admin['login'];

        header('Location: /admin');
        exit;
    }

    public function dashboard(): void
    {
        $this->requireAdmin();
        require __DIR__ . '/../View/admin/dashboard.php';
    }

    public function logout(): void
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_login']);
        header('Location: /');
        exit;
    }
}
