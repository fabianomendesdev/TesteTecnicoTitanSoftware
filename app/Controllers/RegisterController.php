<?php

namespace app\Controllers;

use app\Core\Controller;
use app\Models\User;
use Exception;

class RegisterController extends Controller
{
    public function create()
    {
        $request = request()->all();

        return view('register', [
            'request' => $request
        ]);
    }

    public function store()
    {
        $request = request();

        $errors = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:user,email',
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
            $user = User::create([
                'name'     => $validated['name']  ?? '',
                'email'    => $validated['email'] ?? '',
                'password' => password_hash($validated['password'] ?? '', PASSWORD_DEFAULT),
                'ativo'    => true
            ]);

            return response()->json([
                'success'  => true,
                'message'  => "Cadastro efetuado com sucesso!",
                'redirect' => route('login')->getFullPath(['email' => $validated['email'] ?? ''])
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'success' => false,
                'message' => "Erro no servidor: " . $th->getMessage()
            ], 500);
        }
    }
}