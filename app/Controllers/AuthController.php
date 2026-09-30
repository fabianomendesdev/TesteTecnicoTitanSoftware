<?php

namespace app\Controllers;

use app\Core\Controller;
use app\Core\Session;
use app\Models\User;
use Exception;

class AuthController extends Controller
{
    public function login()
    {
        $params = request()->all();

        return view('login', [
            'email' => $params['email'] ?? ''
        ]);
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
            Session::start();
            
            $user = User::where(['email' => $validated['email'] ?? null])->first();

            if (!$user || !$user->passwordVerify($validated['password'] ?? '')) {
                Session::destroy();
                return response()->json([
                    'success' => false,
                    'message' => "Ops, Email ou Senha inválido",
                    'errors'  => [
                        'email' => ["Ops, Email ou Senha inválido"]
                    ]
                ], 400);
            }

            if (!$user->ativo) {
                return response()->json([
                    'success' => false,
                    'message' => "Sua conta está desativada.",
                ], 400);
            }

            Session::regenerateId();

            $sessionToken = bin2hex(random_bytes(32));

            $user->session_token = $sessionToken;
            $user->save(['session_token']);

            $_SESSION['id_user']       = $user->id_user;
            $_SESSION['session_token'] = $sessionToken;

            setcookie('remember_token', $sessionToken, time() + (86400 * 30), "/", "", false, true);

            return response()->json([
                'success'  => true,
                'message'  => "Login efetuado com sucesso!",
                'redirect' => route('dashboard')->getFullPath()
            ], 200);
        } catch (Exception $th) {
            Session::destroy();
            return response()->json([
                'success' => false,
                'message' => "Erro no servidor: " . $th->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        Session::destroy();
        return response()->redirectToRoute('login');
    }
}