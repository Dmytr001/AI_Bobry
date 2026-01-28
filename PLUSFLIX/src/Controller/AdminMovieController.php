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
            $return = $_SERVER['REQUEST_URI'] ?? '/admin/movies';
            header('Location: /admin/login?overlay=1&return=' . urlencode($return));
            exit;
        }
    }



    private function getAllCategories(PDO $pdo): array
    {
        $rows = $pdo->query("SELECT categories FROM titles WHERE categories IS NOT NULL AND TRIM(categories) <> ''")
                ->fetchAll(PDO::FETCH_ASSOC);

        $set = [];
        foreach ($rows as $r) {
            foreach (preg_split('/,/', (string)$r['categories']) as $c) {
                $c = trim($c);
                if ($c === '') continue;
                $set[$c] = true;
        }
    }

        $cats = array_keys($set);
        sort($cats, SORT_NATURAL | SORT_FLAG_CASE);
        return $cats;
    }

    public function index(): void
    {
        $this->requireAdmin();

        $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $type = isset($_GET['type']) ? trim((string)$_GET['type']) : '';

        $pdo = $this->db();

        $sql = "SELECT id, name, type, average_rating FROM titles WHERE 1=1";
        $params = [];

        if ($type === 'film' || $type === 'series') {
            $sql .= " AND type = :type";
            $params[':type'] = $type;
        }

        if ($q !== '') {
            $sql .= " AND name LIKE :q";
            $params[':q'] = '%' . $q . '%';
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../View/admin/movies/movieIndex.php';
    }

    public function createForm(): void
    {
        $this->requireAdmin();

        $pdo = $this->db();

        $categoriesAll = $this->getAllCategories($pdo);

        $movie = [
            'id' => null,
            'name' => '',
            'type' => 'film',
            'description' => '',
            'categories' => '',
            'blocked_countries' => '',
            'image_path' => '',
        ];

        $languagesAll = $pdo->query("SELECT id, name FROM languages ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $platformsAll = $pdo->query("SELECT id, name FROM platforms ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        $selectedLanguageIds = [];
        $selectedPlatforms = []; 
        $episodes = [];          
        $mode = 'create';
        $error = $_SESSION['admin_movie_error'] ?? null;
        unset($_SESSION['admin_movie_error']);

        require __DIR__ . '/../View/admin/movies/form.php';
    }

    public function store(): void
    {
        $this->requireAdmin();
        $pdo = $this->db();

        $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
        $type = isset($_POST['type']) ? trim((string)$_POST['type']) : 'film';
        $description = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
        $categories = isset($_POST['categories']) ? trim((string)$_POST['categories']) : '';
        $blockedCountries = isset($_POST['blocked_countries']) ? trim((string)$_POST['blocked_countries']) : '';
        
        if ($name === '') {
            $_SESSION['admin_movie_error'] = 'Nazwa (name) jest wymagana.';
            header('Location: /admin/movies/create');
            exit;
        }

        if ($type !== 'film' && $type !== 'series') {
            $type = 'film';
        }

        $languageIds = isset($_POST['languages']) && is_array($_POST['languages']) ? $_POST['languages'] : [];
        $platformIds = isset($_POST['platform_id']) && is_array($_POST['platform_id']) ? $_POST['platform_id'] : [];
        $watchLinks  = isset($_POST['watch_link']) && is_array($_POST['watch_link']) ? $_POST['watch_link'] : [];
        $newLangs = (isset($_POST['new_language']) && is_array($_POST['new_language'])) ? $_POST['new_language'] : [];
        foreach ($newLangs as $nl) {
            $nl = trim((string)$nl);
            if ($nl === '') continue;

            $st = $pdo->prepare("SELECT id FROM languages WHERE name = :n LIMIT 1");
            $st->execute([':n' => $nl]);
            $row = $st->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $ins = $pdo->prepare("INSERT INTO languages (name) VALUES (:n)");
                $ins->execute([':n' => $nl]);
                $langId = (int)$pdo->lastInsertId();
            } else {
                $langId = (int)$row['id'];
            }

            $languageIds[] = $langId;
        }

            $newPlatNames = (isset($_POST['new_platform_name']) && is_array($_POST['new_platform_name'])) ? $_POST['new_platform_name'] : [];
            $newPlatLinks = (isset($_POST['new_platform_link']) && is_array($_POST['new_platform_link'])) ? $_POST['new_platform_link'] : [];

        $countNP = max(count($newPlatNames), count($newPlatLinks));
        for ($i=0; $i<$countNP; $i++) {
            $pn = trim((string)($newPlatNames[$i] ?? ''));
            $pl = trim((string)($newPlatLinks[$i] ?? ''));
            if ($pn === '' || $pl === '') continue;

            $st = $pdo->prepare("SELECT id FROM platforms WHERE name = :n LIMIT 1");
            $st->execute([':n' => $pn]);
            $row = $st->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $ins = $pdo->prepare("INSERT INTO platforms (name) VALUES (:n)");
                $ins->execute([':n' => $pn]);
                $pid = (int)$pdo->lastInsertId();
            } else {
                $pid = (int)$row['id'];
            }

            $platformIds[] = $pid;
            $watchLinks[] = $pl;
        }
        $episodeNumbers = isset($_POST['episode_number']) && is_array($_POST['episode_number']) ? $_POST['episode_number'] : [];
        $episodeNames   = isset($_POST['episode_name']) && is_array($_POST['episode_name']) ? $_POST['episode_name'] : [];

        $imagePath = null;
        try {
                $imagePath = $this->handleImageUpload(null);
        } catch (Throwable $e) {
                $_SESSION['admin_movie_error'] = 'Błąd obrazka: ' . $e->getMessage();
                header('Location: /admin/movies/create');
                exit;
         }   

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("
                INSERT INTO titles (name, type, description, categories, blocked_countries, image_path, average_rating)
                VALUES (:name, :type, :description, :categories, :blocked_countries, :image_path, :avg)
            ");
            $stmt->execute([
                ':name' => $name,
                ':type' => $type,
                ':description' => $description,
                ':categories' => $categories,
                ':blocked_countries' => $blockedCountries,
                ':image_path' => $imagePath,
                ':avg' => 0
            ]);

            $titleId = (int)$pdo->lastInsertId();

            $this->saveTitleLanguages($pdo, $titleId, $languageIds);

            $this->saveTitlePlatforms($pdo, $titleId, $platformIds, $watchLinks);

            if ($type === 'series') {
                $this->saveEpisodes($pdo, $titleId, $episodeNumbers, $episodeNames);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            $_SESSION['admin_movie_error'] = 'Błąd zapisu: ' . $e->getMessage();
            header('Location: /admin/movies/create');
            exit;
        }

        header('Location: /admin/movies');
        exit;
    }
    public function editForm(): void
    {
        $this->requireAdmin();
        $pdo = $this->db();
        $categoriesAll = $this->getAllCategories($pdo);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/movies');
            exit;
        }

        $stmt = $pdo->prepare("SELECT id, name, type, description, categories, blocked_countries, image_path FROM titles WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$movie) {
            header('Location: /admin/movies');
            exit;
        }

        $languagesAll = $pdo->query("SELECT id, name FROM languages ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $platformsAll = $pdo->query("SELECT id, name FROM platforms ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        $selectedLanguageIds = $pdo->prepare("SELECT language_id FROM title_languages WHERE title_id = :id");
        $selectedLanguageIds->execute([':id' => $id]);
        $selectedLanguageIds = array_map(fn($r) => (int)$r['language_id'], $selectedLanguageIds->fetchAll(PDO::FETCH_ASSOC));

        $stmtP = $pdo->prepare("SELECT platform_id, watch_link FROM title_platforms WHERE title_id = :id");
        $stmtP->execute([':id' => $id]);
        $selectedPlatforms = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        $stmtE = $pdo->prepare("SELECT episode_number, name FROM episodes WHERE title_id = :id ORDER BY episode_number ASC");
        $stmtE->execute([':id' => $id]);
        $episodes = $stmtE->fetchAll(PDO::FETCH_ASSOC);

        $mode = 'edit';
        $error = $_SESSION['admin_movie_error'] ?? null;
        unset($_SESSION['admin_movie_error']);

        require __DIR__ . '/../View/admin/movies/form.php';
    }

    public function update(): void
    {
        $this->requireAdmin();
        $pdo = $this->db();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/movies');
            exit;
        }
        $stmtOld = $pdo->prepare("SELECT image_path FROM titles WHERE id = :id LIMIT 1");
        $stmtOld->execute([':id' => $id]);
        $old = $stmtOld->fetch(PDO::FETCH_ASSOC);
        $currentPath = $old['image_path'] ?? null;

        try {
            $imagePath = $this->handleImageUpload($currentPath);
        } catch (Throwable $e) {
            $_SESSION['admin_movie_error'] = 'Błąd obrazka: ' . $e->getMessage();
            header('Location: /admin/movies/edit?id=' . $id);
            exit;
        }
        $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
        $type = isset($_POST['type']) ? trim((string)$_POST['type']) : 'film';
        $description = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
        $categories = isset($_POST['categories']) ? trim((string)$_POST['categories']) : '';
        $blockedCountries = isset($_POST['blocked_countries']) ? trim((string)$_POST['blocked_countries']) : '';
        
        if ($name === '') {
            $_SESSION['admin_movie_error'] = 'Nazwa (name) jest wymagana.';
            header('Location: /admin/movies/edit?id=' . $id);
            exit;
        }

        if ($type !== 'film' && $type !== 'series') {
            $type = 'film';
        }

        $languageIds = isset($_POST['languages']) && is_array($_POST['languages']) ? $_POST['languages'] : [];
        $platformIds = isset($_POST['platform_id']) && is_array($_POST['platform_id']) ? $_POST['platform_id'] : [];
        $watchLinks  = isset($_POST['watch_link']) && is_array($_POST['watch_link']) ? $_POST['watch_link'] : [];

        $episodeNumbers = isset($_POST['episode_number']) && is_array($_POST['episode_number']) ? $_POST['episode_number'] : [];
        $episodeNames   = isset($_POST['episode_name']) && is_array($_POST['episode_name']) ? $_POST['episode_name'] : [];

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("
                UPDATE titles
                SET name = :name,
                    type = :type,
                    description = :description,
                    categories = :categories,
                    blocked_countries = :blocked_countries,
                    image_path = :image_path
                WHERE id = :id
            ");
            $stmt->execute([
                ':name' => $name,
                ':type' => $type,
                ':description' => $description,
                ':categories' => $categories,
                ':blocked_countries' => $blockedCountries,
                ':image_path' => $imagePath,
                ':id' => $id
            ]);

            $pdo->prepare("DELETE FROM title_languages WHERE title_id = :id")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM title_platforms WHERE title_id = :id")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM episodes WHERE title_id = :id")->execute([':id' => $id]);

            $this->saveTitleLanguages($pdo, $id, $languageIds);
            $this->saveTitlePlatforms($pdo, $id, $platformIds, $watchLinks);

            if ($type === 'series') {
                $this->saveEpisodes($pdo, $id, $episodeNumbers, $episodeNames);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            $_SESSION['admin_movie_error'] = 'Błąd zapisu: ' . $e->getMessage();
            header('Location: /admin/movies/edit?id=' . $id);
            exit;
        }

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

         $pdo->prepare("DELETE FROM title_languages WHERE title_id = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM title_platforms WHERE title_id = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM episodes WHERE title_id = :id")->execute([':id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM titles WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header('Location: /admin/movies');
        exit;
    }

    private function saveTitleLanguages(PDO $pdo, int $titleId, array $languageIds): void
    {
        $seen = [];
        $stmt = $pdo->prepare("INSERT INTO title_languages (title_id, language_id) VALUES (:tid, :lid)");

        foreach ($languageIds as $lidRaw) {
            $lid = (int)$lidRaw;
            if ($lid <= 0) continue;
            if (isset($seen[$lid])) continue;
            $seen[$lid] = true;

            $stmt->execute([':tid' => $titleId, ':lid' => $lid]);
        }
    }

    private function saveTitlePlatforms(PDO $pdo, int $titleId, array $platformIds, array $watchLinks): void
    {
        $stmt = $pdo->prepare("INSERT INTO title_platforms (title_id, platform_id, watch_link) VALUES (:tid, :pid, :link)");

        $count = max(count($platformIds), count($watchLinks));
        for ($i = 0; $i < $count; $i++) {
            $pid = isset($platformIds[$i]) ? (int)$platformIds[$i] : 0;
            $link = isset($watchLinks[$i]) ? trim((string)$watchLinks[$i]) : '';

            if ($pid <= 0) continue;
            if ($link === '') continue;

            $stmt->execute([':tid' => $titleId, ':pid' => $pid, ':link' => $link]);
        }
    }

    private function saveEpisodes(PDO $pdo, int $titleId, array $episodeNumbers, array $episodeNames): void
    {
        $stmt = $pdo->prepare("INSERT INTO episodes (title_id, episode_number, name) VALUES (:tid, :num, :name)");

        $count = max(count($episodeNumbers), count($episodeNames));
        for ($i = 0; $i < $count; $i++) {
            $numRaw = isset($episodeNumbers[$i]) ? trim((string)$episodeNumbers[$i]) : '';
            $name = isset($episodeNames[$i]) ? trim((string)$episodeNames[$i]) : '';

            if ($numRaw === '' || !ctype_digit($numRaw)) continue;
            $num = (int)$numRaw;
            if ($num <= 0) continue;
            if ($name === '') continue;

            $stmt->execute([':tid' => $titleId, ':num' => $num, ':name' => $name]);
        }
    }

    private function handleImageUpload(?string $currentPath = null): ?string
{
    if (empty($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return $currentPath;
    }

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Błąd uploadu pliku (kod: ' . (int)$_FILES['image']['error'] . ')');
    }

    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Plik jest za duży (max 5MB).');
    }

    $tmp = $_FILES['image']['tmp_name'];

    $imgInfo = @getimagesize($tmp);
    if ($imgInfo === false) {
        throw new RuntimeException('To nie wygląda na obraz.');
    }

    $mime = $imgInfo['mime'] ?? '';
    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    if (!isset($extMap[$mime])) {
        throw new RuntimeException('Nieobsługiwany format obrazu: ' . $mime);
    }

    $ext = $extMap[$mime];

    $uploadDir = __DIR__ . '/../../public/images';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Nie mogę utworzyć folderu images.');
        }
    }

    $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('Nie udało się zapisać pliku na serwerze.');
    }

    return '/images/' . $fileName;
}

}
