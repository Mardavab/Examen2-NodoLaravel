<?php
namespace App\Http\Controllers;

use App\Models\Grado;
use Illuminate\Http\JsonResponse;

class ChainController extends Controller
{
    public function index(): JsonResponse
    {
        $chain = Grado::orderBy('creado_en', 'asc')->get();
        return response()->json([
            'chain' => $chain,
            'length' => count($chain)
        ]);
    }
}
