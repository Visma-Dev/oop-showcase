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

//заголовок страницы
$page_title = "Список товаров";

//головка
include_once "layout/header.php";

// шаблон каталога товаров (почти blade)
include_once "read_template.php";

// содержит наш JavaScript и закрывающие теги html
include_once "layout/footer.php";