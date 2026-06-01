<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $request->name,
            'bio' => $request->bio,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateBanner(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'banner' => 'required|string', // Base64 cropped image
        ]);

        try {
            $image = $request->banner;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'banners/' . $user->id . '_' . time() . '.png';

            // Delete old banner if exists
            if ($user->profile_banner) {
                Storage::disk('public')->delete($user->profile_banner);
            }

            Storage::disk('public')->put($imageName, base64_decode($image));

            $user->update([
                'profile_banner' => $imageName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Banner updated successfully!',
                'url' => asset('storage/' . $imageName)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update banner: ' . $e->getMessage()
            ], 500);
        }
    }
}
