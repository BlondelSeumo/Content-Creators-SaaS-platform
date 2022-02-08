<!doctype html>
<html class="h-100" dir="{{Cookie::get('app_rtl') == 'rtl' ? 'rtl' : 'ltr'}}" lang="{{session('locale')}}">
<head>
    @include('template.head')
</head>
<body class="d-flex flex-column">
@include('template.header')
<div class="flex-fill">
    @yield('content')
</div>
@include('template.footer')
@include('template.jsVars')
@include('template.jsAssets')
</body>
</html>
