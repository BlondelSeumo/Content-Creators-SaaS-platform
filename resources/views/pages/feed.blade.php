@extends('layouts.user-no-nav')
@section('page_title', __('Your feed'))

{{-- Page specific CSS --}}
@section('styles')
    {!!
        Minify::stylesheet([
            '/libs/swiper/swiper-bundle.min.css',
            '/libs/photoswipe/dist/photoswipe.css',
            '/css/pages/checkout.css',
            '/libs/photoswipe/dist/default-skin/default-skin.css',
            '/css/pages/feed.css',
             '/css/posts/post.css',
             '/css/pages/search.css',
         ])->withFullUrl()
    !!}
@stop

{{-- Page specific JS --}}
@section('scripts')
    {!!
        Minify::javascript([
            '/js/PostsPaginator.js',
            '/js/CommentsPaginator.js',
            '/js/Post.js',
            '/js/SuggestionsSlider.js',
            '/js/pages/lists.js',
            '/js/pages/feed.js',
            '/js/pages/checkout.js',
            '/libs/swiper/swiper-bundle.min.js',
            '/js/plugins/media/photoswipe.js',
            '/libs/photoswipe/dist/photoswipe-ui-default.min.js',
            '/libs/@joeattardi/emoji-button/dist/index.js',
            '/js/plugins/media/mediaswipe.js',
            '/js/plugins/media/mediaswipe-loader.js',
         ])->withFullUrl()
    !!}
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-8 col-md-7 second p-0 ">
                <div class="d-flex d-md-none px-3 py-3 feed-mobile-search neutral-bg fixed-top-m">
                    @include('elements.search-box')
                </div>
                <div class="m-pt-70">
                    @include('elements.message-alert')
                    @include('elements.feed.posts-load-more')
                    <div class="feed-box mt-0 pt-4 posts-wrapper">
                        @include('elements.feed.posts-wrapper',['posts'=>$posts])
                    </div>
                    @include('elements.feed.posts-loading-spinner')
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-5 col-lg-4 first border-left order-0 pt-4 pb-5 min-vh-100 suggestions-wrapper d-none d-md-block">

                <div class="feed-widgets">
                    <div class="mb-4">
                        @include('elements.search-box')
                    </div>
                    @include('elements.feed.suggestions-box',['profiles'=>$suggestions])
                    @if(getSetting('ad-spaces.sidebar_ad_spot'))
                        <div class="d-flex justify-content-center align-items-center mt-4">
                            {!! getSetting('ad-spaces.sidebar_ad_spot') !!}
                        </div>
                    @endif
                </div>

            </div>
        </div>
        @include('template.checkout')
    </div>

@stop
