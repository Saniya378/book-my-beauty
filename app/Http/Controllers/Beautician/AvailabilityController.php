<?php

namespace App\Http\Controllers\Beautician;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\BeauticianProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function index()
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $availabilitySlots = Availability::where('beautician_id', $profile->id)
            ->get()
            ->sortBy(function ($item) use ($daysOfWeek) {
                return array_search($item->day_of_week, $daysOfWeek);
            });

        return view('beautician.availability', compact('availabilitySlots', 'daysOfWeek'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|array|min:1',
            'days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $added = 0;
        $skipped = [];

        foreach ($validated['days'] as $day) {
            $existing = Availability::where('beautician_id', $profile->id)
                ->where('day_of_week', $day)
                ->first();

            if ($existing) {
                $skipped[] = $day;
            } else {
                Availability::create([
                    'beautician_id' => $profile->id,
                    'day_of_week' => $day,
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                ]);
                $added++;
            }
        }

        $message = "";
        if ($added > 0) {
            $message .= "{$added} day(s) added successfully! ";
        }
        if (!empty($skipped)) {
            $message .= "Slots already exist for: " . implode(', ', $skipped) . ". Delete them first to update hours.";
        }

        return back()->with('success', $message);
    }

    public function destroy($id)
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $slot = Availability::where('id', $id)
            ->where('beautician_id', $profile->id)
            ->firstOrFail();

        $slot->delete();

        return back()->with('success', 'Availability slot removed.');
    }
}
