<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::with(['beautician.user'])
            ->where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.favorites', compact('favorites'));
    }

    public function toggle(Request $request)
    {
        $beauticianId = (int) $request->input('beautician_id');

        if ($beauticianId <= 0) {
            return response()->json(['error' => 'Invalid beautician ID.'], 400);
        }

        $existing = Favorite::where('customer_id', Auth::id())
            ->where('beautician_id', $beauticianId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['favorited' => false]);
        } else {
            Favorite::create([
                'customer_id' => Auth::id(),
                'beautician_id' => $beauticianId,
            ]);
            return response()->json(['favorited' => true]);
        }
    }
}
