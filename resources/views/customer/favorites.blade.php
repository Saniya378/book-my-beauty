@extends('layouts.customer')

@section('title', 'My Favorites - Beauty Portal')

@section('styles')
<style>
    .cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 25px; }
    .beautician-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        text-align: center;
        transition: transform 0.3s;
        position: relative;
    }
    .beautician-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-hover); }
    .card-photo-area {
        background: linear-gradient(135deg, var(--primary), var(--primary-darker));
        padding: 30px 20px 20px;
        position: relative;
    }
    .card-photo-area img {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--accent);
        background: white;
    }
</style>
@endsection

@section('content')
<div style="margin-bottom: 25px;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; color: var(--primary); margin-bottom: 4px;">My Favorite Beauticians</h2>
    <p style="color: var(--text-gray); font-size: 13px; margin: 0;">Quick access to the beauticians and stylists you love most.</p>
</div>

@if($favorites->isEmpty())
    <div style="background: white; padding: 50px 20px; text-align: center; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <div style="font-size: 40px; margin-bottom: 15px; color: #ef4444;">♥</div>
        <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--primary); margin-bottom: 8px;">No favorites saved yet</h3>
        <p style="color: var(--text-gray); font-size: 13px; margin-bottom: 20px;">Browse beauticians and tap the heart icon on any profile to save them here for instant booking!</p>
        <a href="{{ route('browse') }}" class="btn" style="padding: 10px 24px; font-size: 13px;">Browse Beauticians</a>
    </div>
@else
    <div class="cards">
        @foreach($favorites as $fav)
        @if($fav->beautician)
        <div class="beautician-card" id="card-{{ $fav->beautician_id }}">
            <div class="card-photo-area">
                <img src="{{ $fav->beautician->photo_url }}" alt="{{ $fav->beautician->user->name ?? 'Beautician' }}">
                <button type="button" onclick="removeFavorite({{ $fav->beautician_id }})" title="Remove from favorites" style="
                    position: absolute; top: 12px; right: 12px; background: white; border: none; border-radius: 50%;
                    width: 32px; height: 32px; cursor: pointer; color: #ef4444; font-size: 16px; display: flex;
                    align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    ♥
                </button>
            </div>
            <div style="padding: 20px;">
                <h4 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin-bottom: 4px;">{{ $fav->beautician->user->name ?? 'Beautician' }}</h4>
                <p style="font-size: 12px; color: var(--text-gray); margin-bottom: 12px;">📍 {{ $fav->beautician->location ?: 'Location confirmed on booking' }}</p>

                <div style="display: flex; justify-content: center; align-items: center; gap: 5px; margin-bottom: 18px; font-size: 13px;">
                    <span style="color: #f59e0b;">★</span>
                    <strong>{{ number_format($fav->beautician->reviews()->avg('rating') ?: 5.0, 1) }}</strong>
                    <span style="color: var(--text-gray); font-size: 12px;">({{ $fav->beautician->reviews()->count() }} reviews)</span>
                </div>

                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('beautician.view', $fav->beautician_id) }}" class="btn btn-outline" style="flex: 1; padding: 8px; font-size: 12px; text-decoration: none;">Profile</a>
                    <a href="{{ route('customer.book', $fav->beautician_id) }}" class="btn" style="flex: 1; padding: 8px; font-size: 12px; text-decoration: none;">Book Now</a>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
@endif
@endsection

@section('scripts')
<script>
function removeFavorite(beauticianId) {
    if (!confirm('Remove this beautician from your favorites?')) return;

    fetch("{{ route('customer.favorites.toggle') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ beautician_id: beauticianId })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.favorited) {
            const card = document.getElementById('card-' + beauticianId);
            if (card) card.remove();
        }
    })
    .catch(() => {});
}
</script>
@endsection
