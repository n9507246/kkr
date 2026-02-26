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
            
            $data = \App\Models\KadastrovieObekti::query()
                ->with(['poruchenie', 'vidiRabot', 'tipObekta'])
                ->sort( $request->sort ?? [] )
                ->filter( $request->filters ?? [] )
                ->paginate( $request->size ?? 10);
                
            return response()->json([
                'data' => $data->items(),
                'last_page' => $data->lastPage(),
            ]);
        }


        return view('test');
    }
}
