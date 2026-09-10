@extends('layouts.customer')

@section('title', 'Review ' . $beautician->user->name)

@section('styles')
<style>
    .star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        gap: 6px;
        font-size: 28px;
        cursor: pointer;
    }
    .star-rating input { display: none; }
    .star-rating label { color: #ddd; transition: color 0.2s; }
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #f59e0b;
    }
</style>
@endsection

@section('content')
<div style="max-width: 750px; margin: 20px auto;">
    <div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden; margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); padding: 25px 30px; color: white; display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid var(--accent);">
            <div>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">Rate & Review</h2>
                <p style="font-size: 13px; opacity: 0.85; margin: 0;">Beautician: <strong>{{ $beautician->user->name }}</strong></p>
            </div>
            <a href="{{ route('beautician.view', $beautician->id) }}" style="color: var(--accent); font-size: 12px; text-decoration: none;">&larr; View Profile</a>
        </div>

        <div style="padding: 30px;">
            @if($existingReview)
                <div style="background: #e8f5e9; border-left: 4px solid #2e7d32; padding: 16px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="color: #2e7d32; margin-bottom: 6px; font-size: 14px;">You have already reviewed this beautician</h4>
                    <div style="color: #f59e0b; font-size: 18px; margin-bottom: 8px;">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= $existingReview->rating ? '★' : '☆' }}
                        @endfor
                    </div>
                    <p style="font-size: 13px; color: #333; margin: 0;">"{{ $existingReview->comment }}"</p>
                </div>
            @else
                <form action="{{ route('customer.review.store', $beautician->id) }}" method="POST">
                    @csrf

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Your Rating</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="5 stars">★</label>
                            <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 stars">★</label>
                            <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 stars">★</label>
                            <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 stars">★</label>
                            <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 star">★</label>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label for="comment" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Your Comments & Feedback</label>
                        <textarea id="comment" name="comment" rows="4" required placeholder="Share details of your experience, styling quality, punctuality..." style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 13px; outline: none;"></textarea>
                    </div>

                    <button type="submit" class="btn" style="padding: 12px 30px; font-size: 14px; font-weight: 600;">Submit Review</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Prior Reviews List -->
    <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--primary); margin-bottom: 15px;">Customer Reviews ({{ $beautician->reviews->count() }})</h3>
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @forelse($beautician->reviews as $r)
        <div style="background: white; padding: 18px 22px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <strong>{{ $r->customer->name ?? 'Verified Client' }}</strong>
                <div style="color: #f59e0b; font-size: 14px;">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= $r->rating ? '★' : '☆' }}
                    @endfor
                </div>
            </div>
            <p style="font-size: 13px; color: #444; line-height: 1.5; margin: 0 0 6px;">{{ $r->comment }}</p>
            <span style="font-size: 11px; color: var(--text-gray);">{{ $r->created_at ? $r->created_at->format('d M Y') : '' }}</span>
        </div>
        @empty
        <div style="background: white; padding: 25px; text-align: center; border-radius: 8px; color: #888; font-size: 13px;">
            No other reviews yet.
        </div>
        @endforelse
    </div>
</div>
@endsection
