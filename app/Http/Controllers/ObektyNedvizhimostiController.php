<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ObektyNedvizhimostiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vse-obekty-nedvizhimosti');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function forma_obekta_privyazkoy_k_porucheniyu(string $id_poruchenie)
    {
        // dd($poruchenie_urr_id);
        return view('porucheniya-urr.sozdat-obekt', compact('id_poruchenie'));
    }
}
