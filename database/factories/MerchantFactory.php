<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Merchant>
 */
class MerchantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => 'other',
            'base_url' => fake()->url() . '/v1',
            'auth_type' => 'api_key',
            'auth_value' => fake()->uuid(),
            'webhook_secret' => null,
            'is_active' => true,
        ];
    }
}
