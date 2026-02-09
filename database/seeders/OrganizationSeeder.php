<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::create([
            'type' => 'insurance',
            'category' => 'insurance',
            'name' => [
                'ru' => 'Такаффул',
                'en' => 'Takafful',
                'tj' => 'Такаффул',
            ],
            'description' => [
                'ru' => 'Такаффул - исламское страхование',
                'en' => 'Takafful - Islamic Insurance',
                'tj' => 'Такаффул - суғуртаи исломӣ',
            ],
            'legal_address' => null,
            'registration_number' => '',
            'website' => 'https://takafful.tj',
            'tin' => '',
            'emails' => [],
            'phones' => [],
            'mobile_apps' => [],
            'social_media' => [],
            'auth_type' => 'bearer',
            'auth_value' => 'nbt_kJV3VfvElO9XcKwY0cdZtr8sjOHH6DIyULZlOfLjf6DLqC6tDu0tZVzotJ5Mes6J',
            'base_url' => 'https://polis.takafful.tj/api/',
            'api_key' => 'nbt_kJV3VfvElO9XcKwY0cdZtr8sjOHH6DIyULZlOfLjf6DLqC6tDu0tZVzotJ5Mes6J',
            'endpoints' => [
                'products' => '/nbt/financial-products',
            ],
            'sync_type' => 'auto',
            'status' => 'active',
            'last_synced_at' => now(),
        ]);

        Organization::create([
            'type' => 'mfo',
            'category' => 'mfo',
            'name' => [
                'ru' => 'Шукр Молия',
                'en' => 'Shukr Moliya',
                'tj' => 'Шукри Молия',
            ],
            'description' => [
                'ru' => 'Микро-кредитная организация',
                'en' => 'Microfinance Organization',
                'tj' => 'Ташкилоти микроқарздиҳӣ',
            ],
            'legal_address' => null,
            'registration_number' => '',
            'website' => 'https://shukr.tj',
            'tin' => '',
            'emails' => [],
            'phones' => [],
            'mobile_apps' => [],
            'social_media' => [],
            'auth_type' => 'sha256_signature',
            'auth_value' => 'my-key:my-super-secret', // Format: key:secret
            'base_url' => 'http://shukr.tj/api/v1/NBT',
            'api_key' => null,
            'endpoints' => [
                'credits' => '/credits',
                'transfers' => '/money-transfers',
                'cards' => '/cards/features',
            ],
            'sync_type' => 'auto',
            'status' => 'active',
            'last_synced_at' => null,
        ]);

        Organization::create([
            'type' => 'mfo',
            'category' => 'mfo',
            'name' => [
                'ru' => 'Пайванд',
                'en' => 'Payvand',
                'tj' => 'Пайванд',
            ],
            'description' => [
                'ru' => 'Электронные платежные системы и банковские услуги',
                'en' => 'Electronic Payment Systems and Banking Services',
                'tj' => 'Системаҳои пардохти электронӣ ва хидматҳои бонкӣ',
            ],
            'legal_address' => null,
            'registration_number' => '',
            'website' => 'https://payvand.tj',
            'tin' => '',
            'emails' => [],
            'phones' => [],
            'mobile_apps' => [],
            'social_media' => [],
            'auth_type' => 'api_key_header',
            'auth_value' => '5C31B010-52C0-46D7-8205-AC85CFE3C5CB',
            'base_url' => 'https://api.payvand.tj/staging/payments',
            'api_key' => '5C31B010-52C0-46D7-8205-AC85CFE3C5CB',
            'endpoints' => [
                'services' => '/GetServices',
            ],
            'sync_type' => 'auto',
            'status' => 'active',
            'last_synced_at' => null,
        ]);

        Organization::create([
            'type' => 'mfo',
            'category' => 'mfo',
            'name' => [
                'ru' => 'МДО Оксус',
                'en' => 'MDO OXUS',
                'tj' => 'МДО Оксус',
            ],
            'description' => [
                'ru' => 'Микро-депозитная организация',
                'en' => 'Micro-deposit Organization',
                'tj' => 'Ташкилоти микроамонатгузорӣ',
            ],
            'legal_address' => null,
            'registration_number' => '',
            'website' => 'https://oxus.tj',
            'tin' => '',
            'emails' => [],
            'phones' => [],
            'mobile_apps' => [],
            'social_media' => [],
            'auth_type' => 'api_key',
            'auth_value' => 'OXUS-NBT-TEST-7D21-F9A5-E0B8',
            'base_url' => 'https://n8n-lolcfinance-n8n.ov4co6.easypanel.host/webhook/v1',
            'api_key' => 'OXUS-NBT-TEST-7D21-F9A5-E0B8',
            'endpoints' => [
                'products' => '/products',
            ],
            'sync_type' => 'auto',
            'status' => 'active',
            'last_synced_at' => null,
        ]);
    }
}
