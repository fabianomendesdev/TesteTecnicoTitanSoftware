<?php

namespace app\Controllers;

use app\Core\Controller;
use app\Models\Service;
use DateTime;
use Exception;

class ServiceController extends Controller
{
    public function create()
    {
        $request = request()->all();

        return view('create.service', [
            'request' => $request
        ]);
    }

    public function store()
    {
        $request = request();

        $errors = $request->validate([
            'description' => 'required|string',
            'price'       => 'required|numeric'
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
            $service = Service::create([
                'description'  => $validated['description'] ?? '',
                'price'        => $validated['price'] ?? 0,
                'user_id_user' => auth()->id(),
            ]);

            return response()->json([
                'success'  => true,
                'message'  => "Cadastro efetuado com sucesso!",
                'redirect' => route('dashboard')->getFullPath()
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'success' => false,
                'message' => "Erro no servidor: " . $th->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id_service)
    {
        $service = Service::where(['id_service' => $id_service])->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => "Serviço não encontrado!"
            ], 404);
        }

        $code = str_pad($service->id_service, 7, '0', STR_PAD_LEFT);

        try {
            $service->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erro inesperado ao tentar excluir o serviço '$code': ". $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Serviço '$code' excluído com sucesso!"
        ], 200);
    }

    public function finish(int $id_service)
    {
        $service = Service::where(['id_service' => $id_service])->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => "Serviço não encontrado!"
            ], 404);
        }

        $code = str_pad($service->id_service, 7, '0', STR_PAD_LEFT);

        if ($service->finished_at) {
            return response()->json([
                'success' => false,
                'message' => "Serviço '$code' já está finalizado!"
            ], 409);
        }

        try {
            $price      = $service->price ?? 0;
            $commission = 0;

            if ($price <= 1000) {
                $commission = $price * 0.05;
            } elseif ($price <= 10000) {
                $commission = $price * 0.10;
            } else {
                $commission = $price * 0.20;
            }

            $service->finished_at = new DateTime();
            $service->commission_user = $commission;
            $service->save();

            // Enviar email
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erro inesperado ao tentar finalizar o serviço '$code': ". $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Serviço '$code' finalizado com sucesso!"
        ], 200);
    }
}