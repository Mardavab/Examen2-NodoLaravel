<?php
namespace App\Http\Controllers;

use App\Models\Grado;
use App\Models\TransaccionPendiente;
use App\Services\BlockchainService;
use Illuminate\Http\JsonResponse;

class MineController extends Controller
{
    public function mine(BlockchainService $service): JsonResponse
    {
        // Get the first pending transaction to process
        $transaccion = TransaccionPendiente::orderBy('creado_en', 'asc')->first();

        if (!$transaccion) {
            return response()->json([
                'message' => 'No pending transactions to mine'
            ], 400);
        }

        // Create a new block (Grado) from the transaction data
        $grado = new Grado();
        $grado->persona_id = $transaccion->persona_id;
        $grado->institucion_id = $transaccion->institucion_id;
        $grado->programa_id = $transaccion->programa_id;
        $grado->titulo_obtenido = $transaccion->titulo_obtenido;
        $grado->fecha_inicio = $transaccion->fecha_inicio;
        $grado->fecha_fin = $transaccion->fecha_fin;
        $grado->numero_cedula = $transaccion->numero_cedula;
        $grado->titulo_tesis = $transaccion->titulo_tesis;
        $grado->menciones = $transaccion->menciones;
        
        // Mine the block
        $service->mineBlock($grado);

        // Remove the transaction from pending once fully mined
        $transaccion->delete();

        return response()->json([
            'message' => 'New block forged and propagated',
            'block' => $grado
        ]);
    }
}
