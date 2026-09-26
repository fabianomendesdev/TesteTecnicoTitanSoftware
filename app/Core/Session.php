<?php

namespace app\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function regenerateId(bool $delete_old_session = true): void
    {
        self::start();
        session_regenerate_id($delete_old_session);
    }

    public static function destroy(): void
    {
        self::start();
        
        $_SESSION = [];
        
        session_destroy();

        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
            unset($_COOKIE['remember_token']);
        }
    }
}