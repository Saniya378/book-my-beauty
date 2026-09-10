@extends('layouts.beautician')

@section('title', 'Work Portfolio - Beautician Studio')

@section('content')
<div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 25px 30px; border-radius: var(--border-radius); margin-bottom: 25px; border-bottom: 3px solid var(--accent);">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">My Work Portfolio</h2>
    <p style="font-size: 13px; opacity: 0.85; margin: 0;">Showcase your finest hairstyles, makeup, nail art, and skincare results to clients.</p>
</div>

<!-- Upload Section -->
<div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 30px;">
    <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin-bottom: 15px;">Upload New Portfolio Photo ({{ $images->count() }}/12)</h3>

    <form action="{{ route('beautician.portfolio.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 2fr auto; gap: 15px; align-items: flex-end;">
            <div>
                <label for="portfolio_image" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Choose Image (Max 3MB, JPG/PNG)</label>
                <input type="file" id="portfolio_image" name="portfolio_image" required accept="image/png, image/jpeg, image/jpg" style="width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px;">
            </div>

            <div>
                <label for="caption" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Caption / Style Name</label>
                <input type="text" id="caption" name="caption" placeholder="e.g. Elegant Bridal Hair & Glow Makeup" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; outline: none;">
            </div>

            <button type="submit" class="btn" style="padding: 10px 22px; font-size: 13px; font-weight: 600; height: 42px;">Upload Image</button>
        </div>
    </form>
</div>

<!-- Portfolio Grid -->
@if($images->isEmpty())
    <div style="background: white; padding: 50px 20px; text-align: center; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <div style="font-size: 40px; margin-bottom: 12px;">📸</div>
        <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin-bottom: 6px;">No photos uploaded yet</h3>
        <p style="color: var(--text-gray); font-size: 13px;">Upload photos of your real client work above to help customers choose your salon!</p>
    </div>
@else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
        @foreach($images as $img)
        <div style="background: white; border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow); position: relative;">
            <img src="{{ $img->url }}" alt="{{ $img->caption }}" style="width: 100%; height: 200px; object-fit: cover; display: block;">

            <div style="padding: 12px 14px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 12px; color: #444; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;">
                    {{ $img->caption ?: 'Portfolio Photo' }}
                </span>

                <form action="{{ route('beautician.portfolio.destroy', $img->id) }}" method="POST" onsubmit="return confirm('Delete this image?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #c62828; cursor: pointer; font-size: 14px;" title="Delete">🗑</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
