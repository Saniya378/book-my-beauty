<?php

namespace App\Http\Controllers\Beautician;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);
        $services = Service::where('beautician_id', $profile->id)->orderBy('id', 'desc')->get();

        return view('beautician.services', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
        ]);

        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        Service::create([
            'beautician_id' => $profile->id,
            'service_name' => $validated['service_name'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'description' => $validated['description'] ?? '',
            'is_active' => true,
        ]);

        return back()->with('success', 'Service added successfully!');
    }

    public function toggleStatus($id)
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $service = Service::where('id', $id)
            ->where('beautician_id', $profile->id)
            ->firstOrFail();

        $service->is_active = !$service->is_active;
        $service->save();

        $statusStr = $service->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Service {$statusStr} successfully.");
    }

    public function destroy($id)
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $service = Service::where('id', $id)
            ->where('beautician_id', $profile->id)
            ->firstOrFail();

        $service->delete();

        return back()->with('success', 'Service deleted successfully.');
    }
}
