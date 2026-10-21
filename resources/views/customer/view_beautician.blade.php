@extends('layouts.app')

@section('title', $beautician->user->name . ' - BookMyBeauty')

@section('content')
    <div class="portal">
        @include('components.alert')

        <section class="profile-hero">
            <img src="{{ $beautician->photo_url }}" alt="{{ $beautician->user->name }}" class="profile-avatar">

            <div class="hero-info">
                <h1>{{ $beautician->user->name }}</h1>

                <div class="meta-list meta--light" style="margin-bottom: 10px;">
                    <span class="meta meta--light">
                        <x-icon name="map-pin" :size="15" />
                        {{ $beautician->location ?: 'Location shared on booking' }}
                    </span>

                    @if ($beautician->phone)
                        <span class="meta meta--light">
                            <x-icon name="phone" :size="15" />
                            {{ $beautician->phone }}
                        </span>
                    @endif
                </div>

                <p class="meta meta--light" style="margin-bottom: 12px;">
                    <span class="rating" style="font-size: 15px;">&#9733;</span>
                    <strong>{{ number_format($beautician->reviews_avg_rating ?: 0, 1) }}</strong>
                    <span>({{ $beautician->reviews_count }} verified reviews)</span>
                </p>

                <p style="font-size: 13px; opacity: 0.85; line-height: 1.6; max-width: 620px; margin: 0;">
                    {{ $beautician->bio ?: 'Experienced beauty artist offering specialised salon treatments and personal care.' }}
                </p>
            </div>

            <div class="hero-actions">
                <a href="{{ route('customer.book', $beautician->id) }}" class="btn btn--accent">Book appointment</a>

                @auth
                    @if (Auth::user()->role === 'customer')
                        <button type="button" class="btn btn--light" onclick="toggleProfileFavorite(this, {{ $beautician->id }})">
                            <x-icon name="heart" :size="15" />
                            <span id="fav-text">{{ $isFavorited ? 'Saved' : 'Save to favourites' }}</span>
                        </button>

                        @if ($canReview)
                            <a href="{{ route('customer.review', $beautician->id) }}" class="link-row"
                                style="justify-content: center; color: var(--accent);">
                                Write a review
                            </a>
                        @else
                            <span class="meta meta--light" style="justify-content: center; font-size: 11px;">
                                Reviews open after a confirmed visit
                            </span>
                        @endif
                    @endif
                @endauth
            </div>
        </section>

        <div class="section-title">
            <span>Services ({{ $beautician->activeServices->count() }})</span>
            <a href="{{ route('customer.book', $beautician->id) }}" class="link-row">
                Book a service
                <x-icon name="arrow-right" :size="14" />
            </a>
        </div>

        <div class="services-grid">
            @forelse ($beautician->activeServices as $service)
                <article class="service-card">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px;">
                            <h3>{{ $service->service_name }}</h3>
                            <span class="service-card__price">Rs. {{ number_format($service->price, 0) }}</span>
                        </div>

                        <p class="meta" style="margin: 6px 0 10px;">
                            <x-icon name="clock" :size="13" />
                            {{ $service->duration }} minutes
                        </p>

                        <p class="service-card__text">
                            {{ $service->description ?: 'Standard professional salon care.' }}
                        </p>
                    </div>

                    <a href="{{ route('customer.book', $beautician->id) }}?service_id={{ $service->id }}"
                        class="btn btn--ghost btn--block">Book this service</a>
                </article>
            @empty
                <div class="empty">No services are listed at the moment.</div>
            @endforelse
        </div>

        @php
            $hoursByDay = $beautician->availability->keyBy('day_of_week');
            $week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        @endphp

        <div class="section-title">
            <span>
                <x-icon name="clock" :size="15" />
                Working schedule
            </span>
        </div>

        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(104px, 1fr)); gap: 10px; margin-bottom: 34px;">
            @foreach ($week as $day)
                @php
                    $slot = $hoursByDay->get($day);
                @endphp

                <div class="day-card {{ $slot ? 'is-open' : '' }}">
                    <div class="day-card__name">{{ Str::substr($day, 0, 3) }}</div>

                    @if ($slot)
                        <span class="pill pill--confirmed">Open</span>
                        <div class="day-card__hours">
                            {{ date('g:i A', strtotime($slot->start_time)) }}<br>
                            to<br>
                            {{ date('g:i A', strtotime($slot->end_time)) }}
                        </div>
                    @else
                        <span class="pill pill--cancelled">Closed</span>
                        <div class="day-card__hours">Off day</div>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($beautician->portfolioImages->isNotEmpty())
            <div class="section-title">
                <span>
                    <x-icon name="camera" :size="15" />
                    Recent work
                </span>
            </div>

            <div class="portfolio-grid">
                @foreach ($beautician->portfolioImages as $image)
                    <figure class="portfolio-item" onclick="window.open('{{ $image->url }}', '_blank')">
                        <img src="{{ $image->url }}" alt="{{ $image->caption }}">
                        @if ($image->caption)
                            <figcaption>{{ $image->caption }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @endif

        <div class="section-title">
            <span>Client reviews</span>
            @auth
                @if (Auth::user()->role === 'customer' && $canReview)
                    <a href="{{ route('customer.review', $beautician->id) }}" class="link-row">
                        Write a review
                        <x-icon name="arrow-right" :size="14" />
                    </a>
                @endif
            @endauth
        </div>

        <div class="reviews-list">
            @forelse ($beautician->reviews as $review)
                <article class="review-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                        <strong>{{ $review->customer?->name ?? 'Verified customer' }}</strong>
                        <span class="rating">
                            @for ($star = 1; $star <= 5; $star++)
                                {!! $star <= $review->rating ? '&#9733;' : '&#9734;' !!}
                            @endfor
                        </span>
                    </div>

                    <p class="service-card__text">{{ $review->comment }}</p>
                    <span class="rows__meta">Reviewed on {{ $review->created_at?->format('j M Y') }}</span>
                </article>
            @empty
                <div class="empty">No reviews yet. Book an appointment and be the first to leave feedback.</div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleProfileFavorite(button, beauticianId) {
            fetch(@json(route('customer.favorites.toggle')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify({ beautician_id: beauticianId })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('fav-text').textContent = data.favorited ? 'Saved' : 'Save to favourites';
                })
                .catch(() => {});
        }
    </script>
@endsection