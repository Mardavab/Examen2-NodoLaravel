<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Grado extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'grados';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'persona_id',
        'institucion_id',
        'programa_id',
        'fecha_inicio',
        'fecha_fin',
        'titulo_obtenido',
        'numero_cedula',
        'titulo_tesis',
        'menciones',
        'hash_actual',
        'hash_anterior',
        'nonce',
        'firmado_por',
    ];
}
