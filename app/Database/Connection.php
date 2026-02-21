<?php
namespace App\Database;

use App\Helpers\Env;   

use PDO;
use PDOException;

class Connection {
    public static function make(): PDO {
        $config = require __DIR__ . '/../../config/database.php';
        $db = $config['Connection'][$config['default']];

        try {
            return new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset={$db['charset']}",

                $db['username'],
                $db['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            throw new \Exception('Database Connection failed: ' . $e->getMessage());
        }
    }
}
?>