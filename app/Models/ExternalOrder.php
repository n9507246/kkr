<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Таблица, связанная с моделью
     */
    protected $table = 'external_orders';

    /**
     * Атрибуты, которые можно массово заполнять
     */
    protected $fillable = [
        'incoming_number',
        'incoming_date',
        'urr_number',
        'urr_date',
        'outgoing_number',
        'outgoing_date',
        'description',
        'created_by',
    ];

    /**
     * Получить кадастровые номера поручения
     */
    public function cadastralItems()
    {
        return $this->hasMany(CadastralItem::class, 'external_order_id');
    }

    protected static function booted()
    {
        static::deleting(function ($poruchenie) {
            $poruchenie->obektiNedvizhimosti()->delete();
        });
    }

    public function obektiNedvizhimosti()
    {
        return $this->hasMany(ObektiNedvizhimosti::class, 'id_porucheniya_urr');
    }
}