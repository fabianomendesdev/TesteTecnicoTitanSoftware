<?php

namespace app\Controllers;

use app\Core\Controller;

class ServiceController extends Controller
{
    public function index()
    {
        $request = request()->all();

        return view('service', [
            'request' => $request
        ]);
    }
}