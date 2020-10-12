<?php

class Database
{
    static private $instance = null;

    static private function connect()
    {
        $host = '127.0.0.1';
        $database = 'layprofits';
        $dbUser = 'root';
        $dbPass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
        $options =
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try
        {
            self::$instance = new PDO($dsn, $dbUser, $dbPass, $options);
        }
        catch (PDOException $e)
        {
            throw new PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    static public function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::connect();
        }
        return self::$instance;
    }
}
