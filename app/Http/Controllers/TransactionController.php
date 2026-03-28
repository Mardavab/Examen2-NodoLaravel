<?php
namespace App\Http\Controllers;

use App\Models\TransaccionPendiente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function store(Request $request, \App\Services\BlockchainService $service): JsonResponse
    {
        $validated = $request->validate([
            'persona_id' => 'required|uuid',
            'institucion_id' => 'required|uuid',
            'programa_id' => 'required|uuid',
            'titulo_obtenido' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'numero_cedula' => 'required|string',
            'titulo_tesis' => 'nullable|string',
            'menciones' => 'nullable|string',
            'origen_nodo' => 'nullable|string'
        ]);

        $transaccion = TransaccionPendiente::create($validated);

        // Propagate the transaction to peers as per Phase 3 requirement
        // We pass the validated data to avoid recursion if the receiver also propagates (logic should handle it)
        $service->propagateTransaction($validated);

        return response()->json([
            'message' => 'Transaction added and propagated.',
            'transaction' => $transaccion
        ], 201);
    }

    public function pending(): JsonResponse
    {
        $pending = TransaccionPendiente::all();
        return response()->json([
            'transactions' => $pending,
            'length' => count($pending)
        ]);
    }
}
