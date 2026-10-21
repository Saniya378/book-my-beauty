<?php

namespace Database\Seeders;

use App\Models\BeauticianProfile;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Demo bookings for the five seeded studios.
     *
     * Reviews are only attached to confirmed bookings, which mirrors the rule
     * enforced in Customer\ReviewController: a customer may review a beautician
     * only after an appointment with them has been confirmed.
     */
    public function run(): void
    {
        $profiles = BeauticianProfile::whereHas('user', fn ($query) => $query->where('email', 'like', '%@bookmybeauty.com'))
            ->with(['services' => fn ($query) => $query->where('is_active', 1), 'user'])
            ->get();

        if ($profiles->isEmpty()) {
            $this->command?->warn('No seeded beauticians found, run BeauticianSeeder first.');

            return;
        }

        $customers = User::where('role', 'customer')->where('is_active', 1)->orderBy('id')->limit(6)->get();

        if ($customers->isEmpty()) {
            $this->command?->warn('No active customers found.');

            return;
        }

        $plan = [
            ['status' => 'confirmed', 'daysAgo' => 21, 'rating' => 5, 'comment' => 'Looked exactly like the reference photos. Finished without a single touch up.'],
            ['status' => 'confirmed', 'daysAgo' => 14, 'rating' => 4, 'comment' => 'Very professional and friendly. The result was lovely, only slightly behind schedule.'],
            ['status' => 'confirmed', 'daysAgo' => 9, 'rating' => 5, 'comment' => 'Best experience I have had at a salon. Clean studio and great advice.'],
            ['status' => 'cancelled', 'daysAgo' => 12, 'rating' => null, 'comment' => null],
            ['status' => 'pending', 'daysAgo' => 0, 'rating' => null, 'comment' => null],
            ['status' => 'pending', 'daysAgo' => 0, 'rating' => null, 'comment' => null],
        ];

        $slots = ['10:00', '12:30', '15:00', '17:30'];

        foreach ($profiles as $index => $profile) {
            if ($profile->services->isEmpty()) {
                continue;
            }

            foreach (array_slice($plan, 0, 4 + ($index % 3)) as $key => $item) {
                $customer = $customers[($index + $key) % $customers->count()];
                $service = $profile->services[$key % $profile->services->count()];

                $createdAt = Carbon::now()->subDays($item['daysAgo']);

                $booking = Booking::firstOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'service_id' => $service->id,
                        'booking_date' => $createdAt->toDateString(),
                        'booking_time' => $slots[$key % count($slots)],
                    ],
                    ['status' => $item['status']]
                );

                if ($item['rating'] !== null && ! $booking->wasRecentlyCreated) {
                    continue;
                }

                if ($item['rating'] !== null) {
                    Review::firstOrCreate(
                        ['customer_id' => $customer->id, 'beautician_id' => $profile->id],
                        ['rating' => $item['rating'], 'comment' => $item['comment']]
                    );
                }
            }
        }
    }
}