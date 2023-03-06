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
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, price=:price, description=:description, category_id=:category_id, created=:created";

        //подготавливаем запрос
        $stmt = $this->conn->prepare($query);

        // получаем время создания записи
        $this->timestamp = date("Y-m-d H:i:s");

        // привязываем значения к плейсхолдерам
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":category_id", $this->category_id);
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
}