<?php

class HomeController
{
    public function index()
    {
        $catF   = $_GET['cat_f'] ?? null;
        $platF  = $_GET['plat_f'] ?? null;
        $langF  = $_GET['lang_f'] ?? null;
        $sortF  = $_GET['sort_f'] ?? 'rating_desc';

        $catS   = $_GET['cat_s'] ?? null;
        $platS  = $_GET['plat_s'] ?? null;
        $langS  = $_GET['lang_s'] ?? null;
        $sortS  = $_GET['sort_s'] ?? 'rating_desc';

        $top5Films = Title::getTopRatedByType('film', 5, $catF, $sortF, $platF, $langF);
        $top5Series = Title::getTopRatedByType('series', 5, $catS, $sortS, $platS, $langS);

        $newestTitles = Title::getNewest(5);

        $trendyTitles = Title::getTrendyWeekly(5);

        require __DIR__ . '/../View/home.php';
    }
}
