<?php

namespace App\Http\Controllers\Beautician;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);
        return view('beautician.profile', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:25',
            'profile_pic' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = 'beautician_' . Auth::id() . '_' . time() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('uploads');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $profile->profile_pic = 'uploads/' . $filename;
        }

        $profile->bio = $validated['bio'] ?? '';
        $profile->location = $validated['location'] ?? '';
        $profile->phone = $validated['phone'] ?? '';
        $profile->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
