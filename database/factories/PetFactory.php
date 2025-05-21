<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->text(10),
            'city' => $this->faker->randomElement(['台北市', '基隆市']),
            'town' => $this->faker->randomElement(['中正區', '信義區']),
            'is_stray' => $this->faker->boolean(),
            'type' => $this->faker->randomElement(['狗狗', '貓咪']),
            'color' => $this->faker->randomElement(['狗狗', '貓咪']),
            'fur_type' => $this->faker->randomElement(['短毛', '長毛']),
            'name' => $this->faker->firstName('黑色', '白色'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'age' => $this->faker->randomElement(['0-1', '2-5', '6-10', '11-15', '16+']),
            'is_neuter' => $this->faker->boolean()
        ];
    }
}
