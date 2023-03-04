<?php

// включаем соединение с БД и файлы с объектами
include_once "config/database.php";
include_once "objects/product.php";
include_once "objects/category.php";

// установка заголовка страницы
$page_title = "Витрина со всеми товарами на любой вкус";

require_once "layout/header.php";
?>

    <div class="right-button-margin">
        <a href="create_product.php" class="btn btn-default pull-right">Добавить товар</a>
    </div>

<?php

// создаём экземпляры классов БД и объектов
$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$category = new Category($db);

// запрос товаров
$stmt = $product->readAll();
$num = $stmt->rowCount(); //возвращает кол-во строк затронутых последним запросом

// отображаем товары, если они есть
if ($num > 0) {

    echo "<table class='table table-hover table-responsive table-bordered'>";
    echo "<tr>";
    echo "<th>Товар</th>";
    echo "<th>Цена</th>";
    echo "<th>Описание</th>";
    echo "<th>Категория</th>";
    echo "<th>Действия</th>";
    echo "</tr>";


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        echo "<tr>";
        echo "<td>{$name}</td>";
        echo "<td>{$price}</td>";
        echo "<td>{$description}</td>";

        //!выводим категорию товара
        echo "<td>";
        // присваиваем свойству объекта $category - Id категории товара, принимаемое из общего запроса
        $category->id = $category_id;

        //применяем метод, результатом которого будет новое значение свойства name в объекте $category
        $category->readName();

        //И наконец, выводим свойство объекта - название категории
        echo $category->name;
        echo "</td>";


        // ссылки/кнопки для просмотра, редактирования и удаления товара
        echo "<td>";

            echo "<a href='read_product.php?id={$id}' class='btn btn-primary left-margin'>
                <span class='glyphicon glyphicon-list'></span> Просмотр
            </a>
            
            <a href='update_product.php?id={$id}' class='btn btn-info left-margin'>
                <span class='glyphicon glyphicon-edit'></span> Редактировать
            </a>
            
            <a delete-id='{$id}' class='btn btn-danger delete-object'>
                <span class='glyphicon glyphicon-remove'></span> Удалить
            </a>";
        echo "</td>";

        echo "</tr>";

    }
    echo "</table>";

    // здесь будет пагинация
}

// сообщим пользователю, если товаров нет
else {
    echo "<div class='alert alert-info'>Товары не найдены.</div>";
}
?>

<?php // подвал
require_once "layout/footer.php";