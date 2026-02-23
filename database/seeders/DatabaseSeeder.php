<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VneshniePorucheniya;
use App\Models\KadastrovieObekti;
use App\Models\VidiRabot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Создаем виды работ (ТОЛЬКО ДВА)
        $this->command->info('Создаю справочник видов работ (Отчет, Заключение)...');
        $vidiRabot = collect(['Отчет', 'Заключение'])
            ->map(function ($name) {
                // Используем firstOrCreate, чтобы не дублировать, если записи уже есть
                return VidiRabot::firstOrCreate(['nazvanie' => $name]);
            });

        // 2. Создаем пользователей
        $this->command->info('Создаю 20 пользователей...');
        $users = User::factory()->count(20)->create();

        User::factory()->create([
            'name' => 'Admin Local',
            'email' => 'admin@example.com',
        ]);

        $targetTotalOrders = 1000;
        $this->command->info("Генерирую {$targetTotalOrders} поручений...");

        for ($i = 0; $i < $targetTotalOrders; $i++) {
            $createdAt = Carbon::now()->subDays(rand(0, 365))->subMinutes(rand(0, 1440));
            $isCompleted = (rand(1, 100) <= 70);

            $order = VneshniePorucheniya::create([
                'sozdal_id'    => $users->random()->id,
                'vhod_nomer'   => 'ВХ-' . rand(100, 999) . '/' . $createdAt->year . '-' . $i,
                'vhod_data'    => $createdAt->toDateString(),
                'urr_nomer'    => rand(10, 99) . '-' . rand(1000, 9999) . '/25',
                'urr_data'     => $createdAt->copy()->subDays(rand(1, 7))->toDateString(),
                'ishod_nomer'  => $isCompleted ? 'ИСХ-' . rand(1000, 9999) . '/' . $createdAt->year : null,
                'ishod_data'   => $isCompleted ? $createdAt->copy()->addDays(rand(5, 15))->toDateString() : null,
                'opisanie'     => 'Тестовое описание поручения №' . $i,
                'created_at'   => $createdAt,
                'updated_at'   => $isCompleted ? $createdAt->copy()->addDays(rand(10, 20)) : $createdAt,
            ]);

            $objectsCount = rand(1, 3);
            for ($j = 0; $j < $objectsCount; $j++) {
                $startDate = $createdAt->copy()->addDays(rand(1, 3));

                KadastrovieObekti::create([
                    'poruchenie_id'          => $order->id,
                    'kadastroviy_nomer'      => rand(10, 99) . ':' . rand(10, 99) . ':' . rand(100000, 999999) . ':' . rand(100, 999),
                    'tip_obekta'             => collect(['Здание', 'Помещение', 'Земельный участок'])->random(),
                    // Теперь здесь рандом только между Отчетом и Заключением
                    'vid_rabot_id'           => $vidiRabot->random()->id,
                    'data_nachala'           => $startDate->toDateString(),
                    'data_zaversheniya'      => $isCompleted ? $startDate->copy()->addDays(rand(3, 10)) : null,
                    'data_okonchaniya_rabot' => $isCompleted ? $startDate->copy()->addDays(rand(3, 10)) : null,
                    'ispolnitel'             => $users->random()->name,
                    'kommentariy'            => 'Тестовый комментарий к объекту',
                    'created_at'             => $createdAt,
                    'updated_at'             => $isCompleted ? $createdAt->copy()->addDays(rand(10, 20)) : $createdAt,
                ]);
            }
        }

        $this->command->info('Готово! База наполнена.');
    }
}
