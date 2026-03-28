<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelGrado extends Model
{
    use HasFactory;

    protected $table = 'niveles_grado';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
