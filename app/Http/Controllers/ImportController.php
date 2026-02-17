<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\CadastralImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function showForm()
    {
        
        return view('import.form');
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);
        
        try {
            // Импортируем файл
            Excel::import(new \App\Imports\CadastralItemsImport, $request->file('file'));
            
            return redirect()->back()->with('success', 'Импорт выполнен успешно!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка импорта: ' . $e->getMessage());
        }
    }
    
    public function downloadTemplate()
    {
        // Пока заглушка
        return "Здесь будет скачивание шаблона";
    }
}