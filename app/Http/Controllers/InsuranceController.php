<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Merchant;

class InsuranceController extends Controller
{
    public function index()
    {
        // Fetch insurance merchants to display in the partners section
        $partners = Merchant::where(function($query) {
                                $query->where('type', 'insurance')
                                      ->orWhere('category', 'insurance');
                            })
                            ->where('status', 'active')
                            ->take(10)
                            ->get();

        return Inertia::render('Insurance/Index', [
            'partners' => $partners
        ]);
    }
}
