<!doctype html>
<html class="h-100" dir="{{Cookie::get('app_rtl') == 'rtl' ? 'rtl' : 'ltr'}}" lang="{{session('locale')}}">
<head>
    <meta charset="utf-8">
    {{-- Page title --}}
    <title>@yield('page_title') - {{config('app.site.name')}} </title>
    {{-- Generic Meta tags --}}
    <meta name="description" content="{{__("Install the script")}}}">
    {{-- CSRF Baby --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('meta')
    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{  asset(config('app.site.favicon')) }}" type="image/x-icon">
    {{-- (Preloading) Fonts --}}
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300" rel="preload" as="style">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,500,600,700" rel="preload" as="style">
    {{-- Global CSS Assets --}}
    {!!
        Minify::stylesheet(
            [
                '/libs/cookieconsent/build/cookieconsent.min.css',
                '/css/theme/bootstrap.css',
                '/css/app.css',
             ]
             )->withFullUrl()
    !!}
    {{-- Page specific CSS --}}
    @yield('styles')
</head>
<body class="d-flex flex-column">

<div class="flex-fill">
    @yield('content')
</div>

{{-- Global JS Assets --}}
{!!
    Minify::javascript(
        [
        '/libs/jquery/dist/jquery.min.js',
        '/libs/popper.js/dist/umd/popper.min.js',
        '/libs/bootstrap/dist/js/bootstrap.min.js',
        '/js/plugins/toasts.js',
        '/libs/cookieconsent/build/cookieconsent.min.js',
        '/js/Installer.js',
        ]
    )->withFullUrl()
!!}

{{-- Page specific JS --}}
@yield('scripts')

<script type="module" src="{{asset('/libs/ionicons/dist/ionicons/ionicons.esm.js')}}"></script>
<script nomodule src="{{asset('/libs/ionicons/dist/ionicons/ionicons.js')}}"></script>

@include('elements.translations')

</body>
</html>
