@php
    // A favicon uploaded under "Cấu hình hệ thống" wins, so the admin can still
    // replace it without touching code. When it is empty we fall back to the
    // files generated from the site logo instead of emitting href="" — an empty
    // href makes the browser request the current page as the icon, which is why
    // no logo showed on the tab at all.
    $faviconOverride = trim((string) ($system['homepage_favicon'] ?? ''));
@endphp
@if ($faviconOverride !== '')
    <link rel="icon" href="{{ $faviconOverride }}">
    <link rel="apple-touch-icon" href="{{ $faviconOverride }}">
@else
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
@endif
