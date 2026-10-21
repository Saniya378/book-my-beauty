@extends('layouts.beautician')

@section('title', 'Edit Studio Profile - Beautician Studio')

@section('content')
    <div style="max-width: 760px; margin: 0 auto;">
        <div class="page-head">
            <div>
                <h1 class="page-head__title">Edit profile</h1>
                <p class="page-head__sub">This is what customers see on your public profile.</p>
            </div>
        </div>

        <section class="panel">
            <div class="panel__body">
                <form action="{{ route('beautician.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="profile-photo-row">
                        <img src="{{ $profile->photo_url }}" alt="Profile photo"
                            style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent);">

                        <div class="field" style="margin: 0;">
                            <label for="profile_pic">Change profile photo</label>
                            <input type="file" id="profile_pic" name="profile_pic" accept="image/png, image/jpeg, image/jpg">
                            <span class="rows__meta">JPG or PNG, up to 2MB.</span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="field">
                        <label for="bio">Bio and experience</label>
                        <textarea id="bio" name="bio" rows="4"
                            placeholder="Tell clients about your background, certifications and specialities...">{{ old('bio', $profile->bio) }}</textarea>
                    </div>

                    <div class="grid grid--2" style="gap: 14px;">
                        <div class="field">
                            <label for="location">Studio or city</label>
                            <input type="text" id="location" name="location" value="{{ old('location', $profile->location) }}"
                                placeholder="e.g. Gilgit, Sector C">
                        </div>

                        <div class="field">
                            <label for="phone">Contact number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}"
                                placeholder="e.g. 0355-1234567">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                        <button type="submit" class="btn">Save changes</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection