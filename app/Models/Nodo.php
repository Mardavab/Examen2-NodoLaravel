<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nodo extends Model
{
    use HasFactory;

    protected $table = 'nodos';
    const CREATED_AT = 'registrado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'url',
    ];
}
