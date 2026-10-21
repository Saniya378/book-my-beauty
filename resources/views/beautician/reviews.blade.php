@extends('layouts.beautician')

@section('title', 'My Reviews - Beautician Studio')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Customer reviews</h1>
            <p class="page-head__sub">What clients said after their appointments with you.</p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('beautician.profile') }}" class="btn btn--ghost">
                <x-icon name="eye" :size="15" />
                View public profile
            </a>
        </div>
    </div>

    <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));">
        <div class="kpi">
            <span class="kpi__icon"><x-icon name="rating" :size="19" /></span>
            <div>
                <div class="kpi__value">
                    {{ number_format($average, 1) }} <span class="rating">&#9733;</span>
                </div>
                <div class="kpi__label">Average rating</div>
                <div class="kpi__note">Across {{ $reviews->total() }} review{{ $reviews->total() === 1 ? '' : 's' }}</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="check-circle" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $confirmed }}</div>
                <div class="kpi__label">Completed appointments</div>
                <div class="kpi__note">Each one can leave one review</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="services" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $activeServices }}</div>
                <div class="kpi__label">Active services</div>
                <div class="kpi__note">Visible on your profile</div>
            </div>
        </div>
    </div>

    <div class="admin-grid admin-grid--even">
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Rating breakdown</h2>
                    <p class="panel__meta">How your {{ number_format($average, 1) }} average is spread</p>
                </div>
            </div>

            <div class="panel__body">
                @if ($reviews->total() === 0)
                    <div class="empty" style="padding: 26px 0;">
                        No reviews yet
                    </div>
                @else
                    <div class="score">
                        <div class="score__value">{{ number_format($average, 1) }}</div>

                        <div class="score__bars">
                            @for ($star = 5; $star >= 1; $star--)
                                @php
                                    $count = (int) ($breakdown[$star] ?? 0);
                                    $percent = $reviews->total() ? round($count / $reviews->total() * 100) : 0;
                                @endphp

                                <div class="star-bar">
                                    <span>{{ $star }} star</span>
                                    <span class="star-bar__track">
                                        <span class="star-bar__fill" style="width: {{ $percent }}%"></span>
                                    </span>
                                    <span class="star-bar__count">{{ $count }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">How reviews work</h2>
                    <p class="panel__meta">Keeps your rating honest</p>
                </div>
            </div>

            <div class="panel__body">
                <div class="notice notice--info" style="margin-bottom: 14px;">
                    <span class="notice__icon"><x-icon name="info" :size="17" /></span>
                    <span>A client can review you only after an appointment with you has been confirmed.</span>
                </div>

                <div class="notice notice--ok" style="margin-bottom: 14px;">
                    <span class="notice__icon"><x-icon name="check-circle" :size="17" /></span>
                    <span>One review per customer. Repeat visits still give them a single voice.</span>
                </div>

                <div class="notice notice--warn">
                    <span class="notice__icon"><x-icon name="warning" :size="17" /></span>
                    <span>Confirming appointments promptly is the easiest way to grow your review count.</span>
                </div>
            </div>
        </section>
    </div>

    <section class="panel">
        <div class="panel__head">
            <div>
                <h2 class="panel__title">All reviews</h2>
                <p class="panel__meta">Newest first</p>
            </div>
        </div>

        <div class="panel__body panel__body--flush">
            @forelse ($reviews as $review)
                <article class="rows--stacked" style="display: flex; gap: 14px; padding: 18px 20px; border-bottom: 1px solid #f4f4f4;">
                    <img class="avatar" src="{{ $profile->photo_url }}" alt=""
                        style="width: 40px; height: 40px;">

                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                            <span class="rows__title">{{ $review->customer?->name ?? 'Verified customer' }}</span>

                            <span class="rating">
                                @for ($star = 1; $star <= 5; $star++)
                                    {!! $star <= $review->rating ? '&#9733;' : '&#9734;' !!}
                                @endfor
                            </span>
                        </div>

                        @if ($review->comment)
                            <p class="rows__note" style="margin-top: 7px;">{{ $review->comment }}</p>
                        @else
                            <p class="rows__meta" style="margin-top: 7px;">Rated without a written comment.</p>
                        @endif

                        <span class="rows__meta">{{ $review->created_at?->diffForHumans() }}</span>
                    </div>
                </article>
            @empty
                <div class="empty" style="padding: 48px 20px;">
                    <strong>No reviews yet</strong>
                    <p style="margin: 6px 0 0;">
                        Confirmed appointments are what unlock reviews. Keep your schedule up to date so clients can book.
                    </p>
                </div>
            @endforelse
        </div>

        @if ($reviews->hasPages())
            <div class="panel__foot">
                {{ $reviews->links('vendor.pagination.custom') }}
            </div>
        @endif
    </section>
@endsection