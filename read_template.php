<?php

// форма поиска
echo "<form role='search' action='search.php'>";
echo "<div class='input-group col-md-3 pull-left margin-right-1em'>";

$search_value = isset($search_term) ? "value='{$search_term}'" : "";

echo "<input type='text' class='form-control' placeholder='Введите название или описание продукта ...' name='s' required {$search_value} />";
echo "<div class='input-group-btn'>";
echo "<button class='btn btn-primary' type='submit'><i class='glyphicon glyphicon-search'></i></button>";
echo "</div>";
echo "</div>";
echo "</form>";

// кнопка создания товара
echo "<div class='right-button-margin'>";
echo "<a href='CRUD/create_product.php' class='btn btn-primary pull-right'>";
echo "<span class='glyphicon glyphicon-plus'></span> Создать товар";
echo "</a>";
echo "</div>";

// показываем товары, если они есть
if ($total_rows > 0) {

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
        // присваиваем свойству $category - Id категории товара, принимаемое из общего запроса
        $category->id = $category_id;

        //применяем метод, результатом которого будет новое значение свойства name в объекте $category
        $category->readName();

        //И наконец, выводим свойство объекта - название категории
        echo $category->name;
        echo "</td>";


        // ссылки/кнопки для просмотра, редактирования и удаления товара
        echo "<td>";

        echo "<a href='CRUD/read_product.php?id={$id}' class='btn btn-primary left-margin'>
                <span class='glyphicon glyphicon-list'></span> Просмотр
            </a>
            
            <a href='CRUD/update_product.php?id={$id}' class='btn btn-info left-margin'>
                <span class='glyphicon glyphicon-edit'></span> Редактировать
            </a>
            
            <a delete-id='{$id}' class='btn btn-danger delete-object'>
                <span class='glyphicon glyphicon-remove'></span> Удалить
            </a>";
        echo "</td>";

        echo "</tr>";

    }

    echo "</table>";

    // пагинация
    include_once "paging.php";
}

// сообщить пользователю, что товаров нет
else {
    echo "<div class='alert alert-danger'>Ни одного товара не найдено.</div>";
}