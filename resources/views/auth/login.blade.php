@extends('layouts.no-nav')
@section('page_title', __('Login'))

@section('content')
    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="col-md-6">
                <div class="login d-flex align-items-center py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-7 col-xl-6 mx-auto">
                                <a href="{{action('HomeController@index')}}">
                                    <img class="brand-logo pb-4" src="{{asset( (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo')) : (Cookie::get('app_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo'))) )}}">
                                </a>
                                @include('auth.login-form')
                                @include('auth.social-login-box')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-none d-md-flex bg-image p-0 m-0">
                <div class="d-flex m-0 p-0 bg-gradient-primary w-100 h-100">
                    <img src="{{asset('/img/pattern-lines.svg')}}" alt="pattern-lines" class="img-fluid opacity-10">
                </div>
            </div>
        </div>
    </div>
@endsection
