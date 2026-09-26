<?php

namespace app\Controllers;

use app\Core\Controller;
use app\Core\Session;
use app\Models\Service;
use app\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $request = request()->all();

        Session::start();
        
        $userId = auth()->id();

        $allServicesUser = Service::where([
            'user_id_user' => $userId
        ])->getAll();

        $totService = 0;
        foreach ($allServicesUser as $service) {
            $totService += $service->price ?? 0;
        }

        $filterDescricao = $request['descricao'] ?? '';
        $filterDataIni   = $request['data_ini']  ?? '';
        $filterDataFim   = $request['data_fim']  ?? '';
        $filterStatus    = $request['status']    ?? '';
        $filterIdUser    = $request['id_user']   ?? '';

        $filters = [];
        
        if ($filterDescricao) $filters[] = ['description', 'like', "%$filterDescricao%"];
        
        if ($filterStatus) {
            switch (strtoupper($filterStatus)) {
                case 'P':
                    $filters[] = ['finished_at', '=', null];
                    break;
                case 'F':
                    $filters[] = ['finished_at', '!=', null];
                    break;
            }
        }

        if ($filterDataIni || $filterDataFim) {
            if ($filterDataIni && $filterDataFim) {
                $filters[] = ['created_at', 'between', $filterDataIni, $filterDataFim];
            } elseif ($filterDataIni) {
                $filters[] = ['created_at', '>=', $filterDataIni];
            } elseif ($filterDataFim) {
                $filters[] = ['created_at', '<=', $filterDataFim];
            }
        }

        if ($filterIdUser) $filters[] = ['user_id_user', '=', $filterIdUser];

        $allServices = Service::where($filters)->getAll();

        foreach ($allServices as $serivce) {
            $userId = $serivce->user_id_user;
            if ($userId) {
                $serivce->user = User::where(['id_user' => $userId])->first();
            }
        }

        $recentServices = Service::where([
            ['user_id_user', '=', $userId],
            ['finished_at', '!=', null],
        ])->limit(5)->getAll();

        $pendentServices = Service::where([
            'user_id_user' => $userId,
            'finished_at'  => null
        ])->limit(10)->getAll();

        $users = User::getAll();

        return view('dashboard', [
            'totService'      => $totService      ?? 0,
            'allServices'     => $allServices     ?? [],
            'recentServices'  => $recentServices  ?? [],
            'pendentServices' => $pendentServices ?? [],
            'users'           => $users           ?? []
        ]);
    }
}