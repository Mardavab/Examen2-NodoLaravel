<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Institucion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'instituciones';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'nombre',
        'pais',
        'estado',
    ];
}
