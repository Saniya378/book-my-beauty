@extends('layouts.customer')

@section('title', 'Book Appointment with ' . $beautician->user->name)

@section('content')
<div style="max-width: 800px; margin: 20px auto;">
    <div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); padding: 25px 30px; color: white; display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid var(--accent);">
            <div>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">Book Appointment</h2>
                <p style="font-size: 13px; opacity: 0.85; margin: 0;">Beautician: <strong>{{ $beautician->user->name }}</strong> ({{ $beautician->location ?: 'Location confirmed upon booking' }})</p>
            </div>
            <a href="{{ route('beautician.view', $beautician->id) }}" style="color: var(--accent); font-size: 12px; text-decoration: none;">&larr; View Profile</a>
        </div>

        <div style="padding: 30px;">
            <!-- Working Hours Schedule -->
            <div style="background: #faf7f2; border: 1px solid #ebdcc5; border-radius: 8px; padding: 16px 20px; margin-bottom: 25px;">
                <h4 style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #855b25; margin-bottom: 10px; font-weight: 700;">
                    📅 Beautician's Working Schedule
                </h4>
                <div style="display: flex; flex-wrap: wrap; gap: 10px; font-size: 12px;">
                    @forelse($daysOfWeek as $day)
                        @if(isset($availabilityByDay[$day]))
                            <span style="background: white; border: 1px solid #ddd; padding: 4px 10px; border-radius: 4px; color: #333;">
                                <strong>{{ $day }}:</strong> {{ date('h:i A', strtotime($availabilityByDay[$day]->start_time)) }} - {{ date('h:i A', strtotime($availabilityByDay[$day]->end_time)) }}
                            </span>
                        @endif
                    @empty
                        <span style="color: #999;">Flexible scheduling. Contact directly or select an available slot.</span>
                    @endforelse
                </div>
            </div>

            <form action="{{ route('customer.book.store', $beautician->id) }}" method="POST">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label for="service_id" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Select Service</label>
                    <select id="service_id" name="service_id" required style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 14px;">
                        <option value="">-- Choose a Service --</option>
                        @foreach($beautician->activeServices as $s)
                            <option value="{{ $s->id }}" {{ request('service_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->service_name }} — Rs. {{ number_format($s->price, 0) }} ({{ $s->duration }} mins)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label for="booking_date" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Appointment Date</label>
                        <input type="date" id="booking_date" name="booking_date" min="{{ date('Y-m-d') }}" required value="{{ old('booking_date', date('Y-m-d')) }}" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 14px;">
                    </div>

                    <div>
                        <label for="booking_time" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Appointment Time</label>
                        <input type="time" id="booking_time" name="booking_time" required value="{{ old('booking_time', '11:00') }}" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 14px;">
                    </div>
                </div>

                <div style="background: #fdfdfd; border-left: 3px solid #1a1a2e; padding: 14px 18px; border-radius: 6px; font-size: 12px; color: #666; margin-bottom: 25px; line-height: 1.5;">
                    ℹ️ <strong>Cancellation Policy:</strong> You may cancel this appointment without fee within 2 hours of booking from your <em>My Bookings</em> dashboard. Payment is made in person at your appointment.
                </div>

                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="{{ route('beautician.view', $beautician->id) }}" class="btn btn-outline" style="padding: 12px 24px; text-decoration: none;">Cancel</a>
                    <button type="submit" class="btn" style="padding: 12px 30px; font-size: 14px; font-weight: 600;">Confirm Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
