@extends('layouts.beautician')

@section('title', 'Working Schedule - Beautician Studio')

@section('content')
<div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 25px 30px; border-radius: var(--border-radius); margin-bottom: 25px; border-bottom: 3px solid var(--accent);">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">Weekly Availability Schedule</h2>
    <p style="font-size: 13px; opacity: 0.85; margin: 0;">Set the working days and hours when clients can book appointments with you.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
    <!-- Add Schedule Form -->
    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin-bottom: 18px;">+ Set Working Hours</h3>

        <form action="{{ route('beautician.availability.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Select Working Days</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 13px;">
                    @foreach($daysOfWeek as $day)
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 6px 10px; background: #fafafa; border: 1px solid #eee; border-radius: 6px;">
                        <input type="checkbox" name="days[]" value="{{ $day }}" style="accent-color: var(--primary);">
                        <span>{{ $day }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 22px;">
                <div>
                    <label for="start_time" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Start Time</label>
                    <input type="time" id="start_time" name="start_time" required value="09:00" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px;">
                </div>
                <div>
                    <label for="end_time" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">End Time</label>
                    <input type="time" id="end_time" name="end_time" required value="18:00" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px;">
                </div>
            </div>

            <button type="submit" class="btn" style="width: 100%; padding: 11px; font-size: 13px; font-weight: 600;">Save Schedule Slots</button>
        </form>
    </div>

    <!-- Active Schedule Slots -->
    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin-bottom: 18px;">Active Weekly Schedule</h3>

        @if($availabilitySlots->isEmpty())
            <div style="padding: 30px 10px; text-align: center; color: var(--text-gray); font-size: 13px;">
                No availability set yet. Choose days and times on the left so customers can book!
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($availabilitySlots as $slot)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #fafafa; border: 1px solid #eee; border-radius: 8px;">
                    <div>
                        <strong style="color: var(--primary); font-size: 14px;">{{ $slot->day_of_week }}</strong>
                        <div style="font-size: 12px; color: var(--text-gray); margin-top: 2px;">
                            🕒 {{ date('h:i A', strtotime($slot->start_time)) }} — {{ date('h:i A', strtotime($slot->end_time)) }}
                        </div>
                    </div>

                    <form action="{{ route('beautician.availability.destroy', $slot->id) }}" method="POST" onsubmit="return confirm('Remove {{ $slot->day_of_week }} from your schedule?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; padding: 5px 12px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600;">
                            Remove
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
