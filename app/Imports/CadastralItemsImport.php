<?php

namespace App\Imports;

use App\Models\CadastralItem;
use Maatwebsite\Excel\Concerns\ToModel;

class CadastralItemsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new CadastralItem([
            //
        ]);
    }
}
