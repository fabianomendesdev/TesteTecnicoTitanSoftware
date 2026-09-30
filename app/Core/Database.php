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
            $name = env('DB_NAME', 'db_sistema_controle_servico');

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

    /**
     * Retorna a instância única (Singleton) da classe.
     * 
     * @return self
     */
    public static function getInstance(): Self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Retorna a conexão PDO
     * 
     * @return PDO
     */
    public function getConn(): PDO
    {
        return $this->connection;
    }

    /**
     * Executa um SQL no banco de dados
     * Caso o $idColumnName esteja sendo passado por parametro
     * é retornado o último id inserido na tabela
     * Caso não tenha passado $idColumnName retorna a quantidade 
     * de linhas alteradas no banco de dados
     * 
     * @param string $sql
     * @param array $params
     * @param ?string $idColumnName
     * @return int
     */
    public function sqlExec(string $sql, array $params = [], ?string $idColumnName = null): int
    {
        try {
            $stmt = $this->connection->prepare($sql);

            // Percore todos os parametros recebidos e faz o bindValue
            foreach ($params as $column => $param) {
                $value = $param[0] ?? null;
                $type  = $param[1] ?? PDO::PARAM_STR;

                $stmt->bindValue(":$column", $value, $type);
            }

            $stmt->execute();

            if (!$idColumnName) return (int) $this->connection->lastInsertId();

            // Retorna a quantidade de linhas alteradas
            return (int) $stmt->rowCount();
        } catch (PDOException $e) {
            throw new \Exception("Erro exec: " . $e->getMessage());
        }
    }

    /**
     * Executa um SQL e retorna os dados buscados no banco de dados
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getResult(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);

            // Percore todos os parametros recebidos e faz o bindValue
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