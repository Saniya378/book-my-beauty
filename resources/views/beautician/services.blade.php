@extends('layouts.beautician')

@section('title', 'My Services - Beautician Studio')

@section('content')
<div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 25px 30px; border-radius: var(--border-radius); margin-bottom: 25px; border-bottom: 3px solid var(--accent); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">My Services & Pricing</h2>
        <p style="font-size: 13px; opacity: 0.85; margin: 0;">Add new salon treatments or customize duration and prices.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
    <!-- Add Service Form -->
    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin-bottom: 18px;">+ Add New Service</h3>

        <form action="{{ route('beautician.services.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label for="service_name" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Service Name</label>
                <input type="text" id="service_name" name="service_name" required placeholder="e.g. Bridal Makeup" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; outline: none;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="price" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Price (Rs.)</label>
                <input type="number" id="price" name="price" required min="0" step="50" placeholder="e.g. 3500" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; outline: none;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="duration" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Duration (Minutes)</label>
                <input type="number" id="duration" name="duration" required min="5" step="5" placeholder="e.g. 60" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Description (Optional)</label>
                <textarea id="description" name="description" rows="3" placeholder="Details about products used or preparation steps..." style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; outline: none;"></textarea>
            </div>

            <button type="submit" class="btn" style="width: 100%; padding: 11px; font-size: 13px; font-weight: 600;">Save Service</button>
        </form>
    </div>

    <!-- Existing Services Table -->
    <div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
        <div style="padding: 20px 25px; border-bottom: 1px solid #f0f0f0;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin: 0;">Listed Services ({{ $services->count() }})</h3>
        </div>

        @if($services->isEmpty())
            <div style="padding: 40px; text-align: center; color: var(--text-gray); font-size: 13px;">
                No services added yet. Use the form on the left to create your first service!
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #fafafa; border-bottom: 1px solid #eee; text-align: left; color: var(--text-gray); font-size: 11px; text-transform: uppercase;">
                        <th style="padding: 12px 18px;">Service Name</th>
                        <th style="padding: 12px 18px;">Price</th>
                        <th style="padding: 12px 18px;">Duration</th>
                        <th style="padding: 12px 18px;">Status</th>
                        <th style="padding: 12px 18px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $s)
                    <tr style="border-bottom: 1px solid #f5f5f5;">
                        <td style="padding: 14px 18px;">
                            <strong>{{ $s->service_name }}</strong>
                            @if(!empty($s->description))
                                <div style="font-size: 11px; color: var(--text-gray); margin-top: 2px;">{{ $s->description }}</div>
                            @endif
                        </td>
                        <td style="padding: 14px 18px; color: #2e7d32; font-weight: 600;">Rs. {{ number_format($s->price, 0) }}</td>
                        <td style="padding: 14px 18px;">{{ $s->duration }} mins</td>
                        <td style="padding: 14px 18px;">
                            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 10px;
                                {{ $s->is_active ? 'background:#e8f5e9; color:#2e7d32;' : 'background:#ffebee; color:#c62828;' }}">
                                {{ $s->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td style="padding: 14px 18px;">
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <form action="{{ route('beautician.services.toggle', $s->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background: none; border: 1px solid #ddd; padding: 4px 8px; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                        {{ $s->is_active ? 'Hide' : 'Activate' }}
                                    </button>
                                </form>

                                <form action="{{ route('beautician.services.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Delete this service permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #c62828; font-size: 14px; cursor: pointer;" title="Delete Service">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
