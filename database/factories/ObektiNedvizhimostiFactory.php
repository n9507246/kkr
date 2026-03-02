<?php

namespace Database\Factories;

use App\Models\KadastrovieObekti;
use App\Models\TipyObektov;
use App\Models\VidiRabot;
use App\Models\VneshniePorucheniya;
use Illuminate\Database\Eloquent\Factories\Factory;

class KadastrovieObektiFactory extends Factory
{
    protected $model = KadastrovieObekti::class;

    public function definition(): array
    {
        return [
            'poruchenie_id' => VneshniePorucheniya::factory(),
            'kadastroviy_nomer' => $this->faker->numerify('##:##:#######:####'),
            'tip_obekta_id' => TipyObektov::query()->inRandomOrder()->value('id') ?? 1,
            'vid_rabot_id' => VidiRabot::query()->inRandomOrder()->value('id'),
            'ispolnitel' => $this->faker->name(),
            'kommentariy' => $this->faker->optional()->sentence(),
        ];
    }
}
