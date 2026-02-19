<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObektiNedvizhimosti extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Таблица, связанная с моделью
     */
    protected $table = 'cadastral_items';

    /**
     * Атрибуты, которые можно массово заполнять
     */
    protected $fillable = [
        'id_porucheniya_urr',
        'kadastroviy_nomer',
        'tip_obekta_nedvizhimosti',
        'vid_rabot',
        'data_okonchaniya_rabot',
        'ispolnitel',
        'komentarii',
        'data_nachala',
        'data_zaversheniya',
    ];

    /**
     * Преобразование типов
     */
    protected $casts = [
        'data_okonchaniya_rabot' => 'date',
        'data_nachala' => 'date',
        'data_zaversheniya' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Связь с поручением
     */
    public function poruchenie()
    {
        return $this->belongsTo(ExternalOrder::class, 'id_porucheniya_urr');
    }
    
    /**
     * Альтернативное название для обратной совместимости
     */
    public function externalOrder()
    {
        return $this->poruchenie();
    }
}