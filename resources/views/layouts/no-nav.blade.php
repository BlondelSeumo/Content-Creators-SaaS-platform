<!doctype html>
<html class="h-100" dir="{{Cookie::get('app_rtl') == 'rtl' ? 'rtl' : 'ltr'}}" lang="{{session('locale')}}">
<head>
    @include('template.head')
</head>
<body class="d-flex flex-column">
<div class="flex-fill">
    @yield('content')
</div>
@include('template.footer-compact',['compact'=>true])
@include('template.jsVars')
@include('template.jsAssets')
</body>
</html>
