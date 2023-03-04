<?php

class Category
{
    // подключение к базе и имя таблицы
    private $conn;
    private $table_name = "categories";

    // свойства объекта
    public $id;
    public $name;

    // в конструкторе принимаем значение метода Database->getConnection()
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // данный метод используется в раскрывающемся списке, при создании продукта
    function read()
    {
        // запрос MySQL: выбираем столбцы в таблице «categories»
        //здесь никакой защиты не потребуется, так как мы не используем в запросе данные от пользователя
        $query = "SELECT id, name FROM " . $this->table_name . " ORDER BY name";

        //подготавливаем запрос
        $stmt = $this->conn->prepare($query);

        //на этом этапе при желании/необходимости можно отформатировать и забиндить параметры запроса, перед его выполнением

        // и выполняем
        $stmt->execute();

        return $stmt;
    }

    // получение названия категории по её ID
    function readName()
    {
        // запрос MySQL
        $query = "SELECT name FROM " . $this->table_name . " WHERE id = ?"; // скрываем параметр запроса

        $stmt = $this->conn->prepare($query);


        $stmt->bindParam(1, $this->id); //биндим плейсхолдер

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row["name"]; //присваиваем свойству name новое значение
    }


}