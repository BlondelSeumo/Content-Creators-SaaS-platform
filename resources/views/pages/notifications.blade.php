@extends('layouts.user-no-nav')

@section('page_title', __('Notifications'))

@section('styles')
    {!!
        Minify::stylesheet([
            '/css/pages/notifications.css'
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/js/pages/notifications.js'
         ])->withFullUrl()
    !!}
@stop

@section('content')
    <div class="row">
        <div class="col-12 pr-0 min-vh-100 pt-4 border-right">
            <div class="px-3 pb-4 border-bottom">
                <h5 class="text-truncate text-bold mb-0 {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{__('Notifications')}}</h5>
            </div>
            <div class="mt-3 inline-border-tabs">
                <div class="d-none d-md-block">
                    @include('elements.notifications.notifications-menu',['variant' => 'desktop'])
                </div>
                <div class="notifications-menu-mobile d-block d-md-none mt-3">
                    @include('elements.notifications.notifications-menu',['variant' => 'mobile'])
                </div>
            </div>
            @include('elements.notifications.notifications-wrapper', ['notifications' => $notifications])
        </div>
    </div>
@stop
