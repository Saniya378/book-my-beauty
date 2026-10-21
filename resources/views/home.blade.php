@extends('layouts.app')

@section('title', 'BookMyBeauty - Book Verified Beauticians Near You')

@section('content')
    <section class="hero">
        <h2>Book <span>Luxury Beauty</span> Services Online</h2>
        <p>
            Verified beauticians, stylists and makeup artists in your area with real reviews and live availability.
            Pick a service, choose a slot and you are booked.
        </p>

        <div class="hero-btns">
            <a href="{{ route('browse') }}" class="btn btn--light btn--lg">Browse beauticians</a>
            <a href="{{ route('register') }}" class="btn btn--accent btn--lg">Join as a beautician</a>
        </div>
    </section>

    <section class="stats">
        <div class="stats__item">
            <div class="stats__value">{{ $totalBeauticians }}+</div>
            <div class="stats__label">Verified beauticians</div>
        </div>
        <div class="stats__item">
            <div class="stats__value">{{ $totalBookings }}+</div>
            <div class="stats__label">Appointments completed</div>
        </div>
        <div class="stats__item">
            <div class="stats__value">{{ $totalServices }}+</div>
            <div class="stats__label">Services on offer</div>
        </div>
        <div class="stats__item">
            <div class="stats__value">{{ number_format($avgPlatformRating, 1) }} <span class="rating">&#9733;</span></div>
            <div class="stats__label">Average rating</div>
        </div>
    </section>

    @if ($featuredBeauticians->isNotEmpty())
        <section class="section">
            <div class="section__inner">
                <div class="section__head">
                    <h2 class="section__title">Featured <span>Beauticians</span></h2>
                    <p class="section__sub">Top rated artists our customers keep coming back to.</p>
                </div>

                <div class="grid grid--3">
                    @foreach ($featuredBeauticians as $beautician)
                        <article class="card" style="padding: 0; overflow: hidden; text-align: center;">
                            <div
                                style="display: flex; justify-content: center; padding: 26px 20px; background: linear-gradient(135deg, var(--primary), var(--primary-darker));">
                                <img src="{{ $beautician->photo_url }}" alt="{{ $beautician->user->name }}"
                                    style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent);">
                            </div>

                            <div style="padding: 20px;">
                                <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary);">
                                    {{ $beautician->user->name }}
                                </h3>

                                <p class="meta" style="justify-content: center; margin-top: 6px;">
                                    <x-icon name="map-pin" :size="14" />
                                    {{ $beautician->location ?: 'Location shared on booking' }}
                                </p>

                                <p class="meta" style="justify-content: center; margin-top: 10px;">
                                    <span class="rating">&#9733;</span>
                                    <strong style="color: var(--primary);">
                                        {{ number_format($beautician->reviews_avg_rating ?: 0, 1) }}
                                    </strong>
                                    <span>({{ $beautician->reviews_count }} reviews)</span>
                                </p>

                                <div style="margin: 16px 0 18px;">
                                    @forelse ($beautician->services->take(2) as $service)
                                        <span class="tag" style="margin: 2px;">
                                            {{ $service->service_name }} &middot; Rs. {{ number_format($service->price, 0) }}
                                        </span>
                                    @empty
                                        <span class="tag">Custom beauty services</span>
                                    @endforelse
                                </div>

                                <a href="{{ route('beautician.view', $beautician->id) }}"
                                    class="btn btn--block">View profile &amp; book</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div style="text-align: center; margin-top: 40px;">
                    <a href="{{ route('browse') }}" class="btn btn--ghost btn--lg">
                        Explore all beauticians
                        <x-icon name="arrow-right" :size="15" />
                    </a>
                </div>
            </div>
        </section>
    @endif

    <section class="section section--tint">
        <div class="section__inner">
            <div class="section__head">
                <h2 class="section__title">How it <span>works</span></h2>
                <p class="section__sub">Booking an appointment takes three steps and less than a minute.</p>
            </div>

            <div class="grid grid--3">
                <div class="card" style="text-align: center;">
                    <span class="icon-tile" style="margin: 0 auto 16px;"><x-icon name="search" :size="20" /></span>
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 17px; color: var(--primary);">
                        Search and explore
                    </h3>
                    <p class="section__sub" style="font-size: 13px; margin-top: 8px;">
                        Filter beauticians by city, service, price and customer rating.
                    </p>
                </div>

                <div class="card" style="text-align: center;">
                    <span class="icon-tile" style="margin: 0 auto 16px;"><x-icon name="clock" :size="20" /></span>
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 17px; color: var(--primary);">
                        Pick a service and slot
                    </h3>
                    <p class="section__sub" style="font-size: 13px; margin-top: 8px;">
                        Choose a treatment and a time from the beautician's live working hours.
                    </p>
                </div>

                <div class="card" style="text-align: center;">
                    <span class="icon-tile" style="margin: 0 auto 16px;"><x-icon name="check-circle" :size="20" /></span>
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 17px; color: var(--primary);">
                        Confirm and relax
                    </h3>
                    <p class="section__sub" style="font-size: 13px; margin-top: 8px;">
                        Get an instant confirmation, a PDF receipt and in app reminders.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--white">
        <div class="section__inner">
            <div class="section__head">
                <h2 class="section__title">Why <span>BookMyBeauty</span></h2>
                <p class="section__sub">
                    A calm, transparent way to find and book beauty professionals you can trust.
                </p>
            </div>

            <div class="grid grid--4">
                <div class="card" style="text-align: center;">
                    <span class="icon-tile" style="margin: 0 auto 14px;"><x-icon name="shield" :size="20" /></span>
                    <h3 style="font-size: 15px; font-weight: 600; color: var(--primary);">Verified profiles</h3>
                    <p class="section__sub" style="font-size: 12px; margin-top: 6px;">
                        Email verified accounts with real work in their portfolio.
                    </p>
                </div>

                <div class="card" style="text-align: center;">
                    <span class="icon-tile" style="margin: 0 auto 14px;"><x-icon name="bookings" :size="20" /></span>
                    <h3 style="font-size: 15px; font-weight: 600; color: var(--primary);">Instant booking</h3>
                    <p class="section__sub" style="font-size: 12px; margin-top: 6px;">
                        Live availability and confirmations without waiting on call.
                    </p>
                </div>

                <div class="card" style="text-align: center;">
                    <span class="icon-tile" style="margin: 0 auto 14px;"><x-icon name="message" :size="20" /></span>
                    <h3 style="font-size: 15px; font-weight: 600; color: var(--primary);">Bella AI assistant</h3>
                    <p class="section__sub" style="font-size: 12px; margin-top: 6px;">
                        Ask for recommendations, prices and aftercare advice at any hour.
                    </p>
                </div>

                <div class="card" style="text-align: center;">
                    <span class="icon-tile" style="margin: 0 auto 14px;"><x-icon name="review" :size="20" /></span>
                    <h3 style="font-size: 15px; font-weight: 600; color: var(--primary);">Honest reviews</h3>
                    <p class="section__sub" style="font-size: 12px; margin-top: 6px;">
                        Only customers who finished an appointment can leave a rating.
                    </p>
                </div>
            </div>

            <div style="text-align: center; margin-top: 38px;">
                <a href="{{ route('about') }}" class="btn btn--ghost">
                    Read more about us
                    <x-icon name="arrow-right" :size="15" />
                </a>
            </div>
        </div>
    </section>

    <section class="section section--dark">
        <div class="section__inner" style="max-width: 900px; text-align: center;">
            <h2 class="section__title">Get in <span>touch</span></h2>
            <p class="section__sub">
                Questions, feedback or a beautician you want on the platform? Send a message and we will reply.
            </p>

            <div class="meta-list" style="justify-content: center; margin: 26px 0 30px;">
                <span class="meta meta--light">
                    <x-icon name="mail" :size="16" />
                    <a href="mailto:support@bookmybeauty.com" style="color: inherit;">support@bookmybeauty.com</a>
                </span>
                <span class="meta meta--light">
                    <x-icon name="phone" :size="16" />
                    <a href="tel:+923001234567" style="color: inherit;">+92 300 123 4567</a>
                </span>
                <span class="meta meta--light">
                    <x-icon name="map-pin" :size="16" />
                    Karakoram International University, Gilgit
                </span>
            </div>

            <a href="{{ route('contact') }}" class="btn btn--light btn--lg">
                Contact us
                <x-icon name="arrow-right" :size="15" />
            </a>
        </div>
    </section>

    <section class="section section--tint">
        <div class="section__inner">
            <div class="cta-band">
                <div>
                    <h2 class="cta-band__title">Meet Bella, your salon assistant</h2>
                    <p class="cta-band__text">
                        Not sure which treatment suits you? Bella answers pricing, availability and aftercare questions
                        in seconds, by text or voice.
                    </p>
                </div>

                <button type="button" class="btn btn--accent btn--lg" onclick="toggleBellaChat()">
                    <x-icon name="message" :size="16" />
                    Chat with Bella
                </button>
            </div>
        </div>
    </section>
@endsection