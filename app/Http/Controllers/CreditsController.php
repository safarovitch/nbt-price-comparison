<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreditsController extends Controller
{
    /**
     * Display credits comparison page.
     */
    public function index(Request $request): Response
    {
        $query = Product::with('organization')
            ->where('type', 'credit')
            ->whereHas('organization', fn($q) => $q->where('status', 'active'));

        // Filter by amount
        if ($request->filled('amount')) {
            $amount = (float) $request->input('amount');
            $query->where(function ($q) use ($amount) {
                $q->where('min_amount', '<=', $amount)
                    ->where(function ($q2) use ($amount) {
                        $q2->where('max_amount', '>=', $amount)
                            ->orWhereNull('max_amount');
                    });
            });
        }

        // Filter by term (months)
        if ($request->filled('term')) {
            $term = (int) $request->input('term');
            $query->where(function ($q) use ($term) {
                $q->where('term_months_min', '<=', $term)
                    ->where(function ($q2) use ($term) {
                        $q2->where('term_months_max', '>=', $term)
                            ->orWhereNull('term_months_max');
                    });
            });
        }

        // Filter by purpose
        if ($request->filled('purpose')) {
            $query->where('purpose', $request->input('purpose'));
        }

        // Filter by organization
        if ($request->filled('organization')) {
            $query->where('organization_uuid', $request->input('organization'));
        }

        // Sorting
        $sort = $request->input('sort', 'rate_asc');
        switch ($sort) {
            case 'rate_asc':
                $query->orderBy('interest_rate_min', 'asc');
                break;
            case 'rate_desc':
                $query->orderBy('interest_rate_max', 'desc');
                break;
            case 'amount_asc':
                $query->orderBy('max_amount', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('max_amount', 'desc');
                break;
            case 'term_asc':
                $query->orderBy('term_months_max', 'asc');
                break;
            case 'term_desc':
                $query->orderBy('term_months_max', 'desc');
                break;
            default:
                $query->orderBy('interest_rate_min', 'asc');
        }

        $products = $query->paginate(12)->withQueryString();

        // Get organizations for filter
        $organizations = Organization::where('status', 'active')
            ->whereHas('products', fn($q) => $q->where('type', 'credit'))
            ->get(['uuid', 'name']);

        // Get unique purposes for filter
        $purposes = Product::where('type', 'credit')
            ->whereNotNull('purpose')
            ->distinct()
            ->pluck('purpose');

        // Available terms for filter
        $termOptions = [6, 12, 24, 36, 48, 60];

        return Inertia::render('Credits/Index', [
            'products' => $products,
            'organizations' => $organizations,
            'purposes' => $purposes,
            'termOptions' => $termOptions,
            'filters' => [
                'amount' => $request->input('amount'),
                'term' => $request->input('term'),
                'purpose' => $request->input('purpose'),
                'organization' => $request->input('organization'),
                'sort' => $sort,
            ],
        ]);
    }

    /**
     * Display a single credit product.
     */
    public function show(Product $product): Response
    {
        // Ensure it's a credit product
        if ($product->type !== 'credit') {
            abort(404);
        }

        // Load organization
        $product->load('organization');

        // Get similar products from same organization
        $similarProducts = Product::with('organization')
            ->where('type', 'credit')
            ->where('organization_uuid', $product->organization_uuid)
            ->where('uuid', '!=', $product->uuid)
            ->limit(3)
            ->get();

        // Get competing products from other organizations
        $competingProducts = Product::with('organization')
            ->where('type', 'credit')
            ->where('organization_uuid', '!=', $product->organization_uuid)
            ->whereHas('organization', fn($q) => $q->where('status', 'active'))
            ->orderBy('interest_rate_min', 'asc')
            ->limit(4)
            ->get();

        return Inertia::render('Credits/Show', [
            'product' => $product,
            'similarProducts' => $similarProducts,
            'competingProducts' => $competingProducts,
        ]);
    }
}
