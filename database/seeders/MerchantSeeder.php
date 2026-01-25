<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Merchant::create([
            'type' => 'insurance',
            'category' => 'insurance',
            'name' => 'Takafful',
            'description' => 'Takafful',
            'legal_address' => '',
            'registration_number' => '',
            'website' => 'takafful.tj',
            'tin' => '',
            'emails' => '',
            'phones' => '',
            'mobile_apps' => '',
            'social_media' => '',
            'auth_type' => 'token',
            'auth_value' => 'nbt_kJV3VfvElO9XcKwY0cdZtr8sjOHH6DIyULZlOfLjf6DLqC6tDu0tZVzotJ5Mes6J',
            'base_url' => 'https://polis.takafful.tj/api/',
            'api_key' => 'nbt_kJV3VfvElO9XcKwY0cdZtr8sjOHH6DIyULZlOfLjf6DLqC6tDu0tZVzotJ5Mes6J',
            'sync_type' => 'auto',
            'status' => 'active',
            'last_synced_at' => now(),
        ]);
    }
}
