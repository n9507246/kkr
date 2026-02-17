<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportController extends Controller
{
    
    public function showForm()
    {
        // СОЗДАЕМ ФЕЙКОВЫЙ МАССИВ с ID
        $order = [
            'id' => 1,                       // добавили ID
            'incoming_number' => 'ВХ-123/2025',
            'incoming_date' => '17.02.2025'
        ];
        
        // Превращаем массив в объект
        $order = (object) $order;
        
        return view('import.form', compact('order'));
    }
}