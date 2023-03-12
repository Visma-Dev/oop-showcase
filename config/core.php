<?php

// Страница, указанная в параметре URL. Страница по умолчанию - 1
$page = isset($_GET["page"]) ? $_GET["page"] : 1;

// устанавливаем ограничение количества записей на странице
$products_per_page = 5;

// подсчитываем лимит запроса
$offset = ($products_per_page * $page) - $products_per_page;