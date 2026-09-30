<?php 

namespace app\Core;

use app\Models\User;
use PDO;

class Auth
{
    private static ?Auth $instance = null;

    private ?User $cachedUser = null;

    private function __clone() {}
    private function __construct() {}

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
     * Verifica e retorna um valor booleano referente se o usuário está logado ou não
     * 
     * @return bool
     */
    public function check(): bool
    {
        Session::start();

        // Verifica se existem dados do usuário salvo na sessão
        if (isset($_SESSION['id_user'], $_SESSION['session_token'])) {
            $userId = $_SESSION['id_user'];

            // Busca o 'session_token' salvo no banco de dados pelo id do usuário
            $database = \app\Core\Database::getInstance();
            $result = $database->getResult(
                "SELECT session_token FROM user WHERE id_user = :id_user LIMIT 1",
                ['id_user' => [$userId, \PDO::PARAM_INT]]
            );

            $dbToken = $result[0]['session_token'] ?? null;

            // Se o token salvo no banco de dados for igual ao salvo na sessão
            // então é retornado um valor verdadeiro
            if ($dbToken && hash_equals((string) $dbToken, (string) $_SESSION['session_token'])) {
                return true;
            }

            Session::destroy();
        }

        // Caso o token não esteja salvo na sessão, mas esteja salvo em cookie
        // então entra aqui e recupera ele
        if (isset($_COOKIE['remember_token'])) {
            // Busca o 'id_user' e 'session_token' salvo no banco dados pelo token salvo em cookie
            $database = \app\Core\Database::getInstance();
            $result = $database->getResult(
                "SELECT id_user, session_token FROM user WHERE session_token = :token LIMIT 1",
                ['token' => [$_COOKIE['remember_token'], \PDO::PARAM_STR]]
            );

            // Se encontrar um usuário então preenche a sessão e retorna um valor verdadeiro
            if (!empty($result)) {
                $user = $result[0];

                Session::regenerateId();

                $_SESSION['id_user']       = $user['id_user'];
                $_SESSION['session_token'] = $user['session_token'];

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna o Model User do usuário logado
     * 
     * @return ?User
     */
    public function user(): ?User
    {
        Session::start();

        // Se já existir um usuário em cache utiliza ele
        if ($this->cachedUser !== null) {
            return $this->cachedUser;
        }

        // Se não existir dados referente ao usuário na seção é retornado null
        if (!isset($_SESSION['id_user'], $_SESSION['session_token'])) {
            return null;
        }

        $userId = $_SESSION['id_user'];

        // Coloca o usuário em cache
        $this->cachedUser = User::where(['id_user' => $userId])->first();

        if (!$this->cachedUser) {
            Session::destroy();
            return null;
        }

        // Pega o 'session_token' salvo no banco de dados pelo id do usuário
        $database = Database::getInstance();
        $result = $database->getResult(
            "SELECT session_token FROM user WHERE id_user = :id_user LIMIT 1",
            ['id_user' => [$userId, PDO::PARAM_INT]]
        );

        $dbToken = $result[0]['session_token'] ?? null;

        // Se o 'session_token' salvo na sessão for diferente do salvo no banco a sessão é destruída
        if (!$dbToken || !hash_equals((string) $dbToken, (string) $_SESSION['session_token'])) {
            Session::destroy();
            $this->cachedUser = null;
            return null;
        }

        return $this->cachedUser;
    }

    /**
     * Retorna o Id do usuário logado
     * 
     * @return int
    */
    public function id(): int
    {
        // Pega o usuário logado
        $user = $this->user();
        
        return $user ? $user->id_user : 0;
    }
}