@extends('layouts.customer')

@section('title', 'Book Appointment with ' . $beautician->user->name)

@section('content')
    <div style="max-width: 900px; margin: 0 auto;">
        <div class="banner">
            <div style="display: flex; align-items: center; gap: 18px;">
                <img src="{{ $beautician->photo_url }}" alt="{{ $beautician->user->name }}"
                    style="width: 58px; height: 58px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent);">

                <div>
                    <h1 class="banner__title">Book an appointment</h1>
                    <p class="banner__sub">
                        {{ $beautician->user->name }}
                        @if ($beautician->location)
                            &middot; {{ $beautician->location }}
                        @endif
                    </p>
                </div>
            </div>

            <a href="{{ route('beautician.view', $beautician->id) }}" class="btn btn--light">
                <x-icon name="arrow-left" :size="15" />
                View profile
            </a>
        </div>

        @php
            $openDays = collect($daysOfWeek)->filter(fn ($day) => isset($availabilityByDay[$day]));
        @endphp

        <section class="panel" style="margin-bottom: 20px;">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Working days and hours</h2>
                    <p class="panel__meta">Pick a date that matches one of the open days</p>
                </div>
            </div>

            <div class="panel__body">
                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(104px, 1fr)); gap: 10px;">
                    @foreach ($daysOfWeek as $day)
                        @php
                            $hours = $availabilityByDay[$day] ?? null;
                        @endphp

                        <div id="day-card-{{ $day }}" class="day-card {{ $hours ? 'is-open' : '' }}">
                            <div class="day-card__name">{{ Str::substr($day, 0, 3) }}</div>

                            @if ($hours)
                                <span class="pill pill--confirmed">Open</span>
                                <div class="day-card__hours">
                                    {{ date('g:i A', strtotime($hours->start_time)) }}<br>
                                    to<br>
                                    {{ date('g:i A', strtotime($hours->end_time)) }}
                                </div>
                            @else
                                <span class="pill pill--cancelled">Closed</span>
                                <div class="day-card__hours">Off day</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($openDays->isEmpty())
                    <div class="notice notice--warn" style="margin-top: 14px;">
                        <span class="notice__icon"><x-icon name="warning" :size="17" /></span>
                        <span>This beautician has not published custom hours yet, so standard salon hours apply.</span>
                    </div>
                @endif
            </div>
        </section>

        <div id="slot-feedback-box" class="notice" style="display: none; margin-bottom: 20px;"></div>

        <section class="panel">
            <div class="panel__body">
                <form action="{{ route('customer.book.store', $beautician->id) }}" method="POST">
                    @csrf

                    <div class="field">
                        <label for="service_id">1. Choose a service</label>
                        <select id="service_id" name="service_id" required>
                            <option value="">Select a service</option>
                            @foreach ($beautician->activeServices as $service)
                                <option value="{{ $service->id }}"
                                    {{ (old('service_id', request('service_id')) == $service->id) ? 'selected' : '' }}>
                                    {{ $service->service_name }} &mdash; Rs. {{ number_format($service->price, 0) }}
                                    ({{ $service->duration }} min)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid--2" style="gap: 16px;">
                        <div class="field">
                            <label for="booking_date">2. Appointment date</label>
                            <input type="date" id="booking_date" name="booking_date" min="{{ date('Y-m-d') }}" required
                                value="{{ old('booking_date', date('Y-m-d')) }}" onchange="checkDayAvailability()">
                        </div>

                        <div class="field">
                            <label for="booking_time">3. Time slot</label>
                            <input type="time" id="booking_time" name="booking_time" required
                                value="{{ old('booking_time', '11:00') }}">
                            <span id="time-hint" class="rows__meta"></span>
                        </div>
                    </div>

                    <div class="notice notice--info" style="margin-bottom: 22px;">
                        <span class="notice__icon"><x-icon name="info" :size="17" /></span>
                        <span>
                            <strong>Instant confirmation and PDF receipt.</strong>
                            You will get an in-app notification with a downloadable receipt, and can cancel free of
                            charge within two hours of booking.
                        </span>
                    </div>

                    <div style="display: flex; gap: 12px; justify-content: flex-end; flex-wrap: wrap;">
                        <a href="{{ route('beautician.view', $beautician->id) }}" class="btn btn--ghost">Cancel</a>
                        <button type="submit" class="btn btn--accent">
                            Confirm appointment
                            <x-icon name="arrow-right" :size="15" />
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    @php
        $availabilityForJs = collect($availabilityByDay)->mapWithKeys(fn ($hours, $day) => [
            $day => [
                'start' => $hours->start_time,
                'end' => $hours->end_time,
                'startFormatted' => date('g:i A', strtotime($hours->start_time)),
                'endFormatted' => date('g:i A', strtotime($hours->end_time)),
            ],
        ])->all();
    @endphp

    <script>
        const availabilityData = @json($availabilityForJs);

        const daysList = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        function checkDayAvailability() {
            const dateInput = document.getElementById('booking_date');
            const feedback = document.getElementById('slot-feedback-box');
            const timeInput = document.getElementById('booking_time');
            const timeHint = document.getElementById('time-hint');

            if (!dateInput.value) return;

            document.querySelectorAll('[id^="day-card-"]').forEach(card => card.classList.remove('is-picked'));

            const [year, month, day] = dateInput.value.split('-');
            const dayName = daysList[new Date(year, month - 1, day).getDay()];

            document.getElementById('day-card-' + dayName)?.classList.add('is-picked');

            const hours = availabilityData[dayName];
            feedback.style.display = 'flex';

            if (hours) {
                feedback.className = 'notice notice--ok';
                feedback.innerHTML = '<span class="notice__icon">' + icon('check-circle') + '</span><span><strong>' +
                    dayName + ' is open.</strong> Working hours are <strong>' + hours.startFormatted +
                    '</strong> to <strong>' + hours.endFormatted + '</strong>.</span>';

                timeInput.min = hours.start;
                timeInput.max = hours.end;
                timeHint.textContent = 'Choose a time between ' + hours.startFormatted + ' and ' + hours.endFormatted;
                return;
            }

            const openDays = Object.keys(availabilityData).join(', ');
            feedback.className = 'notice notice--bad';
            feedback.innerHTML = '<span class="notice__icon">' + icon('warning') + '</span><span><strong>Closed on ' +
                dayName + 's.</strong> Available days: <strong>' + (openDays || 'contact the beautician directly') +
                '</strong>.</span>';

            timeHint.textContent = 'The beautician is closed on ' + dayName + 's';
        }

        function icon(name) {
            const paths = {
                'check-circle': '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4L12 14.01l-3-3"></path>',
                warning: '<path d="M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path>'
            };

            return '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" ' +
                'stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' +
                (paths[name] || '') + '</svg>';
        }

        document.addEventListener('DOMContentLoaded', checkDayAvailability);
    </script>
@endsection