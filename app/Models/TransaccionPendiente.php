<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TransaccionPendiente extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transacciones_pendientes';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'persona_id',
        'institucion_id',
        'programa_id',
        'titulo_obtenido',
        'fecha_inicio',
        'fecha_fin',
        'numero_cedula',
        'titulo_tesis',
        'menciones',
        'origen_nodo',
    ];
}
