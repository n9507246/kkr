<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VidiRabot extends Model
{
    use HasFactory;

    protected $table = 'vidi_rabot';

    protected $fillable = [
        'nazvanie',
        'activno',
    ];
}
