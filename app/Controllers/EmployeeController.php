<?php

namespace app\Controllers;

use app\Core\Controller;

class EmployeeController extends Controller
{
    public function index()
    {
        $request = request()->all();

        return view('employee', [
            'request' => $request
        ]);
    }
}