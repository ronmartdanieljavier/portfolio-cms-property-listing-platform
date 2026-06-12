<?php

namespace Database\Factories;

use App\Modules\Properties\Models\AmenityModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AmenityModel>
 */
class AmenityModelFactory extends Factory
{
    protected $model = AmenityModel::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }
}
