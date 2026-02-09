<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::with(['user', 'topic'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/News/Index', [
            'posts' => $news,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/News/Create', [
            'topics' => \App\Models\Topic::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.ru' => 'required|string|max:255',
            'summary' => 'nullable|array',
            'body' => 'required|array',
            'topic_id' => 'nullable|exists:topics,id',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $title = $validated['title']['en'] ?? $validated['title']['ru'] ?? reset($validated['title']);

        $news = News::create([
            'title' => $validated['title'],
            'slug' => \Illuminate\Support\Str::slug($title),
            'summary' => $validated['summary'],
            'body' => $validated['body'],
            'topic_id' => $validated['topic_id'],
            'user_id' => auth()->id(),
            'published_at' => $validated['published_at'],
        ]);

        if ($request->hasFile('featured_image')) {
            $news->addMediaFromRequest('featured_image')
                ->toMediaCollection('featured_image');
        }

        return redirect()->route('admin.news.index')
            ->with('success', 'News created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        // Append media URL for frontend
        $news->featured_image_url = $news->getFirstMediaUrl('featured_image');

        return Inertia::render('Admin/News/Edit', [
            'post' => [
                'id' => $news->id,
                'title' => $news->getTranslations('title'),
                'slug' => $news->slug,
                'summary' => $news->getTranslations('summary'),
                'body' => $news->getTranslations('body'),
                'featured_image_url' => $news->getFirstMediaUrl('featured_image'),
                'topic_id' => $news->topic_id,
                'published_at' => $news->published_at,
            ],
            'topics' => \App\Models\Topic::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.ru' => 'required|string|max:255',
            'summary' => 'nullable|array',
            'body' => 'required|array',
            'featured_image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'topic_id' => 'nullable|exists:topics,id',
        ]);

        $title = $validated['title']['en'] ?? $validated['title']['ru'] ?? reset($validated['title']);

        $news->update([
            'title' => $validated['title'],
            'slug' => \Illuminate\Support\Str::slug($title),
            'summary' => $validated['summary'],
            'body' => $validated['body'],
            'topic_id' => $validated['topic_id'],
            'published_at' => $validated['published_at'],
        ]);

        if ($request->hasFile('featured_image')) {
            $news->clearMediaCollection('featured_image');
            $news->addMediaFromRequest('featured_image')
                ->toMediaCollection('featured_image');
        }

        return redirect()->route('admin.news.index')
            ->with('success', 'News updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News deleted successfully.');
    }
}
