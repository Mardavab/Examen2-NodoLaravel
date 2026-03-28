<?php
namespace App\Http\Controllers;

use App\Models\Grado;
use App\Services\BlockchainService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BlockController extends Controller
{
    public function receive(Request $request, BlockchainService $service): JsonResponse
    {
        $request->validate([
            'block' => 'required|array'
        ]);

        $blockData = $request->block;
        
        // Check if block already exists locally
        $exists = Grado::where('hash_actual', $blockData['hash_actual'])->exists();
        
        if (!$exists) {
            $block = new Grado();
            $block->setRawAttributes($blockData);
            
            // Validate the received block
            $recalculated = $service->calculateHash($block, $block->nonce);
            if ($recalculated === $block->hash_actual && $service->isValidHash($block->hash_actual)) {
                try {
                    $block->save();
                    // We DO NOT propagate it again to avoid infinite propagation loops.
                    return response()->json(['message' => 'Block accepted']);
                } catch (\Exception $e) {
                    Log::error("Failed to save received block: " . $e->getMessage());
                    return response()->json(['message' => 'Failed to save block'], 500);
                }
            }
            return response()->json(['message' => 'Block rejected (invalid hash or PoW)'], 400);
        }

        return response()->json(['message' => 'Block already exists'], 200);
    }
}
