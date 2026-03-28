<?php
namespace App\Http\Controllers;

use App\Models\Nodo;
use App\Services\BlockchainService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NodeController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nodes' => 'required|array',
            'nodes.*' => 'url'
        ]);

        foreach ($request->nodes as $url) {
            Nodo::firstOrCreate([
                'url' => rtrim($url, '/'),
                'activo' => true
            ]);
        }

        return response()->json([
            'message' => 'New nodes have been added',
            'total_nodes' => Nodo::count()
        ]);
    }

    public function resolve(BlockchainService $service): JsonResponse
    {
        $replaced = $service->resolveConflicts();

        if ($replaced) {
            return response()->json([
                'message' => 'Our chain was replaced',
                'new_chain' => \App\Models\Grado::orderBy('creado_en', 'asc')->get()
            ]);
        }

        return response()->json([
            'message' => 'Our chain is authoritative',
            'chain' => \App\Models\Grado::orderBy('creado_en', 'asc')->get()
        ]);
    }
}
