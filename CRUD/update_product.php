<?php

// получаем ID редактируемого товара
$id = isset($_GET["id"]) ? $_GET["id"] : die("ERROR: отсутствует ID.");

// подключаем файлы для работы с базой данных и файлы с объектами
include_once "../config/database.php";
include_once "../objects/product.php";
include_once "../objects/category.php";

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// подготавливаем объекты
$product = new Product($db);
$category = new Category($db);

// устанавливаем свойство ID товара для редактирования
$product->id = $id;

// !получаем информацию о редактируемом товаре
$product->readOne();

$page_title = "Обновление товара";

include_once "../layout/header.php";
?>

    <div class="right-button-margin">
        <a href="../index.php" class="btn btn-default pull-right">Просмотр всех товаров</a>
    </div>


<?php

// если форма была отправлена
if ($_POST) {

    // устанавливаем значения свойствам товара
    $product->name = $_POST["name"];
    $product->price = $_POST["price"];
    $product->description = $_POST["description"];
    $product->category_id = $_POST["category_id"];
    $image = !empty($_FILES["image"]["name"])
        //уникализируем название изображения с помощью временной метки
        ? time() . "_" .$_FILES["image"]["name"] : "";
    $product->image = $image;

    // обновление товара
    if ($product->update()) {
        echo "<div class='alert alert-success alert-dismissable'>Товар был обновлён.</div>";
        // пытаемся загрузить отправленный файл
        // метод uploadPhoto() вернет сообщение об ошибке, в случае неудачи валидации
        echo $product->uploadPhoto();
    }

    // если не удается обновить товар, сообщим об этом пользователю
    else {
        echo "<div class='alert alert-danger alert-dismissable'>";
        echo "Невозможно обновить товар.";
        echo "</div>";
    }
}
?>

    <!-- форма -->
    <form
            action="<?= htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}"); ?>"
            method="post"
            enctype="multipart/form-data"
    >
        <table class="table table-hover table-responsive table-bordered">

            <tr>
                <td>Название</td>
                <td><input type="text" name="name" value="<?= $product->name; ?>" class="form-control" /></td>
            </tr>

            <tr>
                <td>Цена</td>
                <td><input type="text" name="price" value="<?= $product->price; ?>" class="form-control" /></td>
            </tr>

            <tr>
                <td>Описание</td>
                <td><textarea name="description" class="form-control"><?= $product->description; ?></textarea></td>
            </tr>

            <tr>
                <td>Категория</td>
                <td>
                    <?php
                    $stmt = $category->read();
                    echo "<select class='form-control' name='category_id'>";
                    echo "<option selected disabled value=''>Выберите категорию</option>";

                    // помещаем категории в выпадающий список
                    while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $category_id = $row_category["id"];
                        $category_name = $row_category["name"];

                        // указываем какая категория была выбрана при создании
                        if ($product->category_id == $category_id) {
                            echo "<option value='$category_id' selected>";
                        }
                        else { //остальные выводим как обычные опции
                            echo "<option value='$category_id'>";
                        }
                        echo "$category_name</option>";
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
                    <button type="submit" class="btn btn-primary">Обновить</button>
                </td>
            </tr>

        </table>
    </form>




<?php // подвал
require_once "../layout/footer.php";