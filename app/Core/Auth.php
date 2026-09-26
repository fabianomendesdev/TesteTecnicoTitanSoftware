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

    public static function getInstance(): Self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function check(): bool
    {
        Session::start();

        if (isset($_SESSION['id_user'], $_SESSION['session_token'])) {
            $userId = $_SESSION['id_user'];

            $database = \app\Core\Database::getInstance();
            $result = $database->getResult(
                "SELECT session_token FROM user WHERE id_user = :id_user LIMIT 1",
                ['id_user' => [$userId, \PDO::PARAM_INT]]
            );

            $dbToken = $result[0]['session_token'] ?? null;

            if ($dbToken && hash_equals((string) $dbToken, (string) $_SESSION['session_token'])) {
                return true;
            }

            Session::destroy();
        }

        if (isset($_COOKIE['remember_token'])) {            
            $database = \app\Core\Database::getInstance();
            $result = $database->getResult(
                "SELECT id_user, session_token FROM user WHERE session_token = :token LIMIT 1",
                ['token' => [$_COOKIE['remember_token'], \PDO::PARAM_STR]]
            );

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

    public function user(): ?User
    {
        Session::start();

        if ($this->cachedUser !== null) {
            return $this->cachedUser;
        }

        if (!isset($_SESSION['id_user'], $_SESSION['session_token'])) {
            return null;
        }

        $userId = $_SESSION['id_user'];

        $this->cachedUser = User::where(['id_user' => $userId])->first();

        if (!$this->cachedUser) {
            Session::destroy();
            return null;
        }

        $database = Database::getInstance();
        $result = $database->getResult(
            "SELECT session_token FROM user WHERE id_user = :id_user LIMIT 1",
            ['id_user' => [$userId, PDO::PARAM_INT]]
        );

        $dbToken = $result[0]['session_token'] ?? null;

        if (!$dbToken || !hash_equals((string) $dbToken, (string) $_SESSION['session_token'])) {
            Session::destroy();
            $this->cachedUser = null;
            return null;
        }

        return $this->cachedUser;
    }

    public function id(): int
    {
        $user = $this->user();
        
        return $user ? $user->id_user : 0;
    }
}