<?php

class Database
{
    // свойства, хранящие данные для подключения, естественно, делаем приватными
    private $host = "localhost";
    private $db_name = "oop_showcase";
    private $username = "root";
    private $password = "root";

    public $conn;

    // метод для подключения к бд
    public function getConnection()
    {
        $this->conn = null;

        // всегда стоит оборачивать свои PDO-операции в блок try/catch и использовать механизм исключений
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Ошибка соединения: " . $exception->getMessage(); //вывод ошибки
        }

        return $this->conn;
    }
}