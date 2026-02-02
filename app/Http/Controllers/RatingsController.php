<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class RatingsController extends Controller
{
    protected $otpService;

    public function __construct(\App\Services\OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function index(Request $request)
    {
        $query = \Actuallymab\LaravelComment\Models\Comment::query()
            ->where('commentable_type', \App\Models\Organization::class)
            // Eager load the organization (commentable)
            ->with(['commentable'])
            ->whereNotNull('rate'); // It is 'rate' in the model property docblock

        // Filter by Organization Type
        if ($request->filled('type')) {
            $type = $request->input('type');

            $query->whereHasMorph('commentable', [\App\Models\Organization::class], function ($q) use ($type) {
                if ($type === 'bank') {
                    $q->whereIn('type', ['bank', 'credit']);
                } elseif ($type === 'insurance') {
                    $q->where('type', 'insurance');
                } else {
                    $q->where('type', $type);
                }
            });
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'rating_high':
                $query->orderByDesc('rate'); // 'rate' property
                break;
            case 'rating_low':
                $query->orderBy('rate'); // 'rate' property
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $comments = $query->paginate(12)->withQueryString();

        $comments->getCollection()->transform(function ($comment) {
            return [
                'id' => $comment->id,
                'rating' => $comment->rate, // 'rate' property
                'content' => $comment->comment, // 'comment' property
                'created_at' => $comment->created_at,
                'organization' => $comment->commentable ? [
                    'name' => $comment->commentable->name,
                    'logo' => $comment->commentable->getFirstMediaUrl('logo', 'preview') ?: null,
                    'type' => $comment->commentable->type,
                ] : null,
                'user_name' => 'User',
            ];
        });

        return Inertia::render('Ratings/Index', [
            'comments' => $comments,
            'filters' => $request->only(['type', 'sort']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_uuid' => 'required|exists:organizations,uuid',
            'phone' => 'required|string',
            'phone_verified' => 'required|boolean|accepted',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $organization = \App\Models\Organization::where('uuid', $request->organization_uuid)->firstOrFail();

        $user = \App\Models\User::firstOrCreate(
            ['phone' => $request->phone],
            ['name' => 'Guest ' . substr($request->phone, -4)]
        );

        $organization->comment($request->comment, $request->rating, $user);

        return back()->with('success', 'Comment submitted successfully!');
    }

    public function searchOrganizations(Request $request)
    {
        $query = $request->input('query');
        $organizations = \App\Models\Organization::query()
            ->where('status', 'active')
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($org) {
                return [
                    'uuid' => $org->uuid,
                    'name' => $org->name,
                    'logo' => $org->getFirstMediaUrl('logo', 'preview') ?: null,
                ];
            });

        return response()->json($organizations);
    }
}
