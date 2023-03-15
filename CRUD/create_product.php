<?php

// включим файлы, необходимые для подключения к бд и файлы с объектами
include_once "../config/database.php";
include_once "../objects/product.php";
include_once "../objects/category.php";

// создаем экземпляр класса бд и образ подключения
$database = new Database();
$db = $database->getConnection();

// создаем экземпляры классов Product и Category, добавляя метод подключения, как обязательный параметр
$product = new Product($db);
$category = new Category($db);


// установка заголовка страницы
$page_title = "Выгружаем и выкладываем товар на витрину";


require_once "../layout/header.php";
?>

    <div class="right-button-margin">
        <a href="../index.php" class="btn btn-default pull-right">Просмотр всех товаров</a>
    </div>

    <!-- форма для создания товара -->
    <form
            action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"
            method="post"
            enctype="multipart/form-data"
    >

        <table class="table table-hover table-responsive table-bordered">

            <tr>
                <td>Название</td>
                <td><input type="text" name="name" class="form-control" /></td>
            </tr>

            <tr>
                <td>Цена</td>
                <td><input type="text" name="price" class="form-control" /></td>
            </tr>

            <tr>
                <td>Описание</td>
                <td><textarea name="description" class="form-control"></textarea></td>
            </tr>

            <tr>
                <td>Категория</td>
                <td>
                    <?php

                    // читаем категории товаров из базы данных
                    $stmt = $category->read();

                    echo "<select class='form-control' name='category_id'>";
                    echo "<option>Выбрать категорию товара</option>";

                    //принимаем данные в качестве ASSOC массива, после чего выводим в качестве выпадающих опций
                    while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row_category);
                        echo "<option value='{$id}'>{$name}</option>";
                    }
                    echo "</select>";
                    ?>
                </td>
            </tr>

            <tr>
                <td>Изображение</td>
                <td><input type="file" name="image" /></td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <button type="submit" class="btn btn-primary">Добавить!</button>
                </td>
            </tr>

        </table>

    </form>

<?php

// если форма была отправлена
if ($_POST)
{
    // присваиваем значения свойствам товара
    $product->name = $_POST["name"];
    $product->price = $_POST["price"];
    $product->description = $_POST["description"];
    $product->category_id = $_POST["category_id"];
    $image = !empty($_FILES["image"]["name"])
        //уникализируем название изображения с помощью временной метки
        ? time() . "_" .$_FILES["image"]["name"] : "";
    $product->image = $image;

    // применяем ранее созданный метод, для создания товара
    if ($product->create()) {
        echo '<div class="alert alert-success">Товар был успешно создан.</div>';
        // пытаемся загрузить отправленный файл
        // метод uploadPhoto() вернет сообщение об ошибке, в случае неудачи валидации
        echo $product->uploadPhoto();
    }

    // если не удается создать товар, сообщим об этом пользователю
    else {
        echo '<div class="alert alert-danger">Ошибка добавления товара</div>';
    }
}
?>

<?php // подвал
require_once "../layout/footer.php";
?>