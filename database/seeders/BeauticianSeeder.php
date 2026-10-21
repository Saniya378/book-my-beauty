<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\BeauticianProfile;
use App\Models\PortfolioImage;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BeauticianSeeder extends Seeder
{
    /**
     * Demo beauticians with a complete studio: profile, service list,
     * weekly working hours and portfolio images.
     *
     * Every record is matched on email / user_id first, so running the
     * seeder twice updates the same five beauticians instead of duplicating.
     */
    public function run(): void
    {
        $password = 'beauty@123';

        $studios = [
            [
                'name' => 'Areeba Khan',
                'email' => 'areeba@bookmybeauty.com',
                'phone' => '0300-1234501',
                'location' => 'Gilgit, Sector C',
                'bio' => 'Bridal and occasion makeup artist with six years of experience. Known for soft glam looks that stay flawless through long wedding days.',
                'services' => [
                    ['Bridal Makeup', 8500, 150, 'Full bridal makeup with premium products, lashes and a trial session.'],
                    ['Party Glam', 4500, 90, 'Evening glam with contouring, highlight and false lashes.'],
                    ['Engagement Makeup', 6000, 120, 'Camera-ready engagement look with hair styling included.'],
                    ['Makeup Lesson', 2500, 60, 'One on one makeup lesson for beginners.'],
                ],
                'availability' => ['Monday', 'Tuesday', 'Wednesday', 'Friday', 'Saturday'],
                'hours' => ['10:00', '19:00'],
                'avatar' => 'images/seed/avatars/avatar1.jpg',
            ],
            [
                'name' => 'Maham Siddiqui',
                'email' => 'maham@bookmybeauty.com',
                'phone' => '0300-1234502',
                'location' => 'Gilgit, College Road',
                'bio' => 'Skincare and facial specialist focused on skin health rather than quick fixes. Dermaplaning, chemical peels and clean-up facials.',
                'services' => [
                    ['Deep Cleansing Facial', 3500, 75, 'Steam, extraction and soothing mask for congested skin.'],
                    ['Dermaplaning', 3000, 45, 'Exfoliation and painless facial hair removal with serum.'],
                    ['Anti Ageing Treatment', 5500, 90, 'Firming massage and peptide mask for mature skin.'],
                    ['Hydration Facial', 4000, 70, 'Layered hydrating serums for dry, tired skin.'],
                ],
                'availability' => ['Monday', 'Wednesday', 'Thursday', 'Friday'],
                'hours' => ['11:00', '20:00'],
                'avatar' => 'images/seed/avatars/avatar2.jpg',
            ],
            [
                'name' => 'Zoya Batool',
                'email' => 'zoya@bookmybeauty.com',
                'phone' => '0300-1234503',
                'location' => 'Islamabad, F-10 Markaz',
                'bio' => 'Hair specialist working with colour, cuts and keratin treatments. Every consultation starts with a strand analysis.',
                'services' => [
                    ['Hair Cut and Styling', 2000, 60, 'Consultation, wash, precision cut and blow dry.'],
                    ['Balayage', 12000, 180, 'Hand painted highlights with toner and treatment.'],
                    ['Keratin Treatment', 15000, 210, 'Smoothing treatment that reduces frizz for months.'],
                    ['Hair Spa', 2800, 60, 'Deep conditioning treatment for damaged hair.'],
                ],
                'availability' => ['Tuesday', 'Wednesday', 'Thursday', 'Saturday', 'Sunday'],
                'hours' => ['12:00', '21:00'],
                'avatar' => 'images/seed/avatars/avatar5.jpg',
            ],
            [
                'name' => 'Hina Rehman',
                'email' => 'hina@bookmybeauty.com',
                'phone' => '0300-1234504',
                'location' => 'Lahore, Gulberg III',
                'bio' => 'Nail artist and hand care specialist. Clean studio, sterilised tools and a huge range of gel polies.',
                'services' => [
                    ['Gel Manicure', 2500, 60, 'Shape, cuticle work and long lasting gel colour.'],
                    ['Gel Pedicure', 3000, 75, 'Foot soak, exfoliation and gel colour.'],
                    ['Nail Art', 1200, 45, 'Custom nail art per nail on top of a gel manicure.'],
                    ['Nail Extension', 5000, 120, 'Acrylic or gel extensions with shaping.'],
                ],
                'availability' => ['Monday', 'Tuesday', 'Thursday', 'Friday', 'Saturday'],
                'hours' => ['10:00', '20:00'],
                'avatar' => 'images/seed/avatars/avatar3.jpg',
            ],
            [
                'name' => 'Sana Iqbal',
                'email' => 'sana@bookmybeauty.com',
                'phone' => '0300-1234505',
                'location' => 'Rawalpindi, Bahria Town Phase 7',
                'bio' => 'Unisex barber and grooming specialist. Haircuts, beard styling and head massage in a relaxed setting.',
                'services' => [
                    ['Mens Haircut', 1200, 40, 'Clipper and scissor cut with wash and styling.'],
                    ['Beard Trim', 700, 30, 'Shape up, line up and hot towel finish.'],
                    ['Hair Colour', 3500, 90, 'Full coverage colour with conditioning treatment.'],
                    ['Head Massage', 900, 30, 'Relaxing scalp massage to relieve tension.'],
                ],
                'availability' => ['Monday', 'Tuesday', 'Wednesday', 'Friday', 'Saturday'],
                'hours' => ['11:00', '22:00'],
                'avatar' => 'images/seed/avatars/avatar4.jpg',
            ],
        ];

        foreach ($studios as $studio) {
            $this->createStudio($studio, $password);
        }
    }

    private function createStudio(array $studio, string $password): void
    {
        $user = User::firstOrNew(['email' => $studio['email']]);

        $user->fill([
            'name' => $studio['name'],
            'phone' => $studio['phone'],
            'role' => 'beautician',
            'is_active' => 1,
        ]);

        // only set a password on a brand new account, never reset an existing one
        if (! $user->exists) {
            $user->password = Hash::make($password);
        }

        $user->save();

        $profile = BeauticianProfile::firstOrNew(['user_id' => $user->id]);

        $profile->fill([
            'bio' => $studio['bio'],
            'location' => $studio['location'],
            'phone' => $studio['phone'],
            'profile_pic' => 'uploads/beautician_' . $user->id . '_seed.jpg',
        ]);

        $profile->save();

        $this->copyAvatar($studio['avatar'], $user->id);

        foreach ($studio['services'] as [$name, $price, $duration, $description]) {
            Service::firstOrCreate(
                ['beautician_id' => $profile->id, 'service_name' => $name],
                [
                    'price' => $price,
                    'duration' => $duration,
                    'description' => $description,
                    'is_active' => 1,
                ]
            );
        }

        foreach ($studio['availability'] as $day) {
            Availability::firstOrCreate(
                ['beautician_id' => $profile->id, 'day_of_week' => $day],
                ['start_time' => $studio['hours'][0], 'end_time' => $studio['hours'][1]]
            );
        }

        $this->seedPortfolio($profile, $studio['name']);
    }

    /**
     * Portfolio slots are capped at 12 per beautician in the upload form,
     * so the demo data stays under that limit as well.
     */
    private function seedPortfolio(BeauticianProfile $profile, string $name): void
    {
        $source = public_path('images/portfolio');

        if (! is_dir($source)) {
            return;
        }

        $captions = [
            'Signature look from this month',
            'Client favourite transformation',
            'Soft glam finish',
            'Before styling prep',
            'Detail work close up',
            'Full finished result',
        ];

        foreach (array_slice(scandir($source) ?: [], 2) as $index => $file) {
            if (PortfolioImage::where('beautician_id', $profile->id)->count() >= 12) {
                break;
            }

            if (! preg_match('/\.(jpe?g|png)$/i', $file)) {
                continue;
            }

            $target = 'uploads/portfolio_' . $profile->id . '_' . ($index + 1) . '.jpg';

            copy($source . '/' . $file, public_path($target));

            PortfolioImage::firstOrCreate(
                ['beautician_id' => $profile->id, 'image_path' => $target],
                [
                    'caption' => $captions[$index % count($captions)],
                    'uploaded_at' => now()->subDays($index),
                ]
            );
        }
    }

    private function copyAvatar(string $relativePath, int $userId): void
    {
        $source = public_path($relativePath);

        if (! is_file($source)) {
            return;
        }

        copy($source, public_path('uploads/beautician_' . $userId . '_seed.jpg'));
    }
}