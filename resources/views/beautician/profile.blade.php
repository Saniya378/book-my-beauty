@extends('layouts.beautician')

@section('title', 'Edit Studio Profile - Beautician Studio')

@section('content')
<div style="max-width: 750px; margin: 20px auto;">
    <div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
        <div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); padding: 25px 30px; color: white; border-bottom: 3px solid var(--accent);">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">Edit Beautician Profile</h2>
            <p style="font-size: 13px; opacity: 0.85; margin: 0;">Update your public studio details, location, phone number, and avatar photo.</p>
        </div>

        <div style="padding: 30px;">
            <form action="{{ route('beautician.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Current Avatar Preview & Upload -->
                <div style="display: flex; align-items: center; gap: 25px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0;">
                    <img src="{{ $profile->photo_url }}" alt="Profile Photo" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent); background: white;">
                    <div>
                        <label for="profile_pic" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px;">Change Profile Photo</label>
                        <input type="file" id="profile_pic" name="profile_pic" accept="image/png, image/jpeg, image/jpg" style="font-size: 13px;">
                        <div style="font-size: 11px; color: var(--text-gray); margin-top: 4px;">Allowed formats: JPG, PNG. Max 2MB.</div>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="bio" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Professional Bio & Experience</label>
                    <textarea id="bio" name="bio" rows="4" placeholder="Tell clients about your background, beauty certifications, and specialty styling..." style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 13px; outline: none;">{{ old('bio', $profile->bio) }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label for="location" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Studio / City Location</label>
                        <input type="text" id="location" name="location" value="{{ old('location', $profile->location) }}" placeholder="e.g. Gilgit, Sector C" style="width: 100%; padding: 11px 13px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 13px; outline: none;">
                    </div>

                    <div>
                        <label for="phone" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Contact Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}" placeholder="e.g. 0355-1234567" style="width: 100%; padding: 11px 13px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 13px; outline: none;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn" style="padding: 12px 30px; font-size: 14px; font-weight: 600;">Save Profile Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
