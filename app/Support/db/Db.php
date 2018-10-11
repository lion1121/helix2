<?php

namespace Support\Db;

use PDO;

class Db
{
    /**
     * @return PDO
     */
    public static function getConnection($dbname = 'helix')
    {
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        try {
            $dsn = "mysql:host=localhost;dbname=$dbname;charset=utf8";
            $db = new PDO($dsn, 'root', '');
            return $db;
        } catch (\PDOException $e) {
            echo 'Check database login or password';
            die();
        }
    }
}