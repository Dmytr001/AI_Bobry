<?php

class TitleController
{
    public function show()
    {
        $errors = [];
        $success = null;

        // --- ТВОЯ СТАРАЯ ПРОВЕРКА ID ---
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

        // --- ОБНОВЛЕННАЯ ОБРАБОТКА POST (Удаление + Редактирование + Добавление) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. ПЕРВЫМ ДЕЛОМ: Проверяем запрос на удаление
            if (isset($_POST['delete_id'])) {
                $delId = (int)$_POST['delete_id'];
                Review::delete($delId);
                // После удаления сразу уходим на GET, чтобы не сработал код ниже
                header("Location: /title?id=$titleId&success=deleted");
                exit;
            }

            // 2. Сбор данных для добавления или редактирования
            $ratingRaw = $_POST['rating'] ?? '';
            $contentRaw = trim($_POST['content'] ?? '');
            $reviewId = $_POST['review_id'] ?? null; // Если заполнено — мы в режиме редактирования

            // Валидация оценки (целые числа от 1 до 5)
            $rating = (int)$ratingRaw;
            if ($rating < 1 || $rating > 5) {
                $errors[] = 'Ocena musi być liczbą od 1 do 5.';
            }

            // Валидация текста
            if ($contentRaw === '') {
                $errors[] = 'Komentarz nie może być pusty.';
            } elseif (mb_strlen($contentRaw) > 1000) {
                $errors[] = 'Komentarz jest za długi (max 1000 znaków).';
            }

            // 3. Если ошибок нет — сохраняем
            if (empty($errors)) {
                if ($reviewId && ctype_digit((string)$reviewId)) {
                    // РЕДАКТИРОВАНИЕ существующей записи
                    Review::update((int)$reviewId, $rating, $contentRaw);
                    header("Location: /title?id=$titleId&success=updated");
                    exit;
                } else {
                    // СОЗДАНИЕ новой записи
                    $newId = Review::create($titleId, $rating, $contentRaw);
                    // Передаем new_id, чтобы JavaScript во View добавил его в LocalStorage
                    header("Location: /title?id=$titleId&new_id=$newId");
                    exit;
                }
            }
        }

        // Обработка сообщения об успехе после редиректа
        if (isset($_GET['success'])) {
            $success = 'Zaktualizowano opinię.';
        }

        $languages = Title::getLanguages($titleId);
        $episodes = Title::getEpisodes($titleId);
        $platforms = Title::getPlatforms($titleId); // Метод, который мы добавили в Title.php
        $reviews = Review::getByTitleId($titleId);

        require __DIR__ . '/../View/title.php';
    }
}