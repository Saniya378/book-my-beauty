@extends('layouts.customer')

@section('title', 'Favorites - BookMyBeauty')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Favourite beauticians</h1>
            <p class="page-head__sub">Quick access to the artists you love most.</p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('browse') }}" class="btn btn--ghost">Browse more</a>
        </div>
    </div>

    @if ($favorites->isEmpty())
        <section class="panel">
            <div class="empty" style="padding: 55px 20px;">
                <span class="icon-tile" style="margin: 0 auto 16px; color: var(--bad); background: #fbeaea;">
                    <x-icon name="heart" :size="20" />
                </span>
                <strong>No favourites saved yet</strong>
                <p style="margin: 6px 0 20px;">Tap the heart on any beautician profile to keep it here.</p>
                <a href="{{ route('browse') }}" class="btn btn--accent">Browse beauticians</a>
            </div>
        </section>
    @else
        <div class="grid grid--3">
            @foreach ($favorites as $favourite)
                @if ($favourite->beautician)
                    <article class="card" id="fav-{{ $favourite->beautician_id }}"
                        style="padding: 0; overflow: hidden; text-align: center; position: relative;">

                        <div style="display: flex; justify-content: center; padding: 26px 20px; background: linear-gradient(135deg, var(--primary), var(--primary-darker));">
                            <img src="{{ $favourite->beautician->photo_url }}"
                                alt="{{ $favourite->beautician->user?->name }}"
                                style="width: 92px; height: 92px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent);">
                        </div>

                        <div style="padding: 18px 20px 22px;">
                            <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary);">
                                {{ $favourite->beautician->user?->name ?? 'Beautician' }}
                            </h3>

                            <p class="meta" style="justify-content: center; margin-top: 6px;">
                                <x-icon name="map-pin" :size="13" />
                                {{ $favourite->beautician->location ?: 'Location shared on booking' }}
                            </p>

                            <p class="meta" style="justify-content: center; margin-top: 10px;">
                                <span class="rating">&#9733;</span>
                                <strong style="color: var(--primary);">
                                    {{ number_format($favourite->beautician->reviews()->avg('rating') ?: 0, 1) }}
                                </strong>
                                <span>({{ $favourite->beautician->reviews()->count() }} reviews)</span>
                            </p>

                            <div style="display: flex; gap: 8px; margin-top: 18px;">
                                <a href="{{ route('beautician.view', $favourite->beautician_id) }}"
                                    class="btn btn--ghost" style="flex: 1;">Profile</a>
                                <a href="{{ route('customer.book', $favourite->beautician_id) }}"
                                    class="btn" style="flex: 1;">Book now</a>
                            </div>
                        </div>

                        <button type="button" class="fav-remove" title="Remove from favourites"
                            onclick="removeFavorite({{ $favourite->beautician_id }})">
                            <x-icon name="heart" :size="16" />
                        </button>
                    </article>
                @endif
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        function removeFavorite(beauticianId) {
            if (!confirm('Remove this beautician from your favourites?')) return;

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
                    if (!data.favorited) {
                        document.getElementById('fav-' + beauticianId)?.remove();
                    }
                })
                .catch(() => {});
        }
    </script>
@endsection