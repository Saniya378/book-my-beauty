@extends('layouts.app')

@section('title', $beautician->user->name . ' - BookMyBeauty')

@section('styles')
    <style>
        .profile-hero {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            padding: 40px;
            border-radius: var(--border-radius);
            margin-bottom: 35px;
            display: flex;
            align-items: center;
            gap: 30px;
            color: white;
            position: relative;
            flex-wrap: wrap;
            border-bottom: 3px solid var(--accent);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--accent);
            background: white;
        }

        .hero-info {
            flex: 1;
            min-width: 260px;
        }

        .hero-info h2 {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            margin-bottom: 6px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .service-card {
            background: white;
            padding: 22px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s;
        }

        .service-card:hover {
            transform: translateY(-4px);
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 40px;
        }

        .portfolio-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            height: 180px;
            box-shadow: var(--shadow);
            cursor: pointer;
        }

        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .portfolio-item:hover img {
            transform: scale(1.05);
        }

        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .review-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border-left: 4px solid var(--accent);
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 1100px; margin: 30px auto; padding: 0 20px;">
        @include('components.alert')

        <!-- Hero Header -->
        <div class="profile-hero">
            <img src="{{ $beautician->photo_url }}" alt="{{ $beautician->user->name }}" class="profile-avatar">

            <div class="hero-info">
                <h2>{{ $beautician->user->name }}</h2>
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">
                    <span>📍 {{ $beautician->location ?: 'Location available upon booking' }}</span>
                    @if (!empty($beautician->phone))
                        <span style="margin-left: 15px;">📞 {{ $beautician->phone }}</span>
                    @endif
                </div>

                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; margin-bottom: 12px;">
                    <span style="color: var(--accent); font-size: 16px;">★</span>
                    <strong>{{ number_format($beautician->reviews_avg_rating ?: 5.0, 1) }}</strong>
                    <span style="opacity: 0.75;">({{ $beautician->reviews_count }} verified reviews)</span>
                </div>

                <p style="font-size: 13px; opacity: 0.85; line-height: 1.6; max-width: 600px; margin: 0;">
                    {{ $beautician->bio ?: 'Experienced beauty artist offering specialized salon treatments and personalized care.' }}
                </p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px; align-self: center;">
                <a href="{{ route('customer.book', $beautician->id) }}" class="btn"
                    style="padding: 12px 28px; font-size: 14px; font-weight: 600; text-align: center;">Book Appointment</a>

                @auth
                    @if (Auth::user()->role === 'customer')
                        <button type="button" onclick="toggleProfileFavorite(this, {{ $beautician->id }})"
                            class="btn btn-outline"
                            style="padding: 10px 20px; font-size: 13px; color: white; border-color: white; background: transparent;">
                            <span id="fav-text">{{ $isFavorited ? '♥ Favorited' : '♡ Save to Favorites' }}</span>
                        </button>
                        <a href="{{ route('customer.review', $beautician->id) }}"
                            style="color: var(--accent); font-size: 12px; text-align: center; text-decoration: none;">Write a
                            Review</a>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Services Offered -->
        <div class="section-title">
            <span>Available Services ({{ $beautician->activeServices->count() }})</span>
            <a href="{{ route('customer.book', $beautician->id) }}" class="btn"
                style="padding: 7px 18px; font-size: 12px;">Book Service &rarr;</a>
        </div>

        <div class="services-grid">
            @forelse($beautician->activeServices as $s)
                <div class="service-card">
                    <div>
                        <div
                            style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px;">
                            <h4 style="font-size: 17px; color: var(--primary); font-weight: 600;">{{ $s->service_name }}
                            </h4>
                            <span style="font-size: 16px; font-weight: 700; color: #2e7d32;">Rs.
                                {{ number_format($s->price, 0) }}</span>
                        </div>
                        <div style="font-size: 12px; color: var(--text-gray); margin-bottom: 10px;">
                            ⏱ Duration: {{ $s->duration }} minutes
                        </div>
                        <p style="font-size: 13px; color: #555; line-height: 1.5; margin-bottom: 15px;">
                            {{ $s->description ?: 'Standard professional salon care.' }}
                        </p>
                    </div>
                    <a href="{{ route('customer.book', $beautician->id) }}?service_id={{ $s->id }}"
                        class="btn btn-outline"
                        style="width: 100%; text-align: center; padding: 8px; font-size: 13px; text-decoration: none;">Book
                        This</a>
                </div>
            @empty
                <div
                    style="grid-column: 1 / -1; background: white; padding: 30px; text-align: center; border-radius: 8px; color: #888;">
                    No services currently listed.
                </div>
            @endforelse
        </div>

        <!-- Portfolio Gallery -->
        @if ($beautician->portfolioImages->isNotEmpty())
            <div class="section-title">
                <span>Work Portfolio & Recent Styles</span>
            </div>
            <div class="portfolio-grid">
                @foreach ($beautician->portfolioImages as $img)
                    <div class="portfolio-item" onclick="window.open('{{ $img->url }}', '_blank')">
                        <img src="{{ $img->url }}" alt="{{ $img->caption }}">
                        @if (!empty($img->caption))
                            <div
                                style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.6); color: white; padding: 6px 10px; font-size: 11px;">
                                {{ $img->caption }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Reviews Section -->
        <div class="section-title">
            <span>Client Reviews & Ratings</span>
            @auth
                @if (Auth::user()->role === 'customer')
                    <a href="{{ route('customer.review', $beautician->id) }}" class="btn btn-outline"
                        style="padding: 7px 18px; font-size: 12px; text-decoration: none;">Write Review</a>
                @endif
            @endauth
        </div>

        <div class="reviews-list">
            @forelse($beautician->reviews as $r)
                <div class="review-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <strong>{{ $r->customer->name ?? 'Verified Client' }}</strong>
                        <div style="color: #f59e0b; font-size: 14px;">
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= $r->rating ? '★' : '☆' }}
                            @endfor
                        </div>
                    </div>
                    <p style="font-size: 13px; color: #444; line-height: 1.5; margin-bottom: 6px;">{{ $r->comment }}</p>
                    <span style="font-size: 11px; color: var(--text-gray);">Reviewed on
                        {{ $r->created_at ? $r->created_at->format('d M Y') : '' }}</span>
                </div>
            @empty
                <div style="background: white; padding: 30px; text-align: center; border-radius: 8px; color: #888;">
                    No reviews yet. Be the first to book an appointment and leave feedback!
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleProfileFavorite(btn, beauticianId) {
            fetch("{{ route('customer.favorites.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        beautician_id: beauticianId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    const text = document.getElementById('fav-text');
                    if (data.favorited) {
                        text.innerText = '♥ Favorited';
                    } else {
                        text.innerText = '♡ Save to Favorites';
                    }
                })
                .catch(() => {});
        }
    </script>
@endsection
