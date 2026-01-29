<?php

class FavoriteController
{
    public function index()
    {
        // Вызываем поиск без фильтров, чтобы получить все записи из базы
        // Мы передаем null во все параметры, кроме сортировки
        $results = Title::search(
            null, // query
            null, // category
            null, // min_rating
            null, // max_rating
            null, // platform
            null, // type
            null, // language
            'name_asc' // sort
        );

        // Подключаем View. Проверь путь к папке View!
        require __DIR__ . '/../View/favorites.php';

    }
}