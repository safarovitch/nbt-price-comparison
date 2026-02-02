<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class CalculatorController extends Controller
{
    public function loan()
    {
        return Inertia::render('Calculators/LoanCalculator');
    }

    public function credit()
    {
        return Inertia::render('Calculators/CreditCalculator');
    }

    public function deposit()
    {
        return Inertia::render('Calculators/DepositCalculator');
    }
}
