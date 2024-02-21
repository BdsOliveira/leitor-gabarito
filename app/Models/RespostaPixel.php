<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespostaPixel extends Model
{
    use HasFactory;

    protected $table = 'resposta_pixels';
    protected $guarded = [
        'id',
    ];
}
