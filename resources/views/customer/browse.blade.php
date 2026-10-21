@extends('layouts.app')

@section('title', 'Browse Beauticians - BookMyBeauty')

@section('content')
    <div class="portal">
        <div class="page-head">
            <div>
                <h1 class="page-head__title">Browse beauticians</h1>
                <p class="page-head__sub">Compare artists, read real reviews and check live availability.</p>
            </div>
        </div>

        <form action="{{ route('browse') }}" method="GET" class="filters" style="align-items: flex-end;">
            <div class="field" style="flex: 1 1 200px; margin: 0;">
                <label for="search">Keywords</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Beautician name">
            </div>

            <div class="field" style="flex: 1 1 180px; margin: 0;">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="{{ $location }}" placeholder="Gilgit, Lahore">
            </div>

            <div class="field" style="flex: 1 1 180px; margin: 0;">
                <label for="service">Service</label>
                <input type="text" id="service" name="service" value="{{ $service }}" placeholder="Hair, facial">
            </div>

            <div class="field" style="flex: 0 1 130px; margin: 0;">
                <label for="min_price">Min price</label>
                <input type="number" id="min_price" name="min_price" value="{{ request('min_price') }}"
                    placeholder="0">
            </div>

            <div class="field" style="flex: 0 1 130px; margin: 0;">
                <label for="max_price">Max price</label>
                <input type="number" id="max_price" name="max_price" value="{{ request('max_price') }}"
                    placeholder="Any">
            </div>

            <div class="field" style="flex: 0 1 170px; margin: 0;">
                <label for="rating">Minimum rating</label>
                <select id="rating" name="rating">
                    <option value="">Any rating</option>
                    <option value="5" {{ $minRating == 5 ? 'selected' : '' }}>5 stars</option>
                    <option value="4" {{ $minRating == 4 ? 'selected' : '' }}>4 stars and above</option>
                    <option value="3" {{ $minRating == 3 ? 'selected' : '' }}>3 stars and above</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px; flex: 0 0 auto;">
                <button type="submit" class="btn">
                    <x-icon name="search" :size="15" />
                    Apply
                </button>
                <a href="{{ route('browse') }}" class="btn btn--ghost">Reset</a>
            </div>
        </form>

        @if ($beauticians->isEmpty())
            <section class="panel">
                <div class="empty" style="padding: 55px 20px;">
                    <span class="icon-tile" style="margin: 0 auto 16px;"><x-icon name="search" :size="20" /></span>
                    <strong>No beauticians match those filters</strong>
                    <p style="margin: 6px 0 20px;">Try a different city, service or a wider price range.</p>
                    <a href="{{ route('browse') }}" class="btn btn--accent">Clear all filters</a>
                </div>
            </section>
        @else
            <div class="grid grid--3">
                @foreach ($beauticians as $beautician)
                    <article class="card" style="padding: 0; overflow: hidden; text-align: center; position: relative;">

                        <div style="display: flex; justify-content: center; padding: 28px 20px; background: linear-gradient(135deg, var(--primary), var(--primary-darker));">
                            <img src="{{ $beautician->photo_url }}" alt="{{ $beautician->user->name }}"
                                style="width: 92px; height: 92px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent);">
                        </div>

                        <div style="padding: 20px;">
                            <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary);">
                                {{ $beautician->user->name }}
                            </h3>

                            <p class="meta" style="justify-content: center; margin-top: 6px;">
                                <x-icon name="map-pin" :size="13" />
                                {{ $beautician->location ?: 'Location shared on booking' }}
                            </p>

                            <p class="meta" style="justify-content: center; margin-top: 10px;">
                                <span class="rating">&#9733;</span>
                                <strong style="color: var(--primary);">
                                    {{ number_format($beautician->reviews_avg_rating ?: 0, 1) }}
                                </strong>
                                <span>({{ $beautician->reviews_count }} reviews)</span>
                            </p>

                            <p style="margin-top: 14px; font-size: 12px; line-height: 1.6; color: #666; min-height: 38px;">
                                {{ Str::limit($beautician->bio ?: 'Dedicated beauty professional offering styling and care.', 72) }}
                            </p>

                            <a href="{{ route('beautician.view', $beautician->id) }}" class="btn btn--block"
                                style="margin-top: 16px;">
                                View profile &amp; services
                            </a>
                        </div>

                        @auth
                            @if (Auth::user()->role === 'customer')
                                <button type="button" class="fav-btn {{ in_array($beautician->id, $favoritedIds) ? 'is-on' : '' }}"
                                    onclick="toggleFavorite(this, {{ $beautician->id }})" title="Save to favourites">
                                    <x-icon name="heart" :size="17" />
                                </button>
                            @endif
                        @endauth
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        function toggleFavorite(button, beauticianId) {
            fetch(@json(route('customer.favorites.toggle')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify({ beautician_id: beauticianId })
                })
                .then(response => response.json())
                .then(data => button.classList.toggle('is-on', Boolean(data.favorited)))
                .catch(() => {});
        }
    </script>
@endsection