<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsMediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:8192',
        ]);

        $file = $request->file('image');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::uuid() . '.' . $extension;
        $directory = public_path('uploads/cms');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file->move($directory, $filename);

        // Relative URL so images work on any host/port (localhost:3303, etc.)
        $url = '/uploads/cms/' . $filename;

        return response()->json([
            'url' => $url,
            'path' => 'uploads/cms/' . $filename,
        ]);
    }
}
