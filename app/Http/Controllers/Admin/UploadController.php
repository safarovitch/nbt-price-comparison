<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function image(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // 2MB Max
        ]);

        // We attach the uploaded image to the current user as a way to store it
        // In a more complex app, we might have a dedicated 'Media' model or clean up orphans.
        $media = $request->user()
            ->addMediaFromRequest('image')
            ->toMediaCollection('content-images');

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => $media->getUrl(),
            ],
        ]);
    }
}
