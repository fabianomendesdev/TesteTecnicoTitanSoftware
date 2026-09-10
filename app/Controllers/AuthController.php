<?php

namespace app\Controllers;

use app\Core\Controller;
use app\Models\User;

class AuthController extends Controller
{
    public static function sessionDestroy()
    {
        if (session_status() === PHP_SESSION_DISABLED) {
            session_start();
        }

        unset($_SESSION);
        session_destroy();
        unset($_COOKIE['user']);
    }

    public function login()
    {
        return view('login');
    }

    public function store()
    {
        $request = request();

        $errors = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($errors) {
            return response()->json([
                'success' => false,
                'message' => "Erro de validação",
                'errors'  => $errors
            ], 400);
        }

        $validated = $request->all();

        try {
            session_start();
            
            $user = User::where(['email' => $validated['email'] ?? null])->first();

            return response()->json([
                'success' => false,
                'message' => $user
            ], 500);


            // if (!$user || !$user->passwordVerify($validated['password'] ?? null)) {
            //     self::sessionDestroy();
            //     return response()->json([
            //         'success' => false,
            //         'message' => "Senha ou email inválidos."
            //     ], 400);
            // }

            // $_SESSION['user'] = $user;

            return response()->json([
                'success'  => true,
                'message'  => "Login efetuado com sucesso!",
                'redirect' => route('dashboard')->getFullPath()
            ], 200);
        } catch (\Throwable $th) {
            self::sessionDestroy();
            return response()->json([
                'success' => false,
                'message' => "Erro no servidor: " . $th->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        self::sessionDestroy();
        return response()->redirect(route('login')->getFullPath());
    }
}