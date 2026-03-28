<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ChainController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MineController;
use App\Http\Controllers\BlockController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Blockchain API Endpoints

// Health Check
Route::get('/health', [HealthController::class, 'index']);

// Consultar Blockchain Actual
Route::get('/chain', [ChainController::class, 'index']);

// Nodos: Registrar y Resolver Conflicto (Consenso)
Route::post('/nodes/register', [NodeController::class, 'register']);
Route::get('/nodes/resolve', [NodeController::class, 'resolve']);

// Transacciones
Route::post('/transactions/new', [TransactionController::class, 'store']);
Route::get('/transactions/pending', [TransactionController::class, 'pending']);

// Minería (Proof of Work)
Route::get('/mine', [MineController::class, 'mine']);

// Propagación de Bloques
Route::post('/blocks/receive', [BlockController::class, 'receive']);
