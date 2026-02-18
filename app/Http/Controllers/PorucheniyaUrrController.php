<?php

namespace App\Http\Controllers;


use App\Models\ExternalOrder;
use Illuminate\Http\Request;

class PorucheniyaUrrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $spisok_porucheniy = ExternalOrder::all();
        return view('porucheniya-urr.index', compact('spisok_porucheniy'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('porucheniya-urr.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);

        // $validated = $request->validate([
        //     'incoming_number' => 'required|string|max:50|unique:external_orders',
        //     'incoming_date' => 'required|date',
        //     'urr_number' => 'required|string|max:50',
        //     'urr_date' => 'required|date',
        //     'description' => 'nullable|string',
        //     'outgoing_number' => 'nullable|string|max:50',
        //     'outgoing_date' => 'nullable|date',
        // ]);

        // $validated['created_by'] = auth()->id();

        $order = ExternalOrder::create([
            "incoming_number" => $request['incoming_number'],
            "incoming_date" => $request['incoming_date'],
            "urr_number" => $request['urr_number'],
            "urr_date" => $request['urr_date'],
            "description" => $request['description'],
            "outgoing_number" => null,
            "outgoing_date" => null,
        ]);

        return redirect()
            ->route('porucheniya-urr.edit', $order)
            ->with('success', 'Распоряжение успешно создано');
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {

    //     return view('orders.index', []);

    //     $order = ExternalOrder::with('cadastralItems', 'cadastralItems.executor')
    //         ->withCount('cadastralItems')
    //         ->findOrFail($id);

    //     // Статистика по статусам
    //     $stats = [
    //         'total' => $order->cadastral_items_count,
    //         'completed' => $order->cadastralItems()->where('status', 'completed')->count(),
    //         'in_progress' => $order->cadastralItems()->where('status', 'in_progress')->count(),
    //         'problem' => $order->cadastralItems()->where('status', 'problem')->count(),
    //     ];

    //     return view('orders.show', compact('order', 'stats'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $poruchenie = ExternalOrder::findOrFail($id);
        return view('porucheniya-urr.edit', compact('poruchenie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = ExternalOrder::findOrFail($id);

        $validated = $request->validate([
            'incoming_number' => 'required|string|max:50|unique:external_orders,incoming_number,' . $id,
            'incoming_date' => 'required|date',
            'urr_number' => 'required|string|max:50',
            'urr_date' => 'required|date',
            'description' => 'nullable|string',
            'outgoing_number' => 'nullable|string|max:50',
            'outgoing_date' => 'nullable|date',
        ]);

        $order->update($validated);

        return redirect()
            ->route('porucheniya-urr.show', $order)
            ->with('success', 'Распоряжение успешно обновлено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = ExternalOrder::findOrFail($id);
        $order->delete();

        return redirect()
            ->route('porucheniya-urr.index')
            ->with('success', 'Распоряжение удалено');
    }

    /**
     * Send response for the order.
     */
    public function sendResponse(Request $request, string $id)
    {
        $order = ExternalOrder::findOrFail($id);

        $validated = $request->validate([
            'outgoing_number' => 'required|string|max:50',
            'outgoing_date' => 'required|date',
        ]);

        $order->update($validated);

        return redirect()
            ->route('porucheniya-urr.show', $order)
            ->with('success', 'Ответ зарегистрирован');
    }

    /**
     * Export orders to Excel.
     */
    public function export()
    {
        // Для будущего экспорта
        return redirect()->back()->with('info', 'Экспорт будет доступен позже');
    }
}
