<?php
class Database
{
    public function connect()
    {
        $config = require __DIR__ . '/../config/database.php';
        $host = $config['host'];
        $name = $config['name'];

        try {
            $db = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $config['user'], $config['password']);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $db;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            exit('Database connection failed. Check config/database.php.');
        }
    }
}
