<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VneshniePorucheniya extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vneshnie_porucheniya';

    protected $fillable = [
        'vhod_nomer',
        'vhod_data',
        'urr_nomer',
        'urr_data',
        'ishod_nomer',
        'ishod_data',
        'opisanie',
        'sozdal_id',
    ];

    /**
     * Связь с объектами
     */
    public function kadastrovieObekti()
    {
        return $this->hasMany(KadastrovieObekti::class, 'poruchenie_id');
    }

    protected static function booted()
    {
        static::deleting(function ($poruchenie) {
            // При удалении поручения удаляем связанные объекты
            $poruchenie->kadastrovieObekti()->delete();
        });
    }
}
