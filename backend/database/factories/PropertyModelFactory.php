<?php

namespace Database\Factories;

use App\Modules\Properties\Enums\PropertyStatusEnum;
use App\Modules\Properties\Enums\PropertyTypeEnum;
use App\Modules\Properties\Models\PropertyModel;
use App\Modules\Users\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyModel>
 */
class PropertyModelFactory extends Factory
{
    protected $model = PropertyModel::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agent_id' => UserModel::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 50000, 5000000),
            'property_type' => fake()->randomElement(PropertyTypeEnum::cases())->value,
            'status' => PropertyStatusEnum::ForSale->value,
            'bedrooms' => fake()->numberBetween(1, 6),
            'bathrooms' => fake()->numberBetween(1, 4),
            'floor_area' => fake()->randomFloat(2, 30, 500),
            'lot_area' => fake()->randomFloat(2, 50, 2000),
            'floors' => fake()->numberBetween(1, 3),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'AU',
            'postcode' => fake()->postcode(),
            'latitude' => fake()->latitude(-44, -10),
            'longitude' => fake()->longitude(113, 154),
        ];
    }
}
