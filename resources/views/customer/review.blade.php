@extends('layouts.customer')

@section('title', 'Review ' . $beautician->user->name)

@section('styles')
    <style>
        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 6px;
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
        }

        .star-rating input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .star-rating label {
            margin: 0;
            color: #dcdce4;
            letter-spacing: 0;
            text-transform: none;
            transition: color 0.2s;
        }

        .star-rating label:hover,
        .star-rating label:hover~label,
        .star-rating input:checked~label {
            color: #d9a441;
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 760px; margin: 0 auto;">
        <div class="page-head">
            <div>
                <h1 class="page-head__title">Rate &amp; review</h1>
                <p class="page-head__sub">Your feedback for {{ $beautician->user->name }}</p>
            </div>

            <div class="page-head__actions">
                <a href="{{ route('beautician.view', $beautician->id) }}" class="btn btn--ghost">
                    <x-icon name="arrow-left" :size="15" />
                    View profile
                </a>
            </div>
        </div>

        <section class="panel" style="margin-bottom: 22px;">
            <div class="panel__body">
                @if ($existingReview)
                    <div class="notice notice--ok" style="margin-bottom: 18px;">
                        <span class="notice__icon"><x-icon name="check-circle" :size="17" /></span>
                        <span>
                            <strong>You already reviewed this beautician.</strong>
                            <span class="rating">
                                @for ($star = 1; $star <= 5; $star++)
                                    {!! $star <= $existingReview->rating ? '&#9733;' : '&#9734;' !!}
                                @endfor
                            </span>
                            <br>{{ $existingReview->comment }}
                        </span>
                    </div>
                @else
                    <form action="{{ route('customer.review.store', $beautician->id) }}" method="POST">
                        @csrf

                        <div class="field">
                            <label>Your rating</label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" required>
                                <label for="star5" title="5 stars">&#9733;</label>
                                <input type="radio" id="star4" name="rating" value="4">
                                <label for="star4" title="4 stars">&#9733;</label>
                                <input type="radio" id="star3" name="rating" value="3">
                                <label for="star3" title="3 stars">&#9733;</label>
                                <input type="radio" id="star2" name="rating" value="2">
                                <label for="star2" title="2 stars">&#9733;</label>
                                <input type="radio" id="star1" name="rating" value="1">
                                <label for="star1" title="1 star">&#9733;</label>
                            </div>
                        </div>

                        <div class="field">
                            <label for="comment">Your comments</label>
                            <textarea id="comment" name="comment" rows="4" required
                                placeholder="Share the styling quality, punctuality and overall experience..."></textarea>
                        </div>

                        <button type="submit" class="btn btn--accent">Submit review</button>
                    </form>
                @endif
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Customer reviews</h2>
                    <p class="panel__meta">{{ $beautician->reviews->count() }} review{{ $beautician->reviews->count() === 1 ? '' : 's' }} so far</p>
                </div>
            </div>

            <div class="panel__body panel__body--flush">
                <ul class="rows">
                    @forelse ($beautician->reviews as $review)
                        <li class="rows--stacked">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; width: 100%;">
                                <span class="rows__title">{{ $review->customer?->name ?? 'Verified customer' }}</span>
                                <span class="rating">
                                    @for ($star = 1; $star <= 5; $star++)
                                        {!! $star <= $review->rating ? '&#9733;' : '&#9734;' !!}
                                    @endfor
                                </span>
                            </div>

                            <p class="rows__note">{{ $review->comment }}</p>
                            <span class="rows__meta">{{ $review->created_at?->format('j M Y') }}</span>
                        </li>
                    @empty
                        <li class="empty">No reviews yet, be the first.</li>
                    @endforelse
                </ul>
            </div>
        </section>
    </div>
@endsection