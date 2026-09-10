<?php
use app\Core\Route;
use app\Controllers\AuthController;
use app\Controllers\RegisterController;

Route::get('/', 'DashboardController@index')->name('dashboard');
Route::get('/servicos', 'ServiceController@index')->name('services');
Route::get('/funcionarios', 'EmployeeController@index')->name('employees');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/entrar', [AuthController::class, 'store'])->name('store.login');
Route::get('/sair', [AuthController::class, 'logout'])->name('logout');

Route::get('/cadastro', [RegisterController::class, 'create'])->name('register');
Route::post('/cadastro', [RegisterController::class, 'store'])->name('store.register');