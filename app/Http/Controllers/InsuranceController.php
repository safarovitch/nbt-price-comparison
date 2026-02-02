<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Organization;

class InsuranceController extends Controller
{
    public function index()
    {
        // Fetch insurance Organization to display in the partners section
        $partners = Organization::where(function ($query) {
            $query->where('type', 'insurance')
                ->orWhere('category', 'insurance');
        })
            ->where('status', 'active')
            ->take(10)
            ->get()
            ->map(function ($organization) {
                return [
                    'uuid' => $organization->uuid,
                    'name' => $organization->name, // Localized string via accessor
                    'logo' => $organization->getFirstMediaUrl('default'), // Resolve media URL
                    'rating' => 5, // Default or fetch real rating
                    'reviews_count' => 0,
                ];
            });

        return Inertia::render('Insurance/Index', [
            'partners' => $partners
        ]);
    }
}
