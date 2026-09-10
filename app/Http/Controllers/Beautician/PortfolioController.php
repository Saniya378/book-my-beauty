<?php

namespace App\Http\Controllers\Beautician;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\PortfolioImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PortfolioController extends Controller
{
    public function index()
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);
        $images = PortfolioImage::where('beautician_id', $profile->id)->orderBy('uploaded_at', 'desc')->get();

        return view('beautician.portfolio', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'portfolio_image' => 'required|image|mimes:jpeg,jpg,png|max:3072',
            'caption' => 'nullable|string|max:150',
        ]);

        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $count = PortfolioImage::where('beautician_id', $profile->id)->count();
        if ($count >= 12) {
            return back()->with('error', 'You have reached the maximum limit of 12 portfolio images. Delete some to add new ones.');
        }

        $file = $request->file('portfolio_image');
        $filename = 'portfolio_' . $profile->id . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

        $destinationPath = public_path('uploads');
        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        $file->move($destinationPath, $filename);

        PortfolioImage::create([
            'beautician_id' => $profile->id,
            'image_path' => 'uploads/' . $filename,
            'caption' => $request->input('caption', ''),
            'uploaded_at' => now(),
        ]);

        return back()->with('success', 'Portfolio image uploaded successfully!');
    }

    public function destroy($id)
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $image = PortfolioImage::where('id', $id)
            ->where('beautician_id', $profile->id)
            ->firstOrFail();

        $filePath = public_path($image->image_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $image->delete();

        return back()->with('success', 'Image removed from portfolio.');
    }
}
