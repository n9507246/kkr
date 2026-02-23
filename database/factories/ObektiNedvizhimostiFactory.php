<?php

namespace Database\Factories;

use App\Models\KadastrovieObekti;
use App\Models\VneshniePorucheniya;
use Illuminate\Database\Eloquent\Factories\Factory;

class KadastrovieObektiFactory extends Factory
{
    protected $model = KadastrovieObekti::class;

    public function definition(): array
    {
        $types = ['Земельный участок', 'Здание', 'Помещение', 'Сооружение'];
        $works = ['Отчет', 'Заключение', 'Технический план'];

        return [
            'id_porucheniya_urr' => VneshniePorucheniya::factory(),
            'kadastroviy_nomer' => $this->faker->numerify('##:##:#######:####'),
            'tip_obekta_nedvizhimosti' => $this->faker->randomElement($types),
            'vid_rabot' => $this->faker->randomElement($works),
            'ispolnitel' => $this->faker->name(),
            'komentarii' => $this->faker->optional()->sentence(),
        ];
    }
}
