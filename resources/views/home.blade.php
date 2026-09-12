@extends('layouts.app')

@section('title', 'BookMyBeauty - Luxury Salon Booking & Services')

@section('styles')
    <style>
        .stats-strip {
            background: white;
            display: flex;
            justify-content: center;
            gap: 60px;
            padding: 40px 20px;
            flex-wrap: wrap;
            border-bottom: 1px solid #f0f0f0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item .number {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Playfair Display', serif;
        }

        .stat-item .label {
            font-size: 11px;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        .how-it-works {
            padding: 80px 40px;
            background: var(--light-bg);
        }

        .section-heading {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-heading h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .section-heading p {
            color: var(--text-gray);
            font-size: 14px;
        }

        .steps {
            display: flex;
            justify-content: center;
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
            flex-wrap: wrap;
        }

        .step {
            text-align: center;
            flex: 1;
            min-width: 220px;
        }

        .step-number {
            width: 54px;
            height: 54px;
            background: var(--primary);
            color: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            margin: 0 auto 20px;
            border: 2px solid var(--accent);
        }

        .step h4 {
            color: var(--primary);
            font-size: 17px;
            margin-bottom: 10px;
            font-family: 'Playfair Display', serif;
        }

        .step p {
            color: var(--text-gray);
            font-size: 13px;
            line-height: 1.7;
        }

        .beautician-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: all 0.3s ease;
            text-align: center;
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
    </style>
@endsection

@section('content')
    <!-- Hero Section -->
    <div class="hero"
        style="background: linear-gradient(rgba(26,26,46,0.85), rgba(15,52,96,0.9)), url('{{ asset('images/hero.jpg') }}') center/cover no-repeat; padding: 110px 40px; text-align: center; color: white;">
        <div class="hero-content" style="max-width: 800px; margin: 0 auto;">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 46px; line-height: 1.25; margin-bottom: 20px;">
                Book <span style="color: var(--accent);">Luxury Beauty</span> Services Online
            </h2>
            <p style="font-size: 16px; opacity: 0.9; max-width: 600px; margin: 0 auto 35px; line-height: 1.6;">
                Connect with verified, elite beauticians and stylists in your area. Simple online scheduling, no waiting,
                guaranteed professionalism.
            </p>
            <div class="hero-btns" style="display: flex; justify-content: center; gap: 15px;">
                <a href="{{ route('browse') }}" class="btn btn-outline"
                    style="padding: 14px 34px; font-size: 14px; font-weight: 600; color:var(--accent); border-color: white;">Browse
                    Beauticians</a>
                <a href="{{ route('register') }}" class="btn btn-outline"
                    style="padding: 14px 34px; font-size: 14px; font-weight: 600; color: var(--accent); border-color: white;">Join
                    as Beautician</a>
            </div>
        </div>
    </div>

    <!-- Stats Strip -->
    <div class="stats-strip">
        <div class="stat-item">
            <div class="number">{{ max($totalBeauticians, 8) }}+</div>
            <div class="label">Verified Beauticians</div>
        </div>
        <div class="stat-item">
            <div class="number">{{ max($totalBookings, 20) }}+</div>
            <div class="label">Appointments Completed</div>
        </div>
        <div class="stat-item">
            <div class="number">{{ max($totalServices, 17) }}+</div>
            <div class="label">Specialized Services</div>
        </div>
        <div class="stat-item">
            <div class="number">4.9 ★</div>
            <div class="label">Average Rating</div>
        </div>
    </div>

    <!-- Featured Beauticians -->
    @if ($featuredBeauticians->isNotEmpty())
        <div style="padding: 70px 40px; max-width: 1200px; margin: 0 auto;">
            <div class="section-heading">
                <h2>Featured Beauticians</h2>
                <p>Top-rated artists trusted by hundreds of satisfied clients</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px;">
                @foreach ($featuredBeauticians as $b)
                    <div class="beautician-card">
                        <div class="card-photo-area">
                            <img src="{{ $b->photo_url }}" alt="{{ $b->user->name }}">
                        </div>
                        <div style="padding: 22px 20px;">
                            <h4
                                style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin-bottom: 5px;">
                                {{ $b->user->name }}</h4>
                            <p style="font-size: 12px; color: var(--text-gray); margin-bottom: 12px;">📍
                                {{ $b->location ?: 'Location available upon booking' }}</p>

                            <div
                                style="display: flex; justify-content: center; align-items: center; gap: 6px; margin-bottom: 15px; font-size: 13px;">
                                <span style="color: #f59e0b;">★</span>
                                <strong>{{ number_format($b->reviews_avg_rating ?: 5.0, 1) }}</strong>
                                <span style="color: var(--text-gray); font-size: 12px;">({{ $b->reviews_count }}
                                    reviews)</span>
                            </div>

                            <div style="margin-bottom: 20px; min-height: 40px;">
                                @forelse($b->services->take(2) as $s)
                                    <span
                                        style="display: inline-block; background: var(--light-bg); border: 1px solid var(--border); border-radius: 20px; font-size: 11px; padding: 3px 10px; margin: 2px; color: #555;">
                                        {{ $s->service_name }} (Rs. {{ number_format($s->price, 0) }})
                                    </span>
                                @empty
                                    <span style="font-size: 12px; color: #999;">Custom Beauty Services</span>
                                @endforelse
                            </div>

                            <a href="{{ route('beautician.view', $b->id) }}" class="btn btn-outline"
                                style="width: 100%; padding: 10px; font-size: 13px; text-decoration: none; display: block; border-radius: 6px;">View
                                Profile & Book</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ route('browse') }}" class="btn" style="padding: 12px 30px; font-size: 14px;">Explore All
                    Beauticians &rarr;</a>
            </div>
        </div>
    @endif

    <!-- How It Works -->
    <div class="how-it-works">
        <div class="section-heading">
            <h2>How It Works</h2>
            <p>Book your beauty appointment in three effortless steps</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h4>Search & Explore</h4>
                <p>Browse beauticians by location, service specialties, pricing, and authentic verified customer ratings.
                </p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h4>Select Service & Slot</h4>
                <p>Pick the service you want, select your preferred date, and choose from the beautician's live working
                    hours.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h4>Confirm & Relax</h4>
                <p>Get instant confirmation, download a PDF receipt, receive notifications, and enjoy your personalized
                    appointment.</p>
            </div>
        </div>
    </div>

    <!-- AI Banner -->
    <div
        style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 50px 30px; border-radius: var(--border-radius); max-width: 1100px; margin: 60px auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 25px; border-left: 5px solid var(--accent); box-shadow: var(--shadow);">
        <div style="max-width: 650px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span
                    style="background: var(--accent); color: var(--primary); font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 10px; text-transform: uppercase;">AI
                    Powered</span>
                <h3 style="font-family: 'Playfair Display', serif; font-size: 24px; margin: 0;">Meet Bella, Your Smart Salon
                    Assistant</h3>
            </div>
            <p style="font-size: 14px; opacity: 0.85; line-height: 1.6; margin: 0;">Need style advice, pricing information,
                or help finding the right beautician? Tap the floating icon at the bottom right to chat with Bella using
                text, voice, or even photo uploads!</p>
        </div>
        <div>
            <button type="button" onclick="toggleBellaChat()" class="btn"
                style="padding: 12px 28px; font-size: 14px;">Chat With Bella &rarr;</button>
        </div>
    </div>
@endsection
