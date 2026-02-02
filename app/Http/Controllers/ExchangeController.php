<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ExchangeController extends Controller
{
    public function index()
    {
        return Inertia::render('Exchange/Index');
    }
}
