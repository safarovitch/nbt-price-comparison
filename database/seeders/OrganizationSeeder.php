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
    // 1. Takafful (Islamic Insurance)
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
      'auth_type' => 'api_key',
      'auth_value' => 'nbt_kJV3VfvElO9XcKwY0cdZtr8sjOHH6DIyULZlOfLjf6DLqC6tDu0tZVzotJ5Mes6J',
      'base_url' => 'https://polis.takafful.tj/api',
      'api_key' => 'nbt_kJV3VfvElO9XcKwY0cdZtr8sjOHH6DIyULZlOfLjf6DLqC6tDu0tZVzotJ5Mes6J',
      'endpoints' => [
        'products' => '/nbt/financial-products',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => now(),
    ]);

    // 2. Shukr Moliya (Microfinance)
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

    // 3. Payvand (Electronic Payment Systems)
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

    // 4. MDO OXUS (Micro-deposit Organization)
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

    // 5. MADO MATIN (Microfinance)
    Organization::create([
      'type' => 'mfo',
      'category' => 'mfo',
      'name' => [
        'ru' => 'МАДО Матин',
        'en' => 'MADO Matin',
        'tj' => 'МАДО Матин',
      ],
      'description' => [
        'ru' => 'Микрофинансовая организация',
        'en' => 'Microfinance Organization',
        'tj' => 'Ташкилоти микромолиявӣ',
      ],
      'legal_address' => null,
      'registration_number' => '',
      'website' => 'https://mtpay.tj',
      'tin' => '',
      'emails' => [],
      'phones' => [],
      'mobile_apps' => [],
      'social_media' => [],
      'auth_type' => 'api_key',
      'auth_value' => 'becc881d67839bb4b4d71c039fc8ff16808d9c2cb817b9781c662389257dbc3c',
      'base_url' => 'https://mtpay.tj:4460/api',
      'api_key' => 'becc881d67839bb4b4d71c039fc8ff16808d9c2cb817b9781c662389257dbc3c',
      'endpoints' => [
        'credits' => '/credits/',
        'deposits' => '/deposits/',
        'plastic_cards' => '/plastic-cards/',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => null,
    ]);

    // 6. Emin (Microfinance)
    Organization::create([
      'type' => 'mfo',
      'category' => 'mfo',
      'name' => [
        'ru' => 'Эмин',
        'en' => 'Emin',
        'tj' => 'Эмин',
      ],
      'description' => [
        'ru' => 'Микрофинансовая организация',
        'en' => 'Microfinance Organization',
        'tj' => 'Ташкилоти микромолиявӣ',
      ],
      'legal_address' => null,
      'registration_number' => '',
      'website' => 'https://emin.tj',
      'tin' => '',
      'emails' => [],
      'phones' => [],
      'mobile_apps' => [],
      'social_media' => [],
      'auth_type' => 'bearer',
      'auth_value' => '',
      'base_url' => 'https://api-dev.emin.tj',
      'api_key' => null,
      'endpoints' => [
        'credits' => '/credits-products',
        'deposits' => '/deposit-products',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => null,
    ]);

    // 7. Alif (Fintech)
    Organization::create([
      'type' => 'mfo',
      'category' => 'mfo',
      'name' => [
        'ru' => 'Алиф',
        'en' => 'Alif',
        'tj' => 'Алиф',
      ],
      'description' => [
        'ru' => 'Финансовая технологическая компания',
        'en' => 'Financial Technology Company',
        'tj' => 'Ширкати технологияи молиявӣ',
      ],
      'legal_address' => null,
      'registration_number' => '',
      'website' => 'https://alif.tj',
      'tin' => '',
      'emails' => [],
      'phones' => [],
      'mobile_apps' => [],
      'social_media' => [],
      'auth_type' => 'api_key',
      'auth_value' => '',
      'base_url' => 'https://products4nbt-api.alif.tj/v1',
      'api_key' => '',
      'endpoints' => [
        'products' => '/products',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => null,
    ]);

    // 8. Amonatbonk (Bank)
    Organization::create([
      'type' => 'bank',
      'category' => 'bank',
      'name' => [
        'ru' => 'Амонатбонк',
        'en' => 'Amonatbonk',
        'tj' => 'Амонатбонк',
      ],
      'description' => [
        'ru' => 'Государственный сберегательный банк',
        'en' => 'State Savings Bank',
        'tj' => 'Бонки давлатии амонатгузорӣ',
      ],
      'legal_address' => null,
      'registration_number' => '',
      'website' => 'https://amonatbonk.tj',
      'tin' => '',
      'emails' => [],
      'phones' => [],
      'mobile_apps' => [],
      'social_media' => [],
      'auth_type' => 'api_key',
      'auth_value' => 'a8d1d7e0289e17a6d5dda22a2ea7482c96c421d109ab4e3183a55e68a4922764',
      'base_url' => 'https://products.amonatbonk.tj/api/v1',
      'api_key' => 'a8d1d7e0289e17a6d5dda22a2ea7482c96c421d109ab4e3183a55e68a4922764',
      'endpoints' => [
        'products' => '/products/all/',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => null,
    ]);

    // 9. Сугуртаи Аввалини Миллӣ (SAM - Insurance)
    Organization::create([
      'type' => 'insurance',
      'category' => 'insurance',
      'name' => [
        'ru' => 'Сугуртаи Аввалини Миллӣ',
        'en' => 'SAM Insurance',
        'tj' => 'Суғуртаи Аввалини Миллӣ',
      ],
      'description' => [
        'ru' => 'Национальная страховая компания',
        'en' => 'National Insurance Company',
        'tj' => 'Ширкати суғуртаи миллӣ',
      ],
      'legal_address' => null,
      'registration_number' => '',
      'website' => '',
      'tin' => '',
      'emails' => [],
      'phones' => [],
      'mobile_apps' => [],
      'social_media' => [],
      'auth_type' => 'api_key',
      'auth_value' => '1849499a-76ea-40e6-a727-b594dab232de',
      'base_url' => 'https://api-sam01-sugurta.4rd07a.easypanel.host/webhook/v1',
      'api_key' => '1849499a-76ea-40e6-a727-b594dab232de',
      'endpoints' => [
        'products' => '/products',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => null,
    ]);

    // 10. Arvand (Bank)
    Organization::create([
      'type' => 'bank',
      'category' => 'bank',
      'name' => [
        'ru' => 'Арванд',
        'en' => 'Arvand',
        'tj' => 'Арванд',
      ],
      'description' => [
        'ru' => 'Коммерческий банк',
        'en' => 'Commercial Bank',
        'tj' => 'Бонки тиҷоратӣ',
      ],
      'legal_address' => null,
      'registration_number' => '',
      'website' => '',
      'tin' => '',
      'emails' => [],
      'phones' => [],
      'mobile_apps' => [],
      'social_media' => [],
      'auth_type' => 'basic',
      'auth_value' => 'NBT_Abad:BSekwLSxBh6k3Pp', // Format: username:password
      'base_url' => 'https://192.168.100.127/OnlineBank.Webapi/api',
      'api_key' => null,
      'endpoints' => [
        'products' => '/Deposits/Products/AvailableProducts',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => null,
    ]);

    // 11. Tavhidbonk (Bank)
    Organization::create([
      'type' => 'bank',
      'category' => 'bank',
      'name' => [
        'ru' => 'Тавхидбонк',
        'en' => 'Tavhidbonk',
        'tj' => 'Тавҳидбонк',
      ],
      'description' => [
        'ru' => 'Коммерческий банк',
        'en' => 'Commercial Bank',
        'tj' => 'Бонки тиҷоратӣ',
      ],
      'legal_address' => null,
      'registration_number' => '',
      'website' => 'https://tawhid.tj',
      'tin' => '',
      'emails' => [],
      'phones' => [],
      'mobile_apps' => [],
      'social_media' => [],
      'auth_type' => 'sha256_timestamp',
      'auth_value' => 'Taw20@hid!26', // Secret for SHA256(secret + timestamp)
      'base_url' => 'https://pay.tawhid.tj:4436/banksite/nbt',
      'api_key' => null,
      'endpoints' => [
        'products' => '/products',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => null,
    ]);

    // 12. Sanoatsodirotbonk (Bank)
    Organization::create([
      'type' => 'bank',
      'category' => 'bank',
      'name' => [
        'ru' => 'Саноатсодиротбонк',
        'en' => 'Sanoatsodirotbonk',
        'tj' => 'Саноатсодиротбонк',
      ],
      'description' => [
        'ru' => 'Банк содействия промышленности и экспорту',
        'en' => 'Industry and Export Promotion Bank',
        'tj' => 'Бонки мусоидати саноат ва содирот',
      ],
      'legal_address' => null,
      'registration_number' => '',
      'website' => '',
      'tin' => '',
      'emails' => [],
      'phones' => [],
      'mobile_apps' => [],
      'social_media' => [],
      'auth_type' => 'none',
      'auth_value' => '',
      'base_url' => 'http://109.74.66.233:8082/front/test/info',
      'api_key' => null,
      'endpoints' => [
        'products' => '/products',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => null,
    ]);

    // 13. ICB (International Commercial Bank)
    Organization::create([
      'type' => 'bank',
      'category' => 'bank',
      'name' => [
        'ru' => 'Международный коммерческий банк',
        'en' => 'ICB',
        'tj' => 'Бонки тиҷоратии байналмилалӣ',
      ],
      'description' => [
        'ru' => 'Международный коммерческий банк Таджикистана',
        'en' => 'International Commercial Bank of Tajikistan',
        'tj' => 'Бонки тиҷоратии байналмилалии Тоҷикистон',
      ],
      'legal_address' => null,
      'registration_number' => '',
      'website' => 'https://icb.tj',
      'tin' => '',
      'emails' => [],
      'phones' => [],
      'mobile_apps' => [],
      'social_media' => [],
      'auth_type' => 'bearer',
      'auth_value' => '3|3ZYEjaus3qwiIZbhX9RZ7SNkt3Pde6l2NI5xbYCq6a0d6746',
      'base_url' => 'https://admin.icb.tj/api/icb',
      'api_key' => null,
      'endpoints' => [
        'products' => '/products',
      ],
      'sync_type' => 'auto',
      'status' => 'active',
      'last_synced_at' => null,
    ]);
  }
}
