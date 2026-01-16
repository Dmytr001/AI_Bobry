<?php

class TitleController
{
    public function show()
    {
        $errors = [];
        $success = null;

        // Validate title id
        $idRaw = $_GET['id'] ?? null;
        if ($idRaw === null || $idRaw === '' || !ctype_digit((string)$idRaw)) {
            $errors[] = 'Niepoprawne ID tytułu.';
            $title = null;
            $reviews = [];
            $languages = [];
            $episodes = [];
            $platforms = [];
            require __DIR__ . '/../View/title.php';
            return;
        }

        $titleId = (int)$idRaw;
        $title = Title::findById($titleId);

        if (!$title) {
            $errors[] = 'Nie znaleziono tytułu.';
            $reviews = [];
            $languages = [];
            $episodes = [];
            $platforms = [];
            require __DIR__ . '/../View/title.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // DELETE
            if (isset($_POST['delete_id'])) {
                if (!ctype_digit((string)$_POST['delete_id'])) {
                    $errors[] = 'Niepoprawne ID opinii do usunięcia.';
                } else {
                    Review::delete((int)$_POST['delete_id']);
                    header("Location: /title?id=$titleId&success=deleted");
                    exit;
                }
            }

            // CREATE / UPDATE
            if (empty($errors)) {
                $ratingRaw = $_POST['rating'] ?? '';
                $contentRaw = trim($_POST['content'] ?? ''); // może być puste
                $reviewIdRaw = $_POST['review_id'] ?? null;

                // rating 1..5
                $rating = (int)$ratingRaw;
                if ($rating < 1 || $rating > 5) {
                    $errors[] = 'Ocena musi być liczbą od 1 do 5.';
                }

                // content: optional, but limited
                if ($contentRaw !== '' && mb_strlen($contentRaw) > 1000) {
                    $errors[] = 'Komentarz jest za długi (max 1000 znaków).';
                }

                if (empty($errors)) {
                    // UPDATE
                    if ($reviewIdRaw !== null && $reviewIdRaw !== '' && ctype_digit((string)$reviewIdRaw)) {
                        Review::update((int)$reviewIdRaw, $rating, $contentRaw);
                        header("Location: /title?id=$titleId&success=updated");
                        exit;
                    }

                    // CREATE
                    $newId = Review::create($titleId, $rating, $contentRaw);
                    header("Location: /title?id=$titleId&new_id=$newId");
                    exit;
                }
            }
        }

        // Success messages after redirect
        if (isset($_GET['success'])) {
            $map = [
                'updated' => 'Zaktualizowano opinię.',
                'deleted' => 'Usunięto opinię.',
                'created' => 'Dodano opinię.'
            ];
            $success = $map[$_GET['success']] ?? null;
        }

        $languages = Title::getLanguages($titleId);
        $episodes = Title::getEpisodes($titleId);
        $platforms = Title::getPlatforms($titleId);
        $reviews = Review::getByTitleId($titleId);

        require __DIR__ . '/../View/title.php';
    }
}
