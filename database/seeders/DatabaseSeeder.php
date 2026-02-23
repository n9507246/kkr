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

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Справочник Видов Работ
        $this->command->info('Создаю справочник vidi_rabot...');
        $vidiRabot = collect([
            ['nazvanie' => 'Отчет'],
            ['nazvanie' => 'Заключение']
        ])->map(function ($item) {
            return VidiRabot::firstOrCreate($item);
        });

        // 2. Справочник Типов Объектов (ОСТАВЛЯЕМ ТОЛЬКО ЗУ и ОКС)
        $this->command->info('Создаю справочник tipy_obektov (ЗУ и ОКС)...');
        $tipyObektov = collect([
            ['abbreviatura' => 'ЗУ', 'nazvanie' => 'Земельный участок'],
            ['abbreviatura' => 'ОКС', 'nazvanie' => 'Объект капитального строительства'],
        ])->map(function ($item) {
            return TipyObektov::firstOrCreate($item);
        });

        // 3. Пользователи
        $this->command->info('Создаю пользователей...');
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

                    // Теперь будет выбирать только между ОКС и ЗУ
                    'tip_obekta_id'          => $tipyObektov->random()->id,

                    'vid_rabot_id'           => $vidiRabot->random()->id,
                    'data_nachala'           => $startDate->toDateString(),
                    'data_zaversheniya'      => $isCompleted ? $startDate->copy()->addDays(rand(3, 10)) : null,
                    'data_okonchaniya_rabot' => $isCompleted ? $startDate->copy()->addDays(rand(3, 10)) : null,
                    'ispolnitel'             => $users->random()->name,
                    'kommentariy'            => 'Тестовый комментарий к объекту недвижимости',
                    'created_at'             => $createdAt,
                    'updated_at'             => $isCompleted ? $createdAt->copy()->addDays(rand(10, 20)) : $createdAt,
                ]);
            }
        }

        $this->command->info('Сидинг завершен успешно!');
    }
}
