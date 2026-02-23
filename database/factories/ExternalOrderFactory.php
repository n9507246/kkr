<?php
namespace Database\Factories;

use App\Models\VneshniePorucheniya;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VneshniePorucheniyaFactory extends Factory
{
    protected $model = VneshniePorucheniya::class;

    public function definition(): array
    {
        $year = $this->faker->year();
        return [
            'incoming_number' => 'BX-' . $this->faker->unique()->numberBetween(1000, 999999) . '/' . $year,
            'incoming_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'urr_number' => $this->faker->numberBetween(10, 99) . '-' . $this->faker->numberBetween(1000, 9000) . '/25',
            'urr_date' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
            'outgoing_number' => 'OUT-' . $this->faker->numberBetween(1000, 5000),
            'outgoing_date' => $this->faker->optional(0.7)->dateTimeBetween('now', '+1 month'),
            'description' => $this->faker->sentence(10),
            'created_by' => User::first()?->id ?? User::factory(),
        ];
    }
}
