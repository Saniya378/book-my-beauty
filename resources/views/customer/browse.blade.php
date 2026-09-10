@extends('layouts.app')

@section('title', 'Browse Beauticians - Beauty Portal')

@section('styles')
<style>
    .page-header { margin-bottom: 25px; }
    .page-header h2 {
        font-size: 26px;
        color: var(--primary);
        font-weight: 700;
        font-family: 'Playfair Display', serif;
    }
    .page-header p { color: var(--text-gray); font-size: 13px; margin-top: 4px; }

    .search-box {
        background: white;
        padding: 24px;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin-bottom: 30px;
    }
    .search-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        align-items: flex-end;
    }
    .search-group { display: flex; flex-direction: column; }
    .search-group label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .search-group input, .search-group select {
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        outline: none;
    }
    .search-group input:focus, .search-group select:focus { border-color: var(--accent); }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 25px;
    }
    .beautician-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: all 0.3s ease;
        text-align: center;
        position: relative;
        display: flex;
        flex-direction: column;
    }
    .beautician-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-hover);
    }
    .card-photo-area {
        background: linear-gradient(135deg, var(--primary), var(--primary-darker));
        padding: 30px 20px 20px;
        position: relative;
    }
    .card-photo-area img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--accent);
        background: white;
    }
    .fav-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.2);
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        transition: background 0.2s, transform 0.2s;
    }
    .fav-btn:hover {
        transform: scale(1.1);
        background: rgba(255,255,255,0.35);
    }
    .fav-btn.active {
        color: #ef4444;
        background: white;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
    <div class="page-header">
        <h2>Browse Expert Beauticians</h2>
        <p>Explore top professionals, read authentic reviews, check availability, and book online.</p>
    </div>

    <!-- Search / Filter Form -->
    <div class="search-box">
        <form action="{{ route('browse') }}" method="GET">
            <div class="search-grid">
                <div class="search-group" style="grid-column: span 1;">
                    <label for="search">Keywords</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Beautician name...">
                </div>

                <div class="search-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="{{ $location }}" placeholder="e.g. Gilgit, Lahore...">
                </div>

                <div class="search-group">
                    <label for="service">Service</label>
                    <input type="text" id="service" name="service" value="{{ $service }}" placeholder="e.g. Hair, Facial...">
                </div>

                <div class="search-group">
                    <label for="min_price">Min Price (Rs.)</label>
                    <input type="number" id="min_price" name="min_price" value="{{ request('min_price') }}" placeholder="0">
                </div>

                <div class="search-group">
                    <label for="max_price">Max Price (Rs.)</label>
                    <input type="number" id="max_price" name="max_price" value="{{ request('max_price') }}" placeholder="Any">
                </div>

                <div class="search-group">
                    <label for="rating">Min Rating</label>
                    <select id="rating" name="rating">
                        <option value="">Any Rating</option>
                        <option value="5" {{ $minRating == 5 ? 'selected' : '' }}>5 Stars ★★★★★</option>
                        <option value="4" {{ $minRating == 4 ? 'selected' : '' }}>4+ Stars ★★★★☆</option>
                        <option value="3" {{ $minRating == 3 ? 'selected' : '' }}>3+ Stars ★★★☆☆</option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn" style="flex: 1; padding: 10px; font-size: 13px; border-radius: 8px;">Apply Filters</button>
                    <a href="{{ route('browse') }}" class="btn btn-outline" style="padding: 10px 14px; font-size: 13px; border-radius: 8px; text-decoration: none;">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Beauticians Results -->
    @if($beauticians->isEmpty())
        <div style="background: white; padding: 50px; text-align: center; border-radius: var(--border-radius); box-shadow: var(--shadow);">
            <div style="font-size: 40px; margin-bottom: 15px;">🔍</div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--primary); margin-bottom: 8px;">No beauticians found</h3>
            <p style="color: var(--text-gray); font-size: 13px; margin-bottom: 20px;">Try adjusting your filters or search terms to find available professionals.</p>
            <a href="{{ route('browse') }}" class="btn" style="padding: 9px 22px; font-size: 13px;">View All Beauticians</a>
        </div>
    @else
        <div class="cards-grid">
            @foreach($beauticians as $b)
            <div class="beautician-card">
                <div class="card-photo-area">
                    <img src="{{ $b->photo_url }}" alt="{{ $b->user->name }}">

                    @auth
                        @if(Auth::user()->role === 'customer')
                            <button type="button" class="fav-btn {{ in_array($b->id, $favoritedIds) ? 'active' : '' }}" onclick="toggleFavorite(this, {{ $b->id }})" title="Save to Favorites">
                                ♥
                            </button>
                        @endif
                    @endauth
                </div>

                <div style="padding: 22px 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h4 style="font-family: 'Playfair Display', serif; font-size: 19px; color: var(--primary); margin-bottom: 5px;">{{ $b->user->name }}</h4>
                        <p style="font-size: 12px; color: var(--text-gray); margin-bottom: 12px;">📍 {{ $b->location ?: 'Location available upon booking' }}</p>

                        <div style="display: flex; justify-content: center; align-items: center; gap: 6px; margin-bottom: 15px; font-size: 13px;">
                            <span style="color: #f59e0b;">★</span>
                            <strong>{{ number_format($b->reviews_avg_rating ?: 5.0, 1) }}</strong>
                            <span style="color: var(--text-gray); font-size: 12px;">({{ $b->reviews_count }} reviews)</span>
                        </div>

                        <p style="font-size: 12px; color: #666; line-height: 1.5; margin-bottom: 15px; min-height: 36px;">
                            {{ Str::limit($b->bio ?: 'Dedicated beauty professional offering exceptional styling and care.', 70) }}
                        </p>
                    </div>

                    <div style="margin-top: 10px;">
                        <a href="{{ route('beautician.view', $b->id) }}" class="btn" style="width: 100%; padding: 10px; font-size: 13px; text-decoration: none; display: block; border-radius: 6px;">View Profile & Services</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function toggleFavorite(btn, beauticianId) {
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
        if (data.favorited) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    })
    .catch(() => {});
}
</script>
@endsection
