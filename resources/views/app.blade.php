@php
    $config = [
        'appName' => config('app.name'),
        'fallback_locale' => config('app.fallback_locale'),
        'locale' => app()->getLocale(),
        'locales' => config('app.locales')
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="default">

<head>
    {{--    <meta name="viewport" content="viewport-fit=cover, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">--}}
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no, minimum-scale=1.0, maximum-scale=1">
    <meta name="theme-color" content="#202046">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset='utf-8'>

    @if (request()->hasCookie('adminUserToken'))
        <meta name="admin_user_token" content="{{ request()->cookie('adminUserToken') }}">
        @php(\Cookie::queue(\Cookie::forget('adminUserToken')))
    @endif

    <title>{{ $meta['title'] }}</title>
    <meta property="og:url"                content="{{ request()->fullUrl() }}" />
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content="{{ $meta['title'] }}" />
    <meta property="og:description"        content="{{ $meta['description'] }}" />
    <meta property="og:image"              content="{{ $meta['image'] }}" />
    @if ($meta['image_alt'])
        <meta property="og:image:alt"              content="{{ $meta['image_alt'] }}" />
    @endif

    @if ($role == 'user')
        {{--        <link rel="manifest" href="/site.webmanifest" />--}}
        <meta name="msapplication-TileImage" content="/icons/logo-144x144.png">
        <link rel="apple-touch-icon-precomposed" href="/icons/logo-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/icons/logo-180x180.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/icons/logo-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/icons/logo-16x16.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="{{ config('env.APP_NAME') }}">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <script>
            if (navigator.serviceWorker.controller) {
                console.log("Active service worker found");
            } else {
                if (typeof navigator.serviceWorker !== 'undefined') {
                    navigator.serviceWorker.register('/service-worker.js')
                }
            }
        </script>
    @endif

    @if (isset($assetVersion))
        <meta name="v-asset-v" content="{{ $assetVersion }}" />
    @endif

    @if (isset($assetRevision))
        <meta name="v-rev-v" content="{{ $assetRevision }}" />
    @endif
</head>

<body class="{{ app()->getLocale() }} font-sans antialiased">
<script>
    window.config = @json($config)
</script>
<noscript>
    You need to enable JavaScript to run this app.
</noscript>
<div id="app" class="flex h-[100dvh] overflow-hidden"></div>
{!! $assets !!}
</body>

</html>
