<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // five complete demo studios with services, hours and portfolios
        $this->call(BeauticianSeeder::class);

        // demo bookings, and reviews only for the confirmed ones
        $this->call(BookingSeeder::class);
    }
}