<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        

        if ($request->ajax()) {
            
            $query = \App\Models\KadastrovieObekti::query()
                ->with(['poruchenie', 'vidiRabot', 'tipObekta']);

            if ($request->filled('sort') && is_array($request->sort)){
                $sort_fields_list = $request->sort;
                $query->sort($sort_fields_list);
            }
                
                

            $perPage = $request->size ?? 10;

            // проверяем установлено ли поле sort 
            // if ($request->filled('sort') && is_array($request->sort)) {
                
            //     // запускаем цикл на случай если полей несколько
            //     foreach ($request->sort as $sort) {

            //         match ($sort['field']) {

            //             'kadastroviy_nomer' =>
            //                 $query->orderBy('kadastroviy_nomer', $sort['dir']),

            //             'ispolnitel' =>
            //                 $query->orderBy('ispolnitel', $sort['dir']),

            //             'vidi_rabot.nazvanie' =>
            //                 $query->orderBy(
            //                     \App\Models\VidiRabot::select('nazvanie')
            //                         ->whereColumn('vidi_rabot.id', 'kadastrovie_obekti.vid_rabot_id')
            //                         ->limit(1),
            //                     $sort['dir']
            //                 ),

            //             default => null,
            //         };
                                    
            //     }

            // }
            $data = $query->paginate($perPage);
            return response()->json([
                'data' => $data->items(),
                'last_page' => $data->lastPage(),
            ]);
        }
        // dump($request);
        return view('test');
    }
}
