<?php 

namespace app\Core;

use Exception;
use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __clone() {}
    private function __construct() {
        try {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', 3306);
            $user = env('DB_USERNAME', 'admin');
            $password = env('DB_PASSWORD', 'admin');
            $name = env('DB_NAME', 'db_teste_tecnico_titan');

            $this->connection = new PDO(
                "mysql:host=$host;port={$port};dbname=$name;charset=utf8mb4", 
                $user, 
                $password,
                [
                    PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection error.");
        }
    }

    public static function getInstance(): Self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConn(): PDO
    {
        return $this->connection;
    }

    public function sqlExec(string $sql, array $params = [], ?string $idColumnName = null): int
    {
        try {
            $stmt = $this->connection->prepare($sql);

            foreach ($params as $column => $param) {
                $value = $param[0] ?? null;
                $type  = $param[1] ?? PDO::PARAM_STR;

                $stmt->bindValue(":$column", $value, $type);
            }

            $stmt->execute();

            if (!$idColumnName) return (int) $this->connection->lastInsertId();

            return (int) $stmt->rowCount();
        } catch (PDOException $e) {
            throw new \Exception("Erro exec: " . $e->getMessage());
        }
    }

    public function getResult(string $sql, array $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);

            foreach ($params as $column => $param) {
                $value = $param[0] ?? null;
                $type  = $param[1] ?? PDO::PARAM_STR;

                $stmt->bindValue(":$column", $value, $type);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro query". $e->getMessage());
        }
    }
}