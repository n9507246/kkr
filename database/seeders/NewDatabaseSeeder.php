<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VneshniePorucheniya;
use App\Models\KadastrovieObekti;
use App\Models\VidiRabot;
use App\Models\TipyObektov;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NewDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {

        User::factory()->create([
            'name' => 'Admin Local',
            'email' => 'admin@example.com',
        ]);

    }
}
