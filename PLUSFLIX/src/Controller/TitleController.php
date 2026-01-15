<?php

class TitleController
{
    public function show()
    {
        $errors = [];
        $success = null;

        $idRaw = $_GET['id'] ?? null;
        if ($idRaw === null || $idRaw === '' || !ctype_digit((string)$idRaw)) {
            $errors[] = 'Niepoprawne ID tytułu.';
            $title = null;
            $reviews = [];
            require __DIR__ . '/../View/title.php';
            return;
        }

        $titleId = (int)$idRaw;
        $title = Title::findById($titleId);

        if (!$title) {
            $errors[] = 'Nie znaleziono tytułu.';
            $reviews = [];
            require __DIR__ . '/../View/title.php';
            return;
        }

        // Obsługa dodawania komentarza/recenzji + walidacja (FR-3.2.2 + walidacja/komunikaty)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ratingRaw = $_POST['rating'] ?? '';
            $contentRaw = trim($_POST['content'] ?? '');

            if ($ratingRaw === '' || !ctype_digit((string)$ratingRaw)) {
                $errors[] = 'Ocena musi być liczbą całkowitą.';
            } else {
                $rating = (int)$ratingRaw;
                // "gwiazdki" → najprościej 1..5 (FR-3.2.1)
                if ($rating < 1 || $rating > 5) {
                    $errors[] = 'Ocena musi być w zakresie 1–5.';
                }
            }

            if ($contentRaw === '') {
                $errors[] = 'Komentarz nie może być pusty.';
            } elseif (mb_strlen($contentRaw) > 1000) {
                $errors[] = 'Komentarz jest za długi (max 1000 znaków).';
            }

            if (empty($errors)) {
                Review::create($titleId, (int)$ratingRaw, $contentRaw);
                $success = 'Dodano komentarz.';
            }
        }

        $reviews = Review::getByTitleId($titleId);
        require __DIR__ . '/../View/title.php';
    }
}
