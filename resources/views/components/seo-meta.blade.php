@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'keywords' => [],
    'canonical' => null,
    'schemaType' => 'SoftwareApplication',
    'schemaData' => []
])

@php
    $finalTitle = \App\Helpers\SeoHelper::title($title);
    $finalDescription = \App\Helpers\SeoHelper::description($description);
    $finalCanonical = \App\Helpers\SeoHelper::canonical($canonical);
    $finalImage = \App\Helpers\SeoHelper::image($image);
    $finalKeywords = \App\Helpers\SeoHelper::keywords($keywords);
    $googleVerification = config('seo.google_site_verification');
@endphp

<!-- Primary Meta Tags -->
<title>{{ $finalTitle }}</title>
<meta name="title" content="{{ $finalTitle }}">
<meta name="description" content="{{ $finalDescription }}">
<meta name="keywords" content="{{ $finalKeywords }}">
<meta name="author" content="{{ config('seo.default_author') }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ $finalCanonical }}">

<!-- Favicon & App Icons -->
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

<!-- Google Search Console Verification -->
@if(!empty($googleVerification))
<meta name="google-site-verification" content="{{ $googleVerification }}" />
@endif

<!-- Open Graph / Facebook / WhatsApp -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $finalCanonical }}">
<meta property="og:title" content="{{ $finalTitle }}">
<meta property="og:description" content="{{ $finalDescription }}">
<meta property="og:image" content="{{ $finalImage }}">
<meta property="og:site_name" content="{{ config('seo.site_name') }}">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $finalCanonical }}">
<meta name="twitter:title" content="{{ $finalTitle }}">
<meta name="twitter:description" content="{{ $finalDescription }}">
<meta name="twitter:image" content="{{ $finalImage }}">
@if(config('seo.twitter_handle'))
<meta name="twitter:site" content="{{ config('seo.twitter_handle') }}">
@endif

<!-- Schema.org JSON-LD Structured Data -->
{!! \App\Helpers\SeoHelper::schemaOrg($schemaType, $schemaData) !!}
