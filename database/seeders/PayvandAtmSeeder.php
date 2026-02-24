<?php

namespace Database\Seeders;

use App\Models\DeviceLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PayvandAtmSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $jsonPath = storage_path('app/public/payvand-atms.json');

    if (!File::exists($jsonPath)) {
      $this->command->error("File not found at: $jsonPath");
      return;
    }

    $json = File::get($jsonPath);
    $data = json_decode($json, true);

    if (!$data || !isset($data['cities'])) {
      $this->command->error("Invalid JSON data or missing 'cities' key.");
      return;
    }

    $organization = \App\Models\Organization::where('name->en', 'Payvand')->first();

    if (!$organization) {
      $this->command->error("Organization 'Payvand' not found. Ensure OrganizationSeeder has run.");
      return;
    }

    $organizationUuid = $organization->uuid;

    // Clear existing locations for this organization to avoid duplicates
    DeviceLocation::where('organization_uuid', $organizationUuid)->delete();

    $count = 0;
    foreach ($data['cities'] as $cityData) {
      $cityName = $cityData['city'];
      foreach ($cityData['details'] as $item) {
        DeviceLocation::create([
          'organization_uuid' => $organizationUuid,
          'type' => 'atm',
          'city' => $cityName,
          'address' => $item['address'],
          'latitude' => $item['xCoordinate'] ?? null,
          'longitude' => $item['yCoordinate'] ?? null,
          'working_hours' => $item['workingTime'] ?? null,
          'meta_data' => [
            'original_id' => $item['id'],
            'anchor_link' => $item['anchorLink'] ?? null,
          ],
        ]);
        $count++;
      }
    }

    $this->command->info("Imported $count ATM locations for Payvand.");
  }
}
