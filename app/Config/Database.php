<?php 

namespace Irfanm\Belajar\PHP\MVC\Config;

use PDO;

class Database 
{
    private static ?\PDO $pdo= null;

    public static function getConnection(string $env = "test"): \PDO {
        if(self::$pdo == null) {
            //create PDO
            require_once __DIR__ . "/../../config/database.php";
            $config = getDatabaseConfig();
            self::$pdo = new \PDO (
              $config['database'][$env]['url'],
              $config['database'][$env]['username'],
              $config['database'][$env]['password'],
            ); 
        }

        return self::$pdo;
    }

    public static function beginTransaction()
    {
        self::$pdo->beginTransaction();
    }

    public static function commitTransactiion()
    {
        self::$pdo->commit();
    }

    public static function rollbackTransaction()
    {
        self::$pdo->rollBack();
    }
}