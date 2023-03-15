<?php

class Product
{
    // подключение к базе данных и имя таблицы
    private $conn;
    private $table_name = "products";

    // свойства объекта
    public $id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $image;
    public $timestamp;

    // в конструкторе принимаем значение метода Database->getConnection()
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // метод создания товара
    function create()
    {
        // Запрос для добавления записей в таблицу products.
        // Вместо значений подставляем именные плейсхолдеры, тем самым отделяя синтаксис запроса от значений параметров - защищаемся от инъекций.
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, price=:price, description=:description, category_id=:category_id, image=:image, created=:created";

        //подготавливаем запрос
        $stmt = $this->conn->prepare($query);

        // получаем время создания записи
        $this->timestamp = date("Y-m-d H:i:s");

        // привязываем значения к плейсхолдерам
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":created", $this->timestamp);

        //выполняем запрос
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // метод для получения товаров
    function readAll($offset, $products_per_page)
    {
        // запрос MySQL
        $query = "SELECT
                id, name, description, price, category_id
            FROM
                " . $this->table_name . "
            ORDER BY
                modified DESC
            LIMIT
                {$offset}, {$products_per_page}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // используется для пагинации товаров
    public function countAll()
    {
        // запрос MySQL
        $query = "SELECT id FROM " . $this->table_name . "";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        return $num;
    }

    // метод для получения определенного товара
    function readOne()
    {
        // запрос MySQL
        $query = "SELECT
                name, price, description, category_id, image
            FROM
                " . $this->table_name . "
            WHERE
                id = ?
            LIMIT
                0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row["name"];
        $this->price = $row["price"];
        $this->description = $row["description"];
        $this->category_id = $row["category_id"];
        $this->image = $row["image"];
    }

    // метод для обновления товара
    function update()
    {
        // запрос MySQL
        $query = "UPDATE
                " . $this->table_name . "
            SET
                name = :name,
                price = :price,
                description = :description,
                category_id  = :category_id,
                image = :image
            WHERE
                id = :id";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // привязка значений
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":id", $this->id);

        // выполняем запрос
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // метод для удаления товара
    function delete()
    {
        // запрос MySQL
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($result = $stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    // Выбираем товары по поисковому запросу
    // Параметры запроса скрываем плейсхолдерами, т.к. вывод будет на основе данных от юзера
    public function search($search_term, $offset, $products_per_page)
    {
        // запрос MySQL
        // запрос MySQL
        $query = "
        SELECT
            id, name, description, price, category_id
        FROM
            " . $this->table_name . "
        WHERE
            name LIKE ? OR description LIKE ?
        ORDER BY
            modified DESC
        LIMIT
            {$offset}, {$products_per_page}";

        // подготавливаем запрос
        $stmt = $this->conn->prepare($query);

        // привязываем значения переменных
        $search_term = "%{$search_term}%";
        $stmt->bindParam(1, $search_term);
        $stmt->bindParam(2, $search_term);

        // выполняем запрос
        $stmt->execute();

        // возвращаем значения из БД
        return $stmt;
    }

    // метод для подсчёта общего количества строк запроса
    public function countSearch($search_term)
    {
        // запрос
        $query = "SELECT
            COUNT(*) as total_rows
        FROM
            " . $this->table_name . " 
        WHERE
            name LIKE ? OR description LIKE ?";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // привязка значений
        $search_term = "%{$search_term}%";
        $stmt->bindParam(1, $search_term);
        $stmt->bindParam(2, $search_term);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row["total_rows"];
    }

    // загрузка изображения на сервер
    function uploadPhoto()
    {
        $result_message = "";

        // если свойству присвоили значение:
        if ($this->image) {

            // определяем директорию, путь и тип файла
            $target_directory = "uploads/";
            $target_file = $target_directory . $this->image;
            $file_type = pathinfo($target_file, PATHINFO_EXTENSION);

            //переменная для списка ошибок
            $file_upload_error_messages = "";

            // Проверяем файл, через функцию работы с изображениями. Если там будет не изображение - вернет false.
            $check = getimagesize($_FILES["image"]["tmp_name"]);

            if ($check !== false){} //если проверка пройдена - отправленный файл является изображением
            // если нет - заносим первую ошибочку в нашу переменную
            else { $file_upload_error_messages .= "<div>Отправленный файл не является изображением.</div>";}


            // Устанавливаем разрешенные типы файлов
            $allowed_file_types = array("jpg", "jpeg", "png", "gif");

            // если перед функцией стоит "!", - тело условия выполнится, в случае если функция вернёт false.
            if (!in_array($file_type, $allowed_file_types)) { //проверяем через функцию поиска значения в массивах
                $file_upload_error_messages .= "<div>Разрешены только файлы JPG, JPEG, PNG, GIF.</div>";
            }

            // убедимся, что отправленный файл не слишком большой (не может быть больше 1 МБ)
            if ($_FILES["image"]["size"] > (1024000)) {
                $file_upload_error_messages .= "<div>Размер изображения не должен превышать 1 МБ.</div>";
            }


            // убедимся, что папка uploads существует, если нет, то создаём
            if (!is_dir($target_directory)) {
                mkdir($target_directory, 0777, true); //аргумент permissions игнорируется в Windows.
            }


            //!! если $file_upload_error_messages всё ещё пуст - значит все проверки валидации пройдены.
            if (empty($file_upload_error_messages)) {

                // раз ошибок нет - попробуем загрузить файл
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file))

                {} // фото успешно загружено

                // ну а если нет:
                else {
                    // расскажем об этом пользователю, и лишь разведем руками
                    $result_message .= "<div class='alert alert-danger'>";
                    $result_message .= "<div>Невозможно загрузить фото.</div>";
                    $result_message .= "<div>Обновите запись, чтобы загрузить фото снова.</div>";
                    $result_message .= "</div>";
                }
            }

            // если $file_upload_error_messages все таки содержит какие-либо ошибки:
            else {

                // покажем их пользователю
                $result_message .= "<div class='alert alert-danger'>";
                $result_message .= "{$file_upload_error_messages}";
                $result_message .= "<div>Обновите запись, чтобы загрузить фото.</div>";
                $result_message .= "</div>";
            }
        }

        return $result_message;
    }

}


