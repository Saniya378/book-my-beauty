@props(['name', 'size' => 18])

@php
    /*
     * Single icon set for the whole site. Every pictographic emoji used anywhere
     * in the blades is replaced with one of these line icons so the look stays
     * consistent instead of depending on whatever font the device uses.
     */
    $icons = [
        'arrow-left' => '<path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path>',
        'arrow-right' => '<path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path>',
        'bell' => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path>',
        'bookings' => '<rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M8 2v4"></path><path d="M16 2v4"></path><path d="M3 10h18"></path>',
        'cancelled' => '<circle cx="12" cy="12" r="9"></circle><path d="M15 9l-6 6"></path><path d="M9 9l6 6"></path>',
        'camera' => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle>',
        'chart' => '<path d="M4 20V10"></path><path d="M10 20V4"></path><path d="M16 20v-7"></path><path d="M22 20H2"></path>',
        'check' => '<path d="M20 6L9 17l-5-5"></path>',
        'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4L12 14.01l-3-3"></path>',
        'clock' => '<circle cx="12" cy="12" r="9"></circle><path d="M12 7.5V12l3 2"></path>',
        'close' => '<path d="M18 6L6 18"></path><path d="M6 6l12 12"></path>',
        'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="M7 10l5 5 5-5"></path><path d="M12 15V3"></path>',
        'edit' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"></path>',
        'eye' => '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle>',
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1L12 21l7.7-7.6 1.1-1a5.5 5.5 0 0 0 0-7.8z"></path>',
        'info' => '<circle cx="12" cy="12" r="9"></circle><path d="M12 16v-5"></path><path d="M12 8h.01"></path>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path>',
        'mail' => '<path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"></path><path d="M22 6l-10 7L2 6"></path>',
        'map-pin' => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>',
        'menu' => '<path d="M3 12h18"></path><path d="M3 6h18"></path><path d="M3 18h18"></path>',
        'message' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
        'mic' => '<rect x="9" y="2" width="6" height="12" rx="3"></rect><path d="M19 10a7 7 0 0 1-14 0"></path><path d="M12 18v4"></path>',
        'paper' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><path d="M8 13h8"></path><path d="M8 17h5"></path>',
        'pending' => '<circle cx="12" cy="12" r="9"></circle><path d="M12 7.5V12l3 2"></path>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2 4.2 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.1a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"></path>',
        'plus' => '<path d="M12 5v14"></path><path d="M5 12h14"></path>',
        'revenue' => '<rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20"></path><path d="M6 15h4"></path>',
        'review' => '<path d="M12 3.5l2.6 5.3 5.9.9-4.25 4.15 1 5.85L12 17l-5.25 2.75 1-5.85L3.5 9.7l5.9-.9z"></path>',
        'scissors' => '<circle cx="6" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M20 4L8.12 15.88"></path><path d="M14.47 14.48L20 20"></path><path d="M8.12 8.12L12 12"></path>',
        'search' => '<circle cx="11" cy="11" r="7"></circle><path d="M20.5 20.5l-4.6-4.6"></path>',
        'send' => '<path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4z"></path>',
        'services' => '<circle cx="6" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M20 4L8.12 15.88"></path><path d="M14.47 14.48L20 20"></path><path d="M8.12 8.12L12 12"></path>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path>',
        'sparkle' => '<path d="M12 3l1.9 4.6L18.5 9.5 13.9 11.4 12 16l-1.9-4.6L5.5 9.5l4.6-1.9z"></path><path d="M19 15l.9 2.1 2.1.9-2.1.9-.9 2.1-.9-2.1-2.1-.9 2.1-.9z"></path>',
        'star' => '<path d="M12 3.5l2.6 5.3 5.9.9-4.25 4.15 1 5.85L12 17l-5.25 2.75 1-5.85L3.5 9.7l5.9-.9z"></path>',
        'target' => '<circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="1.5"></circle>',
        'trash' => '<path d="M3 6h18"></path><path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"></path><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path>',
        'trend' => '<path d="M22 7l-8.5 8.5-4-4L2 19"></path><path d="M16 7h6v6"></path>',
        'user' => '<circle cx="12" cy="8" r="4"></circle><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"></path>',
        'users' => '<path d="M16 20v-1.5a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4V20"></path><circle cx="9" cy="7" r="3.5"></circle><path d="M22 20v-1.5a4 4 0 0 0-3-3.87"></path><path d="M16.5 3.9a4 4 0 0 1 0 6.2"></path>',
        'warning' => '<path d="M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path>',
        'whatsapp' => '<path d="M21 11.5a8.4 8.4 0 0 1-12.6 7.3L3 20.5l1.8-5.2A8.5 8.5 0 1 1 21 11.5z"></path><path d="M8.9 8.4c.2-.5.4-.5.6-.5h.5c.2 0 .4 0 .6.5l.7 1.7c.1.2 0 .4-.1.6l-.4.5c-.1.2-.2.3 0 .6a7 7 0 0 0 3.2 2.8c.3.1.5 0 .6-.1l.5-.6c.2-.2.3-.2.6-.1l1.6.8c.3.1.4.3.4.5a1.6 1.6 0 0 1-1.1 1.6c-.6.2-1.4.2-3.3-.6a9.4 9.4 0 0 1-4-4c-.5-1-.6-1.6-.5-2.2z"></path>',
    ];
@endphp

<svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none"
    stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
    focusable="false">
    {!! $icons[$name] ?? '' !!}
</svg>