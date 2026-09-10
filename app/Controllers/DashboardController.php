<?php

namespace app\Controllers;

use app\Core\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $request = request()->all();

        return view('dashboard', [
            'request' => $request
        ]);
    }
}