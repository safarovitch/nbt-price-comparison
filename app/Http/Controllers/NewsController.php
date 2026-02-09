<?php

namespace App\Http\Controllers;

use App\Models\News;
use Inertia\Inertia;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::published()
            ->with(['topic', 'user', 'media'])
            ->orderByDesc('published_at')
            ->paginate(9);

        // Transform for frontend
        $news->getCollection()->transform(function ($item) {
            $item->featured_image = $item->getFirstMediaUrl('featured_image');
            return $item;
        });

        return Inertia::render('News/Index', [
            'posts' => $news, // Keeping 'posts' key for now to avoid breaking Vue props instantly, or should rename? Vue expects 'posts'. Let's keep key 'posts' but change variable.
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $post = News::published()
            ->with(['topic', 'user', 'media'])
            ->where('slug', $slug)
            ->firstOrFail();

        $post->featured_image = $post->getFirstMediaUrl('featured_image');

        $relatedPosts = News::published()
            ->with('media')
            ->where('id', '!=', $post->id)
            ->limit(3)
            ->get()
            ->map(function ($item) {
                $item->featured_image = $item->getFirstMediaUrl('featured_image');
                return $item;
            });

        return Inertia::render('News/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
