<?php

// проверим, было ли получено значение в $_POST
if ($_POST) {

    // подключаем файлы для работы с базой данных и файлы с объектами
    include_once "config/database.php";
    include_once "objects/product.php";

    // получаем соединение с бд
    $database = new Database();
    $db = $database->getConnection();

    // подготавливаем объект Product
    $product = new Product($db);

    // устанавливаем в свойство объекта, ID товара, полученный из js скрипта
    $product->id = $_POST["object_id"];

    // удаляем товар
    if ($product->delete()) {
        echo "Товар был удалён";
    }else {
        echo "Невозможно удалить товар";
    }
}