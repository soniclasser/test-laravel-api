<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'folio',
        'emisor',
        'receptor',
        'moneda',
        'total',
        'tipo_cambio',
    ];
}

