<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization)
    {
        return Inertia::render('Admin/Products/Index', [
            'organization' => $organization->load('products'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization)
    {
        return Inertia::render('Admin/Products/Create', [
            'organization' => $organization,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:credit,deposit,insurance,transfer,mortgage,card,islamic,other'],
            'currency_code' => ['required', 'integer'],
            'interest_rate' => ['nullable', 'numeric'],
            'fees' => ['nullable', 'numeric'],
            'term_months' => ['nullable', 'integer'],
            'min_amount' => ['nullable', 'numeric'],
            'max_amount' => ['nullable', 'numeric'],
            'eligibility' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'extra' => ['nullable', 'array'],
        ]);

        $validated['uuid'] = (string) Str::uuid();

        $organization->products()->create($validated);

        return redirect()->route('admin.organizations.show', $organization)
            ->with('success', 'Product added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'organization' => $organization,
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:credit,deposit,insurance,transfer,mortgage,card,islamic,other'],
            'currency_code' => ['required', 'integer'],
            'interest_rate' => ['nullable', 'numeric'],
            'fees' => ['nullable', 'numeric'],
            'term_months' => ['nullable', 'integer'],
            'min_amount' => ['nullable', 'numeric'],
            'max_amount' => ['nullable', 'numeric'],
            'eligibility' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'extra' => ['nullable', 'array'],
        ]);

        $product->update($validated);

        return redirect()->route('admin.organizations.show', $organization)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, Product $product)
    {
        $product->delete();

        return redirect()->route('admin.organizations.show', $organization)
            ->with('success', 'Product deleted successfully.');
    }
}
