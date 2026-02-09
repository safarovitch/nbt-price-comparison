<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Get credit products with lowest rates, grouped by organization
        $creditProducts = Product::where('type', 'credit')
            ->whereNotNull('interest_rate_min')
            ->with(['organization' => function ($query) {
                $query->select('uuid', 'name', 'type');
            }])
            ->orderBy('interest_rate_min', 'asc')
            ->limit(10)
            ->get()
            ->map(fn($product) => [
                'uuid' => $product->uuid,
                'name' => $product->name,
                'rate' => $product->interest_rate_min,
                'rateMax' => $product->interest_rate_max,
                'termMin' => $product->term_months_min,
                'termMax' => $product->term_months_max,
                'organization' => $product->organization ? [
                    'uuid' => $product->organization->uuid,
                    'name' => $product->organization->name,
                    'logo' => $product->organization->getFirstMediaUrl('logo', 'preview') ?: null,
                ] : null,
            ]);

        // Get deposit products with highest rates
        $depositProducts = Product::where('type', 'deposit')
            ->whereNotNull('interest_rate_min')
            ->with(['organization' => function ($query) {
                $query->select('uuid', 'name', 'type');
            }])
            ->orderBy('interest_rate_max', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($product) => [
                'uuid' => $product->uuid,
                'name' => $product->name,
                'rate' => $product->interest_rate_max ?? $product->interest_rate_min,
                'rateMin' => $product->interest_rate_min,
                'termMin' => $product->term_months_min,
                'termMax' => $product->term_months_max,
                'organization' => $product->organization ? [
                    'uuid' => $product->organization->uuid,
                    'name' => $product->organization->name,
                    'logo' => $product->organization->getFirstMediaUrl('logo', 'preview') ?: null,
                ] : null,
            ]);

        // Get latest comments
        $latestComments = \Actuallymab\LaravelComment\Models\Comment::where('approved', true)
            ->with(['commentable', 'commented'])
            ->latest()
            ->limit(6)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'rate' => $comment->rate,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user_name' => $comment->commented ? $comment->commented->name : 'Anonymous',
                    'organization' => $comment->commentable ? [
                        'name' => $comment->commentable->name,
                        'logo' => method_exists($comment->commentable, 'getFirstMediaUrl') ? $comment->commentable->getFirstMediaUrl('logo', 'preview') : null,
                    ] : null,
                ];
            });

        $latestNews = \App\Models\News::published()
            ->with(['media', 'topic'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($newsItem) {
                return [
                    'id' => $newsItem->id,
                    'title' => $newsItem->title,
                    'slug' => $newsItem->slug,
                    'short_description' => $newsItem->summary,
                    'posted_at' => $newsItem->published_at,
                    'image_url' => $newsItem->getFirstMediaUrl('featured_image'),
                    'categories' => $newsItem->topic ? [['name' => $newsItem->topic->name]] : [],
                ];
            });

        return Inertia::render('Index', [
            'marketMonitoring' => [
                'credits' => $creditProducts,
                'deposits' => $depositProducts,
            ],
            'latestComments' => $latestComments,
            'latestNews' => $latestNews,
        ]);
    }
}
