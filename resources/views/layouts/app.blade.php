<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $siteSettings->get('site_name', 'Hotel Host'))</title>
    @if($siteSettings->get('site_favicon'))
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($siteSettings->get('site_favicon')) }}" type="image/x-icon">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    @yield('styles')
</head>
<body>
    @include('partials.header')
    
    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="{{ asset('js/global.js') }}"></script>
    @yield('scripts')
</body>
</html>
