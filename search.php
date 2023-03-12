<?php

// файл с конфигурациями вывода товаров
include_once "config/core.php";

// файлы для работы с БД и файлы с объектами
include_once "config/database.php";
include_once "objects/product.php";
include_once "objects/category.php";

// получение соединения с БД
$database = new Database();
$db = $database->getConnection();

//создаем экземпляры классов
$product = new Product($db);
$category = new Category($db);

// получение поискового запроса
$search_term = isset($_GET["s"]) ? $_GET["s"] : "";

$page_title = "Вы искали \"{$search_term}\"";
require_once "layout/header.php";

// запрос товаров
$stmt = $product->search($search_term, $offset, $products_per_page);

// подсчитываем общее количество строк - используется для разбивки на страницы
$total_rows = $product->countSearch($search_term);

// шаблон для отображения списка товаров
include_once "read_template.php";

// содержит наш JavaScript и закрывающие теги html
require_once "layout/footer.php";