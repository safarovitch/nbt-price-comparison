<?php

namespace App\Http\Controllers\Admin;

use Actuallymab\LaravelComment\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::with(['commented', 'commentable'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Comments/Index', [
            'comments' => $comments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);
        
        $request->validate([
            'approved' => 'required|boolean',
        ]);

        $comment->update([
            'approved' => $request->approved,
        ]);

        return back()->with('success', 'Comment status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }
}
