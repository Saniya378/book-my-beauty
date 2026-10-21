@extends('layouts.app')

@section('title', 'Contact Us - BookMyBeauty | Support & Inquiries')

@section('styles')
    <style>
        .contact-hero {
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.88), rgba(45, 53, 97, 0.92)), url('{{ asset('images/hero.jpg') }}') center/cover no-repeat;
            padding: 85px 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .contact-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            margin-bottom: 14px;
        }

        .contact-hero h1 span {
            color: var(--accent);
        }

        .contact-hero p {
            font-size: 15px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .contact-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 30px;
        }

        .contact-main-grid {
            display: grid;
            grid-template-columns: 1.1fr 1.3fr;
            gap: 45px;
            margin-bottom: 70px;
        }

        /* Contact Details Side */
        .contact-details-panel {
            background: white;
            border-radius: var(--border-radius);
            padding: 40px 35px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .contact-details-panel h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .gold-bar {
            width: 50px;
            height: 3px;
            background: var(--accent);
            margin-bottom: 20px;
        }

        .contact-details-panel p.intro {
            font-size: 14px;
            color: var(--text-gray);
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .info-cards-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-card-item {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            padding: 16px 18px;
            background: var(--light-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .info-card-item:hover {
            transform: translateX(5px);
            border-color: var(--accent);
            background: #fffdfa;
        }

        .info-card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: var(--accent);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-card-item h4 {
            font-size: 15px;
            color: var(--primary);
            margin-bottom: 4px;
            font-weight: 600;
        }

        .info-card-item p {
            font-size: 13px;
            color: var(--text-gray);
            margin: 0;
            line-height: 1.5;
        }

        .info-card-item a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .info-card-item a:hover {
            color: #b89350;
        }

        /* Contact Form Side */
        .contact-form-panel {
            background: white;
            border-radius: var(--border-radius);
            padding: 40px 35px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .contact-form-panel h3 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .contact-form-panel p.form-subtitle {
            font-size: 13px;
            color: var(--text-gray);
            margin-bottom: 25px;
        }

        .c-form-group {
            margin-bottom: 20px;
        }

        .c-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .c-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--primary);
            margin-bottom: 7px;
        }

        .c-form-group input,
        .c-form-group textarea,
        .c-form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            transition: border-color 0.3s, box-shadow 0.3s;
            outline: none;
            background: #fafafa;
            box-sizing: border-box;
        }

        .c-form-group input:focus,
        .c-form-group textarea:focus,
        .c-form-group select:focus {
            border-color: var(--accent);
            background: white;
            box-shadow: 0 0 0 3px rgba(224, 189, 125, 0.25);
        }

        .c-form-group textarea {
            height: 130px;
            resize: vertical;
        }

        .c-submit-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: var(--accent);
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .c-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(45, 53, 97, 0.3);
            color: white;
        }

        .alert-success-box {
            background: #eaf8ee;
            border: 1px solid #7ed898;
            color: #1e7e34;
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* FAQ Section */
        .faq-section {
            margin-top: 40px;
        }

        .faq-section .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .faq-section .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .faq-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px;
        }

        .faq-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 25px 22px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .faq-card h4 {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            color: var(--primary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .faq-card h4 span {
            color: var(--accent);
            font-weight: bold;
        }

        .faq-card p {
            font-size: 13px;
            color: var(--text-gray);
            line-height: 1.6;
            margin: 0;
        }

        @media (max-width: 992px) {
            .contact-main-grid {
                grid-template-columns: 1fr;
            }

            .faq-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .c-form-row {
                grid-template-columns: 1fr;
            }

            .contact-hero h1 {
                font-size: 30px;
            }

            .contact-details-panel,
            .contact-form-panel {
                padding: 30px 20px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Hero Banner -->
    <div class="contact-hero">
        <h1>Get In <span>Touch</span></h1>
        <p>Have questions about appointments, services, or joining as a beauty professional? We're here to help.</p>
    </div>

    <div class="contact-wrap">
        <!-- Main Grid -->
        <div class="contact-main-grid">
            <!-- Contact Info Panel -->
            <div class="contact-details-panel">
                <h2>Contact Information</h2>
                <div class="gold-bar"></div>
                <p class="intro">
                    Reach out to our team via phone, email, or visit our university project department. We typically respond within 24 hours.
                </p>

                <div class="info-cards-list">
                    <div class="info-card-item">
                        <div class="info-card-icon"><x-icon name="mail" :size="18" /></div>
                        <div>
                            <h4>Email Support</h4>
                            <p><a href="mailto:support@bookmybeauty.com">support@bookmybeauty.com</a></p>
                            <p style="font-size: 11px; color: #888;">For inquiries, assistance & support</p>
                        </div>
                    </div>

                    <div class="info-card-item">
                        <div class="info-card-icon"><x-icon name="phone" :size="18" /></div>
                        <div>
                            <h4>Phone / Helpline</h4>
                            <p><a href="tel:+923001234567">+92 300 123 4567</a></p>
                            <p style="font-size: 11px; color: #888;">Available Mon â€“ Sat, 9am â€“ 6pm</p>
                        </div>
                    </div>

                    <div class="info-card-item">
                        <div class="info-card-icon"><x-icon name="map-pin" :size="18" /></div>
                        <div>
                            <h4>Location</h4>
                            <p><strong>Department of Computer Science</strong></p>
                            <p>Karakoram International University, Gilgit, Pakistan</p>
                        </div>
                    </div>

                    <div class="info-card-item">
                        <div class="info-card-icon"><x-icon name="clock" :size="18" /></div>
                        <div>
                            <h4>Operating Hours</h4>
                            <p>Monday â€“ Saturday: <strong>9:00 AM â€“ 6:00 PM</strong></p>
                            <p>Sunday: <strong>Closed</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form Panel -->
            <div class="contact-form-panel">
                <h3>Send Us a Message</h3>
                <p class="form-subtitle">Fill out the quick form below and our team will get back to you promptly.</p>

                @if (session('success'))
                    <div class="alert-success-box">
                        <span>âœ…</span>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" id="main-contact-form">
                    @csrf
                    <div class="c-form-row">
                        <div class="c-form-group">
                            <label for="c-name">Full Name *</label>
                            <input type="text" id="c-name" name="name" placeholder="e.g. Saniya Khan" required value="{{ old('name') }}">
                            @error('name')
                                <span style="color: #e53e3e; font-size: 11px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="c-form-group">
                            <label for="c-email">Email Address *</label>
                            <input type="email" id="c-email" name="email" placeholder="e.g. you@example.com" required value="{{ old('email') }}">
                            @error('email')
                                <span style="color: #e53e3e; font-size: 11px;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="c-form-group">
                        <label for="c-subject">Subject *</label>
                        <input type="text" id="c-subject" name="subject" placeholder="What is your inquiry about?" required value="{{ old('subject') }}">
                        @error('subject')
                            <span style="color: #e53e3e; font-size: 11px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="c-form-group">
                        <label for="c-message">Your Message *</label>
                        <textarea id="c-message" name="message" placeholder="Please write your detailed question or feedback..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <span style="color: #e53e3e; font-size: 11px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="c-submit-btn">
                        <span>Send Message</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <div class="section-header">
                <h2>Frequently Asked Questions</h2>
                <p>Quick answers to common questions about BookMyBeauty</p>
            </div>

            <div class="faq-grid">
                <div class="faq-card">
                    <h4><span>Q.</span> How do I book an appointment with a beautician?</h4>
                    <p>Simply navigate to <strong>Browse Beauticians</strong>, select your preferred artist, choose the service and slot you want, and confirm your booking instantly.</p>
                </div>

                <div class="faq-card">
                    <h4><span>Q.</span> How do beauticians register on BookMyBeauty?</h4>
                    <p>Click on <strong>Register</strong> in the top menu, select "Join as Beautician", complete your OTP phone verification, and set up your portfolio and services.</p>
                </div>

                <div class="faq-card">
                    <h4><span>Q.</span> Can I cancel or reschedule my appointment?</h4>
                    <p>Yes, logged-in clients can view and manage upcoming appointments anytime from their <strong>My Bookings</strong> dashboard.</p>
                </div>

                <div class="faq-card">
                    <h4><span>Q.</span> What is Bella and how does it help me?</h4>
                    <p>Bella is our smart Gemini-powered AI salon assistant that offers instant beauty recommendations, service pricing comparisons, and hairstyle advice 24/7.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
