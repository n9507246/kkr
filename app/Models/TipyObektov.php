<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipyObektov extends Model
{
    use HasFactory;

    // Указываем таблицу явно, так как Laravel по умолчанию будет искать "tipy_obektovs"
    protected $table = 'tipy_obektov';

    protected $fillable = [
        'abbreviatura',
        'nazvanie',
        'activno',
    ];
}
