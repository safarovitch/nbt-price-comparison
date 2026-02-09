<?php

namespace App\Http\Controllers;

use App\Models\DeviceLocation;
use Inertia\Inertia;

class DeviceLocationController extends Controller
{
    public function index()
    {
        $locations = DeviceLocation::with(['organization' => function ($query) {
            $query->select('uuid', 'name', 'type');
        }])
            ->orderBy('city')
            ->orderBy('type')
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'type' => $location->type,
                    'city' => $location->city,
                    'address' => $location->address,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'working_hours' => $location->working_hours,
                    'landmark' => $location->landmark,
                    'organization' => $location->organization ? [
                        'uuid' => $location->organization->uuid,
                        'name' => $location->organization->getTranslation('name', app()->getLocale()),
                        'logo' => $location->organization->getFirstMediaUrl('logo', 'preview'),
                    ] : null,
                ];
            });

        $cities = $locations->pluck('city')->filter()->unique()->sort()->values();
        
        $types = $locations->pluck('type')->filter()->unique()->sort()->values();

        return Inertia::render('Atms/Index', [
            'locations' => $locations,
            'cities' => $cities,
            'types' => $types,
        ]);
    }
}
