<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class KadastrovieObekti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kadastrovie_obekti';

    protected $fillable = [
        'poruchenie_id',
        'kadastroviy_nomer',
        'tip_obekta_id',
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
     * Связь с типом объекта
     */
    public function tipObekta()
    {
        return $this->belongsTo(TipyObektov::class, 'tip_obekta_id');
    }

    /**
     * Связь с видом работ
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

    /**
     * Применение сортировки к запросу
     */
    public function scopeSort(Builder $query, ?array $sortFields): Builder
    {
        if (empty($sortFields)) {
            return $query->orderBy('kadastrovie_obekti.created_at', 'desc');
        }

        foreach ($sortFields as $sort) {
            $field = $sort['field'] ?? null;
            $direction = $sort['dir'] ?? 'asc';

            if (!$field) {
                continue;
            }

            $this->applySortField($query, $field, $direction);
        }

        return $query;
    }

    /**
     * Применение сортировки по конкретному полю
     */
    private function applySortField(Builder $query, string $field, string $direction): void
    {
        match ($field) {
            // Поля из основной таблицы
            'kadastroviy_nomer',
            'ispolnitel',
            'data_zaversheniya',
            'kommentariy' => $query->orderBy($field, $direction),

            // Поле из связанной таблицы tipObekta
            'tip_obekta.abbreviatura' => $query->orderBy(
                TipyObektov::select('abbreviatura')
                    ->whereColumn('tipy_obektov.id', 'kadastrovie_obekti.tip_obekta_id')
                    ->limit(1),
                $direction
            ),

            // Поле из связанной таблицы vidiRabot
            'vidi_rabot.nazvanie' => $query->orderBy(
                VidiRabot::select('nazvanie')
                    ->whereColumn('vidi_rabot.id', 'kadastrovie_obekti.vid_rabot_id')
                    ->limit(1),
                $direction
            ),

            // Поля из связанной таблицы poruchenie
            'poruchenie.vhod_nomer' => $this->orderByPoruchenieField($query, 'vhod_nomer', $direction),
            'poruchenie.vhod_data' => $this->orderByPoruchenieField($query, 'vhod_data', $direction),
            'poruchenie.urr_nomer' => $this->orderByPoruchenieField($query, 'urr_nomer', $direction),
            'poruchenie.urr_data' => $this->orderByPoruchenieField($query, 'urr_data', $direction),

            default => null,
        };
    }

    /**
     * Сортировка по полю поручения
     */
    private function orderByPoruchenieField(Builder $query, string $field, string $direction): void
    {
        $query->orderBy(
            VneshniePorucheniya::select($field)
                ->whereColumn('vneshnie_porucheniya.id', 'kadastrovie_obekti.poruchenie_id')
                ->limit(1),
            $direction
        );
    }

    /**
     * Применение фильтров к запросу
     * Ожидает структуру: $filters = ['field_name' => 'value', ...]
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // Фильтры по полям основной таблицы
        $query->when($filters['kadastroviy_nomer'] ?? null, fn($q, $value) => 
                $q->where('kadastroviy_nomer', 'like', "%{$value}%"))
                
            ->when($filters['ispolnitel'] ?? null, fn($q, $value) => 
                $q->where('ispolnitel', 'like', "%{$value}%"))
                
            ->when($filters['tip_obekta_id'] ?? null, fn($q, $value) => 
                $q->where('tip_obekta_id', $value))
                
            ->when($filters['vid_rabot_id'] ?? null, fn($q, $value) => 
                $q->where('vid_rabot_id', $value))
                
            ->when($filters['kommentariy'] ?? null, fn($q, $value) => 
                $q->where('kommentariy', 'like', "%{$value}%"));

        // Фильтры по датам
        $query->when($filters['data_nachala'] ?? null, fn($q, $value) => 
                $q->whereDate('data_nachala', $value))
                
            ->when($filters['data_nachala_start'] ?? null, fn($q, $value) => 
                $q->whereDate('data_nachala', '>=', $value))
                
            ->when($filters['data_nachala_end'] ?? null, fn($q, $value) => 
                $q->whereDate('data_nachala', '<=', $value))
                
            ->when($filters['data_zaversheniya'] ?? null, fn($q, $value) => 
                $q->whereDate('data_zaversheniya', $value))
                
            ->when($filters['data_zaversheniya_start'] ?? null, fn($q, $value) => 
                $q->whereDate('data_zaversheniya', '>=', $value))
                
            ->when($filters['data_zaversheniya_end'] ?? null, fn($q, $value) => 
                $q->whereDate('data_zaversheniya', '<=', $value));

        // Фильтры по связанной таблице poruchenie
        $query->when($filters['vhod_nomer'] ?? null, fn($q, $value) => 
                $q->whereHas('poruchenie', fn($subQ) => 
                    $subQ->where('vhod_nomer', 'like', "%{$value}%")))
                    
            ->when($filters['vhod_data'] ?? null, fn($q, $value) => 
                $q->whereHas('poruchenie', fn($subQ) => 
                    $subQ->whereDate('vhod_data', $value)))
                    
            ->when($filters['urr_nomer'] ?? null, fn($q, $value) => 
                $q->whereHas('poruchenie', fn($subQ) => 
                    $subQ->where('urr_nomer', 'like', "%{$value}%")))
                    
            ->when($filters['urr_data'] ?? null, fn($q, $value) => 
                $q->whereHas('poruchenie', fn($subQ) => 
                    $subQ->whereDate('urr_data', $value)));

        return $query;
    }
}