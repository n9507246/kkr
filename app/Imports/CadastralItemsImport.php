<?php

namespace App\Imports;

use App\Models\CadastralItem;
use Maatwebsite\Excel\Concerns\ToModel;

class CadastralItemsImport implements ToModel
{
 public function model(array $row)
    {

        dd($row);
        // Сначала создаем или находим поручение
        $order = ExternalOrder::firstOrCreate(
            [
                'incoming_number' => $row['incoming_number'],
                'incoming_date' => $row['incoming_date'],
            ],
            [
                'urr_number' => $row['urr_number'],
                'urr_date' => $row['urr_date'],
                'description' => 'Импортировано из Excel',
                'created_by' => auth()->id(),
            ]
        );
        
        // Создаем кадастровый номер
        return new CadastralItem([
            'external_order_id' => $order->id,
            'cadastral_number' => $row['cadastral_number'],
            'object_type' => $row['object_type'],
            'work_type' => $row['work_type'],
            'status' => 'assigned',
            'assigned_to' => $this->findUser($row['assigned_to'] ?? null),
            'comment' => $row['comment'] ?? null,
        ]);
    }
    
    public function rules(): array
    {
        return [
            'incoming_number' => 'required|string',
            'incoming_date' => 'required|date',
            'urr_number' => 'required|string',
            'urr_date' => 'required|date',
            'cadastral_number' => 'required|string',
            'object_type' => 'required|string',
            'work_type' => 'required|string',
        ];
    }
    
    private function findUser($name)
    {
        if (!$name) return null;
        
        $user = \App\Models\User::where('full_name', 'LIKE', "%{$name}%")->first();
        return $user ? $user->id : null;
    }
}
