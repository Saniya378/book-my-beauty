@php
    $footerBadges = [
        ['icon' => 'shield', 'label' => 'Verified salons'],
        ['icon' => 'message', 'label' => 'Bella AI'],
        ['icon' => 'paper', 'label' => 'PDF receipts'],
    ];
@endphp

<footer class="footer">
    <div class="footer__inner">
        <div>
            <div class="footer__brand">BookMyBeauty</div>
            <p class="footer__text">
                Book verified beauticians and stylists near you, compare real reviews and manage every
                appointment from one place.
            </p>

            <div class="footer__badges">
                @foreach ($footerBadges as $badge)
                    <span class="meta meta--light">
                        <x-icon :name="$badge['icon']" :size="15" />
                        {{ $badge['label'] }}
                    </span>
                @endforeach
            </div>
        </div>

        <div>
            <div class="footer__label">Quick links</div>
            <ul class="footer__links">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('browse') }}">Browse beauticians</a></li>
                <li><a href="{{ route('about') }}">About us</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
                @guest
                    <li><a href="{{ route('login') }}">Sign in</a></li>
                    <li><a href="{{ route('register') }}">Join as beautician</a></li>
                @endguest
            </ul>
        </div>

        <div>
            <div class="footer__label">Help and project</div>
            <p class="footer__text" style="margin-top: 0;">
                Our assistant Bella is available on every page to answer questions about services,
                pricing and aftercare.
            </p>

            <div class="footer__badges" style="flex-direction: column; gap: 10px;">
                <span class="meta meta--light">
                    <x-icon name="map-pin" :size="15" />
                    Karakoram International University, Gilgit-Baltistan
                </span>
                <span class="meta meta--light">
                    <x-icon name="mail" :size="15" />
                    <a href="mailto:support@bookmybeauty.com">support@bookmybeauty.com</a>
                </span>
                <span class="meta meta--light">
                    <x-icon name="phone" :size="15" />
                    +92 300 123 4567
                </span>
            </div>
        </div>
    </div>

    <div class="footer__bottom">
        &copy; {{ date('Y') }} BookMyBeauty &middot; A KIU Computer Science final year project
    </div>
</footer>