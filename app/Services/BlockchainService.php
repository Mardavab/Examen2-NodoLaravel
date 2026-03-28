<?php

namespace App\Services;

use App\Models\Grado;
use App\Models\Nodo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BlockchainService
{
    private int $difficulty = 4;

    /**
     * Calculate the SHA-256 hash of a block (Grado)
     */
    public function calculateHash(Grado $grado, int $nonce): string
    {
        $data = $grado->persona_id .
                $grado->institucion_id .
                $grado->programa_id .
                $grado->fecha_inicio .
                $grado->fecha_fin .
                $grado->titulo_obtenido .
                $grado->numero_cedula .
                $grado->titulo_tesis .
                $grado->menciones .
                $grado->hash_anterior .
                $nonce;

        return hash('sha256', $data);
    }

    /**
     * Mine a block (Proof of Work)
     */
    public function mineBlock(Grado $grado): void
    {
        $nonce = 0;
        $target = str_repeat('0', $this->difficulty);
        
        // Ensure we have a previous hash
        if (empty($grado->hash_anterior)) {
            $lastBlock = Grado::orderBy('creado_en', 'desc')->first();
            $grado->hash_anterior = $lastBlock ? $lastBlock->hash_actual : str_repeat('0', 64); // Genesis block previous hash
        }

        while (true) {
            $hash = $this->calculateHash($grado, $nonce);
            if (substr($hash, 0, $this->difficulty) === $target) {
                $grado->nonce = $nonce;
                $grado->hash_actual = $hash;
                break;
            }
            $nonce++;
        }
        
        // El Grado se guarda al finalizar de minar correctamente
        $grado->save();
        
        // Propagate the new block to peers
        $this->propagateBlock($grado);
    }

    /**
     * Check if a hash is valid based on the difficulty
     */
    public function isValidHash(string $hash): bool
    {
        return substr($hash, 0, $this->difficulty) === str_repeat('0', $this->difficulty);
    }

    /**
     * Validate the entire chain
     */
    public function isChainValid($chain = null): bool
    {
        $chain = $chain ?? Grado::orderBy('creado_en', 'asc')->get();
        
        for ($i = 1; $i < count($chain); $i++) {
            $currentBlock = $chain[$i];
            $previousBlock = $chain[$i - 1];

            // Verify current hash
            $recalculatedHash = $this->calculateHash($currentBlock, $currentBlock->nonce);
            if ($currentBlock->hash_actual !== $recalculatedHash) {
                return false;
            }

            // Verify PoW
            if (!$this->isValidHash($currentBlock->hash_actual)) {
                return false;
            }

            // Verify previous hash link
            if ($currentBlock->hash_anterior !== $previousBlock->hash_actual) {
                return false;
            }
        }

        return true;
    }

    /**
     * Propagate a newly mined block to all known active nodes
     */
    public function propagateBlock(Grado $grado): array
    {
        $nodes = Nodo::where('activo', true)->get();
        $results = [];

        foreach ($nodes as $node) {
            try {
                $response = Http::timeout(5)->post("{$node->url}/api/blocks/receive", [
                    // Se envía el bloque completo serializado
                    'block' => $grado->toArray()
                ]);
                $results[$node->url] = $response->status();
            } catch (\Exception $e) {
                Log::warning("Failed to propagate block to node: {$node->url}. Error: " . $e->getMessage());
                $results[$node->url] = 'Failed';
            }
        }

        return $results;
    }

    /**
     * Consensus Algorithm: Longest Valid Chain Rule
     */
    public function resolveConflicts(): bool
    {
        $neighbors = Nodo::where('activo', true)->get();
        $newChain = null;
        $maxLength = Grado::count();

        foreach ($neighbors as $node) {
            try {
                $response = Http::timeout(5)->get("{$node->url}/api/chain");

                if ($response->successful()) {
                    $length = $response->json('length');
                    $chainData = $response->json('chain'); // Array of block arrays

                    // Check if chain length is strictly greater
                    if ($length > $maxLength) {
                        // Validate format first to ensure we can build the chain
                        if (is_array($chainData) && $this->isChainValidFromData($chainData)) {
                            $maxLength = $length;
                            $newChain = $chainData;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Failed to fetch chain from node: {$node->url}. Error: " . $e->getMessage());
            }
        }

        if ($newChain) {
            DB::transaction(function () use ($newChain) {
                Schema::disableForeignKeyConstraints();
                Grado::query()->delete(); // clear current blocks
                
                foreach ($newChain as $blockData) {
                    $grado = new Grado();
                    $grado->setRawAttributes($blockData);
                    $grado->save();
                }
                Schema::enableForeignKeyConstraints();
            });
            return true;
        }

        return false;
    }

    /**
     * Build an array of models to validate
     */
    private function isChainValidFromData(array $chainData): bool
    {
        $chain = [];
        foreach ($chainData as $data) {
            $model = new Grado();
            $model->setRawAttributes($data); 
            $chain[] = $model;
        }
        return $this->isChainValid($chain);
    }
}
