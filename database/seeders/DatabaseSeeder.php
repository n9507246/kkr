<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ExternalOrder; // Добавили импорт модели
use App\Models\ObektiNedvizhimosti; // Добавили импорт модели
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon; // Обязательно для работы с датами

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Создаем пользователей
        $this->command->info('Создаю 20 пользователей...');
        $users = User::factory()->count(20)->create();

        // Создаем тестового админа, если нужно
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $targetTotalOrders = 1000;
        $this->command->info("Генерирую {$targetTotalOrders} поручений за прошедший год...");

        for ($i = 0; $i < $targetTotalOrders; $i++) {
            // Генерируем дату создания: от 365 дней назад до сегодня
            $createdAt = Carbon::now()->subDays(rand(0, 365))->subMinutes(rand(0, 1440));

            // Определяем, будет ли это поручение "отработанным" (70% вероятность)
            $isCompleted = (rand(1, 100) <= 70);

            // 2. Создаем поручение
            $order = ExternalOrder::factory()->create([
                'created_by'      => $users->random()->id,
                'incoming_date'   => $createdAt->toDateString(),
                'urr_date'        => $createdAt->copy()->subDays(rand(1, 7))->toDateString(),
                'created_at'      => $createdAt,
                'updated_at'      => $isCompleted ? $createdAt->copy()->addDays(rand(10, 20)) : $createdAt,

                'outgoing_number' => $isCompleted ? 'ИСХ-' . rand(1000, 9999) . '/' . $createdAt->year : null,
                'outgoing_date'   => $isCompleted ? $createdAt->copy()->addDays(rand(5, 15))->toDateString() : null,
            ]);

            // 3. Создаем связанные объекты для каждого поручения (от 1 до 3 шт)
            $objectsCount = rand(1, 3);
            for ($j = 0; $j < $objectsCount; $j++) {
                $startDate = $createdAt->copy()->addDays(rand(1, 3));

                ObektiNedvizhimosti::factory()->create([
                    'id_porucheniya_urr'     => $order->id,
                    'data_nachala'           => $startDate->toDateString(),
                    'data_zaversheniya'      => $isCompleted ? $startDate->copy()->addDays(rand(3, 10)) : null,
                    'data_okonchaniya_rabot' => $isCompleted ? $startDate->copy()->addDays(rand(3, 10)) : null,
                    'created_at'             => $createdAt,
                    'updated_at'             => $isCompleted ? $createdAt->copy()->addDays(rand(10, 20)) : $createdAt,
                ]);
            }
        }

        $this->command->info('Готово! База наполнена.');
    }
}
