<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KadastrovieObekti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kadastrovie_obekti';

    protected $fillable = [
        'poruchenie_id',
        'kadastroviy_nomer',
        'tip_obekta',
        'vid_rabot_id',
        'data_nachala',
        'data_zaversheniya',
        'data_okonchaniya_rabot',
        'ispolnitel',
        'kommentariy',
    ];

    protected $casts = [
        'data_nachala' => 'date',
        'data_zaversheniya' => 'date',
        'data_okonchaniya_rabot' => 'date',
    ];

    /**
     * Связь с видом работ (справочник)
     */
    public function vidiRabot()
    {
        return $this->belongsTo(VidiRabot::class, 'vid_rabot_id');
    }

    /**
     * Связь с поручением
     */
    public function poruchenie()
    {
        return $this->belongsTo(VneshniePorucheniya::class, 'poruchenie_id');
    }
}
