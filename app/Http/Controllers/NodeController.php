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
        $validated = $request->validate([
            'node_url' => 'required|url'
        ]);

        $url = rtrim($validated['node_url'], '/');

        Nodo::firstOrCreate([
            'url' => $url,
            'activo' => true
        ]);

        return response()->json([
            'message' => 'Node has been added',
            'total_nodes' => Nodo::count()
        ]);
    }

    public function resolve(BlockchainService $service): JsonResponse
    {
        $result = $service->resolveConflicts();

        if ($result['replaced']) {
            return response()->json([
                'message' => 'Our chain was replaced',
                'new_chain' => \App\Models\Grado::orderBy('creado_en', 'asc')->get(),
                'peers_consultados' => $result['peers_consultados']
            ]);
        }

        return response()->json([
            'message' => 'Our chain is authoritative',
            'chain' => \App\Models\Grado::orderBy('creado_en', 'asc')->get(),
            'peers_consultados' => $result['peers_consultados']
        ]);
    }
}
