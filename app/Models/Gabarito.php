<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gabarito extends Model
{
    use HasFactory;

    protected $table = 'gabarito';
    protected $guarded = [
        'id',
    ];
}
