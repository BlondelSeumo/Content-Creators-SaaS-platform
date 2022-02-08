<meta charset="utf-8">

{{-- Page title --}}
@hasSection('page_title')
    <title>@yield('page_title') - {{getSetting('site.name')}} </title>
@else
    <title>{{getSetting('site.name')}} -  {{getSetting('site.slogan')}}</title>
@endif

{{-- Generic Meta tags --}}
@hasSection('page_description')
    <meta name="description" content="@yield('page_description')">
@endif

{{-- Mobile tab color --}}
<meta name="theme-color" content="#505050">
<meta name="color-scheme" content="dark light">

{{-- Facebook share section --}}
<meta property="og:url"           content="@yield('share_url')" />
<meta property="og:type"          content="@yield('share_type')" />
<meta property="og:title"         content="@yield('share_title')" />
<meta property="og:description"   content="@yield('share_description')" />
<meta property="og:image"         content="@yield('share_img')" />

{{-- Twitter share section --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@yield('share_url')">
<meta name="twitter:creator" content="@yield('author')">
<meta name="twitter:title" content="@yield('share_title')">
<meta name="twitter:description" content="@yield('share_description')">
<meta name="twitter:image" content="@yield('share_img')">

{{-- CSRF Baby --}}
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

@yield('meta')

@if(getSetting('site.allow_pwa_installs'))
    @laravelPWA
@endif

<script src="{{asset('libs/pusher-js/dist/web/pusher.min.js')}}"></script>

{{-- Favicon --}}
<link rel="shortcut icon" href="{{ getSetting('site.favicon') }}" type="image/x-icon">

{{-- (Preloading) Fonts --}}
<link href="https://fonts.googleapis.com/css?family=Roboto:400,300" rel="preload" as="style">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,500,600,700" rel="preload" as="style">
{{-- Global CSS Assets --}}
{!!
    Minify::stylesheet(
        array_merge([
            '/libs/cookieconsent/build/cookieconsent.min.css',
            '/css/theme/bootstrap'.
            (Cookie::get('app_rtl') == null ? (getSetting('site.default_site_direction') == 'rtl' ? '.rtl' : '') : (Cookie::get('app_rtl') == 'rtl' ? '.rtl' : '')).
            (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '.dark' : '') : (Cookie::get('app_theme') == 'dark' ? '.dark' : '')).
            '.css',
            '/css/app.css',
         ],
         (isset($additionalCss) ? $additionalCss : [])
         ))->withFullUrl()
!!}

{{-- Page specific CSS --}}
@yield('styles')

@if(getSetting('site.custom_css'))
    <style>
        {!! getSetting('site.custom_css') !!}
    </style>
@endif
