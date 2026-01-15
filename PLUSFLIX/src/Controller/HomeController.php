<?php

class HomeController
{
    public function index()
    {
        $top5Films = Title::getTopRatedByType('film', 5);
        $top5Series = Title::getTopRatedByType('series', 5);

        require __DIR__ . '/../View/home.php';
    }
}
