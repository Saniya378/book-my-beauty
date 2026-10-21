@extends('layouts.app')

@section('title', 'About Us - BookMyBeauty | Luxury Salon & Beautician Booking Platform')

@section('styles')
    <style>
        .about-hero {
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.85), rgba(45, 53, 97, 0.9)), url('{{ asset('images/hero.jpg') }}') center/cover no-repeat;
            padding: 90px 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .about-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 44px;
            margin-bottom: 16px;
        }

        .about-hero h1 span {
            color: var(--accent);
        }

        .about-hero p {
            font-size: 16px;
            opacity: 0.9;
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .about-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 70px 30px;
        }

        .about-intro-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 50px;
            align-items: center;
            margin-bottom: 80px;
        }

        .about-intro-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .about-intro-text .gold-bar {
            width: 60px;
            height: 3px;
            background: var(--accent);
            margin-bottom: 25px;
        }

        .about-intro-text p {
            font-size: 15px;
            color: var(--text-gray);
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .about-intro-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            border-radius: var(--border-radius);
            padding: 40px 35px;
            color: white;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--accent);
            position: relative;
        }

        .about-intro-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .about-quote {
            font-size: 16px;
            font-style: italic;
            line-height: 1.7;
            opacity: 0.95;
            margin-bottom: 25px;
        }

        .about-stat-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 25px;
        }

        .about-stat-box .num {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--accent);
        }

        .about-stat-box .lbl {
            font-size: 12px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Mission & Vision Section */
        .mv-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 80px;
        }

        .mv-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 40px 35px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .mv-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--accent);
        }

        .mv-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .mv-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            margin-bottom: 16px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: var(--accent);
        }

        .mv-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .mv-card p {
            font-size: 14px;
            color: var(--text-gray);
            line-height: 1.7;
        }

        /* Features Section */
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .section-header p {
            font-size: 14px;
            color: var(--text-gray);
            max-width: 600px;
            margin: 0 auto;
        }

        .why-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 80px;
        }

        .why-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 30px 25px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .why-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: transparent;
        }

        .why-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: var(--accent);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 20px;
            box-shadow: 0 4px 15px rgba(45, 53, 97, 0.15);
        }

        .why-card h4 {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .why-card p {
            font-size: 13px;
            color: var(--text-gray);
            line-height: 1.6;
        }

        /* Project / Academic Context */
        .fyp-box {
            background: linear-gradient(135deg, #fbf8f2, #f5efe6);
            border: 1px solid rgba(224, 189, 125, 0.4);
            border-radius: var(--border-radius);
            padding: 40px;
            margin-bottom: 70px;
            display: flex;
            align-items: center;
            gap: 35px;
        }

        .fyp-badge {
            flex-shrink: 0;
            width: 90px;
            height: 90px;
            background: var(--primary);
            border: 2px solid var(--accent);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            text-align: center;
        }

        .fyp-badge span:first-child {
            font-size: 22px;
            font-weight: 800;
        }

        .fyp-badge span:last-child {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
        }

        .fyp-info h3 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .fyp-info p {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            margin: 0;
        }

        /* CTA Banner */
        .about-cta {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: white;
            padding: 50px 40px;
            border-radius: var(--border-radius);
            text-align: center;
            border-bottom: 4px solid var(--accent);
            box-shadow: var(--shadow);
        }

        .about-cta h3 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            margin-bottom: 12px;
        }

        .about-cta p {
            font-size: 15px;
            opacity: 0.9;
            max-width: 550px;
            margin: 0 auto 25px;
            line-height: 1.6;
        }

        .about-cta-btns {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        @media (max-width: 992px) {
            .about-intro-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .why-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .mv-grid {
                grid-template-columns: 1fr;
            }

            .why-grid {
                grid-template-columns: 1fr;
            }

            .fyp-box {
                flex-direction: column;
                text-align: center;
            }

            .about-hero h1 {
                font-size: 32px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Hero Banner -->
    <div class="about-hero">
        <h1>About <span>BookMyBeauty</span></h1>
        <p>Transforming how clients discover beauty services and how talented beauticians grow their independent businesses.</p>
    </div>

    <div class="about-wrap">
        <!-- Intro Section -->
        <div class="about-intro-grid">
            <div class="about-intro-text">
                <h2>Redefining the Salon Experience</h2>
                <div class="gold-bar"></div>
                <p>
                    <strong>BookMyBeauty</strong> is an all-in-one digital platform created to connect individuals with verified,
                    talented beauticians and stylists. We eliminate the frustration of waiting in long queues, uncertain pricing,
                    and inconvenient scheduling by bringing effortless online appointment booking directly to your screen.
                </p>
                <p>
                    Whether you need a quick haircut, a relaxing facial, professional skincare treatments, or a comprehensive bridal
                    makeover, BookMyBeauty helps you discover top-rated service providers, view real customer feedback, compare prices,
                    and book appointments seamlessly.
                </p>
                <p>
                    At the same time, we empower independent beauticians and salon owners with dedicated digital storefronts, portfolio
                    showcases, dynamic availability calendars, and automated booking management.
                </p>
            </div>

            <div class="about-intro-card">
                <h3>Our Vision</h3>
                <p class="about-quote">
                    "To become the most reliable and elegant digital beauty ecosystem, connecting passionate artists with delighted clients through seamless technology."
                </p>
                <div class="about-stat-row">
                    <div class="about-stat-box">
                        <div class="num">{{ $totalBeauticians }}+</div>
                        <div class="lbl">Active Beauticians</div>
                    </div>
                    <div class="about-stat-box">
                        <div class="num">{{ $totalBookings }}+</div>
                        <div class="lbl">Bookings Handled</div>
                    </div>
                    <div class="about-stat-box">
                        <div class="num">{{ $totalServices }}+</div>
                        <div class="lbl">Services Offered</div>
                    </div>
                    <div class="about-stat-box">
                        <div class="num">100%</div>
                        <div class="lbl">Verified Feedback</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission & Values -->
        <div class="mv-grid">
            <div class="mv-card">
                <div class="mv-icon"><x-icon name="target" :size="24" /></div>
                <h3>Our Mission</h3>
                <p>
                    To democratize beauty service access by providing a transparent, trustworthy, and modern platform where
                    every customer finds their perfect stylist and every beautician gets the opportunity to shine and grow.
                </p>
            </div>

            <div class="mv-card">
                <div class="mv-icon"><x-icon name="shield" :size="24" /></div>
                <h3>Our Core Values</h3>
                <p>
                    We hold ourselves to the highest standards of transparency, quality assurance, user safety, and tech-driven innovation â€”
                    ensuring every appointment booked is dependable, punctual, and satisfying.
                </p>
            </div>
        </div>

        <!-- Why Choose Us -->
        <div class="section-header">
            <h2>Why Choose BookMyBeauty</h2>
            <p>Designed with beauty enthusiasts and professional stylists in mind.</p>
        </div>

        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon"><x-icon name="services" :size="24" /></div>
                <h4>Verified Beauticians</h4>
                <p>Every professional profile is reviewed and vetted, ensuring high service quality and skill authenticity.</p>
            </div>

            <div class="why-card">
                <div class="why-icon"><x-icon name="bookings" :size="24" /></div>
                <h4>Live Slot Scheduling</h4>
                <p>Choose exact dates and available time slots with instantaneous automated confirmations.</p>
            </div>

            <div class="why-card">
                <div class="why-icon"><x-icon name="message" :size="24" /></div>
                <h4>Bella AI Chatbot</h4>
                <p>Our smart Gemini-powered assistant answers beauty questions, recommends services, and provides pricing advice 24/7.</p>
            </div>

            <div class="why-card">
                <div class="why-icon"><x-icon name="review" :size="24" /></div>
                <h4>Authentic Reviews</h4>
                <p>Only clients with completed appointments can leave ratings, keeping reviews genuine and trustworthy.</p>
            </div>

            <div class="why-card">
                <div class="why-icon"><x-icon name="paper" :size="24" /></div>
                <h4>Automated PDF Receipts</h4>
                <p>Download structured digital invoices with full appointment details, timestamps, and beautician contacts.</p>
            </div>

            <div class="why-card">
                <div class="why-icon"><x-icon name="bell" :size="24" /></div>
                <h4>Real-time Notifications</h4>
                <p>Receive live status updates for booking approvals, cancellations, and appointment reminders.</p>
            </div>
        </div>

        <!-- Academic / FYP Context -->
        <div class="fyp-box">
            <div class="fyp-badge">
                <span>KIU</span>
                <span>FYP</span>
            </div>
            <div class="fyp-info">
                <h3>Final Year Project (FYP)</h3>
                <p>
                    BookMyBeauty was researched, architected, and developed as a <strong>Final Year Project</strong> at
                    <strong>Karakoram International University (KIU)</strong>, Department of Computer Science.
                    It demonstrates modern software engineering, role-based access control, AI chatbot integration with Google Gemini,
                    and clean web application design.
                </p>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="about-cta">
            <h3>Ready to Experience BookMyBeauty?</h3>
            <p>Explore top beauticians near you or start receiving client bookings today.</p>
            <div class="about-cta-btns">
                <a href="{{ route('browse') }}" class="btn btn--accent">Browse Beauticians</a>
                <a href="{{ route('register') }}" class="btn btn--light">
                    Join as Beautician
                </a>
            </div>
        </div>
    </div>
@endsection
