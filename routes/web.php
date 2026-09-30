<?php
use app\Core\Route;
use app\Controllers\AuthController;
use app\Controllers\RegisterController;

// MIDDLEWARE LOGED
$logedMiddleware = function() {
    if (!auth()->check()) {
        return response()->redirect(route('login')->getFullPath());
    }
};

// MIDDLEWARE GUEST
$guestMiddleware = function() {
    if (auth()->check()) {
        return response()->redirect(route('dashboard')->getFullPath());
    }
};

// APENAS USUÁRIOS LOGADOS
Route::middleware($logedMiddleware)
    ->get('/', 'DashboardController@index')
    ->name('dashboard');

// Service Controller
// Editar serviço
Route::middleware($logedMiddleware)
    ->get('/servicos', 'ServiceController@create')
    ->name('create.service');
Route::middleware($logedMiddleware)
    ->post('/cadastrar-servico', 'ServiceController@store')
    ->name('store.service');
// FIM Editar serviço

// Editar serviço
Route::middleware($logedMiddleware)
    ->get('/servicos/{id_service}/editar', 'ServiceController@edit')
    ->name('edit.service');
Route::middleware($logedMiddleware)
    ->put('/editar-servico/{id_service}', 'ServiceController@update')
    ->name('update.service');
// FIM Editar serviço

Route::middleware($logedMiddleware)
    ->delete('/servicos/{id_service}', 'ServiceController@destroy')
    ->name('destroy.service');
Route::middleware($logedMiddleware)
    ->post('/finalizar-servico/{id_service}', 'ServiceController@finish')
    ->name('finish.service');
// FIM Service Controller

Route::middleware($logedMiddleware)
    ->get('/sair', [AuthController::class, 'logout'])
    ->name('logout');
// FIM APENAS USUÁRIOS LOGADOS

// APENAS USUÁRIOS NÃO LOGADOS
Route::middleware($guestMiddleware)
    ->get('/login', [AuthController::class, 'login'])
    ->name('login');
Route::middleware($guestMiddleware)
    ->get('/cadastro', [RegisterController::class, 'create'])
    ->name('register');
// FIM APENAS USUÁRIOS NÃO LOGADOS

// SEM REGRAS MIDDLEWARE
Route::post('/entrar', [AuthController::class, 'store'])->name('store.login');
Route::post('/cadastro', [RegisterController::class, 'store'])->name('store.register');
// FIM SEM REGRAS MIDDLEWARE