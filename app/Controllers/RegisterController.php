<?php

namespace app\Controllers;

use app\Core\Controller;

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
        $request = request()->all();

        return view('register', [
            'request' => $request
        ]);
    }
}