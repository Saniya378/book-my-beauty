@extends('layouts.beautician')

@section('title', 'Work Portfolio - Beautician Studio')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">My portfolio</h1>
            <p class="page-head__sub">Real client work helps customers choose the right artist.</p>
        </div>

        <div class="page-head__actions">
            <span class="pill pill--neutral">{{ $images->count() }} of 12 slots used</span>
        </div>
    </div>

    <section class="panel" style="margin-bottom: 22px;">
        <div class="panel__head">
            <h2 class="panel__title">Upload a photo</h2>
        </div>

        <div class="panel__body">
            <form action="{{ route('beautician.portfolio.store') }}" method="POST" enctype="multipart/form-data"
                class="grid grid--2" style="align-items: end; gap: 14px;">
                @csrf

                <div class="field">
                    <label for="portfolio_image">Photo (JPG or PNG, up to 3MB)</label>
                    <input type="file" id="portfolio_image" name="portfolio_image" required
                        accept="image/png, image/jpeg, image/jpg">
                </div>

                <div class="field">
                    <label for="caption">Caption or style name</label>
                    <input type="text" id="caption" name="caption" placeholder="e.g. Bridal hair with soft glow makeup">
                </div>

                <div style="grid-column: 1 / -1;">
                    <button type="submit" class="btn btn--accent">
                        <x-icon name="camera" :size="15" />
                        Upload photo
                    </button>
                </div>
            </form>
        </div>
    </section>

    @if ($images->isEmpty())
        <section class="panel">
            <div class="empty" style="padding: 55px 20px;">
                <span class="icon-tile" style="margin: 0 auto 16px;"><x-icon name="camera" :size="20" /></span>
                <strong>No photos yet</strong>
                <p style="margin: 6px 0 0;">Upload photos of your finished work so customers know what to expect.</p>
            </div>
        </section>
    @else
        <div class="grid grid--4">
            @foreach ($images as $image)
                <figure class="card shot" style="padding: 0; overflow: hidden; margin: 0;">
                    <img src="{{ $image->url }}" alt="{{ $image->caption }}"
                        style="width: 100%; height: 190px; object-fit: cover; display: block;">

                    <figcaption style="display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 11px 13px;">
                        <span style="font-size: 12px; color: #444; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $image->caption ?: 'Portfolio photo' }}
                        </span>

                        <form action="{{ route('beautician.portfolio.destroy', $image->id) }}" method="POST"
                            onsubmit="return confirm('Delete this photo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn action-btn--danger" title="Delete photo">
                                <x-icon name="trash" :size="13" />
                            </button>
                        </form>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    @endif
@endsection