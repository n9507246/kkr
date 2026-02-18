<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CadastralItem extends Model
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
        'external_order_id',
        'cadastral_number',
        'object_type',
        'work_type',
        'report_date',
        'status',
        'assigned_to',
        'comment',
        'start_date',
        'completion_date',
    ];

    /**
     * Преобразование типов
     */
    protected $casts = [
        'report_date' => 'date',
        'start_date' => 'date',
        'completion_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

   

    /**
     * Связь с поручением
     */
    public function externalOrder()
    {
        return $this->belongsTo(ExternalOrder::class, 'external_order_id');
    }

    
}