@extends('layouts.beautician')

@section('title', 'Working Schedule - Beautician Studio')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Working schedule</h1>
            <p class="page-head__sub">Customers can only book inside the hours you set here.</p>
        </div>
    </div>

    <div class="admin-grid admin-grid--even">
        <section class="panel">
            <div class="panel__head">
                <h2 class="panel__title">Set working hours</h2>
            </div>

            <div class="panel__body">
                <form action="{{ route('beautician.availability.store') }}" method="POST">
                    @csrf

                    <div class="field">
                        <label>Working days</label>
                        <div class="day-picker">
                            @foreach ($daysOfWeek as $day)
                                <label class="day-picker__option">
                                    <input type="checkbox" name="days[]" value="{{ $day }}">
                                    <span>{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid--2" style="gap: 14px;">
                        <div class="field">
                            <label for="start_time">Start time</label>
                            <input type="time" id="start_time" name="start_time" required value="09:00">
                        </div>

                        <div class="field">
                            <label for="end_time">End time</label>
                            <input type="time" id="end_time" name="end_time" required value="18:00">
                        </div>
                    </div>

                    <button type="submit" class="btn btn--accent btn--block">Save these slots</button>
                </form>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Active weekly schedule</h2>
                    <p class="panel__meta">{{ $availabilitySlots->count() }} day{{ $availabilitySlots->count() === 1 ? '' : 's' }} open</p>
                </div>
            </div>

            <div class="panel__body panel__body--flush">
                @if ($availabilitySlots->isEmpty())
                    <div class="empty">
                        <strong>No availability yet</strong>
                        Pick your working days and hours so customers can book you.
                    </div>
                @else
                    <ul class="rows">
                        @foreach ($availabilitySlots as $slot)
                            <li>
                                <span class="icon-tile icon-tile--sm"><x-icon name="clock" :size="16" /></span>

                                <div class="rows__main">
                                    <div class="rows__title">{{ $slot->day_of_week }}</div>
                                    <div class="rows__meta">
                                        {{ date('g:i A', strtotime($slot->start_time)) }} to
                                        {{ date('g:i A', strtotime($slot->end_time)) }}
                                    </div>
                                </div>

                                <form action="{{ route('beautician.availability.destroy', $slot->id) }}" method="POST"
                                    onsubmit="return confirm('Remove {{ $slot->day_of_week }} from your schedule?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn--danger">Remove</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </div>
@endsection