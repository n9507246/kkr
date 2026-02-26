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
    public function tipObekta() {
        return $this->belongsTo(TipyObektov::class, 'tip_obekta_id');
    }
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


    public function sortVariants(Builder $query, array $sort)
    {
        match ($sort['field']) {

            'kadastroviy_nomer' =>
                $query->orderBy('kadastroviy_nomer', $sort['dir']),

            'ispolnitel' =>
                $query->orderBy('ispolnitel', $sort['dir']),

            'vidi_rabot.nazvanie' =>
                $query->orderBy(
                    \App\Models\VidiRabot::select('nazvanie')
                        ->whereColumn('vidi_rabot.id', 'kadastrovie_obekti.vid_rabot_id')
                        ->limit(1),
                    $sort['dir']
                ),

            default => null,
        };
    }

    public function scopeSort(Builder $query, $sort_fields_list)
    {
        foreach ($sort_fields_list as $sort) {

            $this->sortVariants($query, $sort);                    
        }
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        if (isset($filters['kadastroviy_nomer'])) {
            $query->where('kadastroviy_nomer', 'like', "%{$filters['kadastroviy_nomer']}%");
        }

        if (isset($filters['ispolnitel'])) {
            $query->where('ispolnitel', 'like', "%{$filters['ispolnitel']}%");
        }

        if (isset($filters['vid_rabot.nazvanie'])) {
            $query->whereHas('vidiRabot', function ($q) use ($filters) {
                $q->where('nazvanie', 'like', "%{$filters['vid_rabot.nazvanie']}%");
            });
        }
    }

}


