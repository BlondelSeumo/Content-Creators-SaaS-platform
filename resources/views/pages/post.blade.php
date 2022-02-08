@extends('layouts.user-no-nav')

@section('page_title', __(":username post",['username'=>$post->user->name]))

@section('styles')
    {!!
        Minify::stylesheet([
            '/css/posts/post.css',
            '/libs/swiper/swiper-bundle.min.css',
            '/libs/photoswipe/dist/photoswipe.css',
            '/css/pages/checkout.css',
            '/libs/photoswipe/dist/default-skin/default-skin.css',
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/libs/swiper/swiper-bundle.min.js',
            '/js/PostsPaginator.js',
            '/js/CommentsPaginator.js',
            '/js/Post.js',
            '/js/pages/lists.js',
            '/js/pages/checkout.js',
            '/js/plugins/media/photoswipe.js',
            '/libs/@joeattardi/emoji-button/dist/index.js',
            '/libs/photoswipe/dist/photoswipe-ui-default.min.js',
            '/js/plugins/media/mediaswipe.js',
            '/js/plugins/media/mediaswipe-loader.js',
            '/js/posts/view.js',
         ])->withFullUrl()
    !!}
@stop

@section('content')
    <div class="row">
        <div class="min-vh-100 col-12 col-md-8 border-right">
            <div class="feed-box mt-0 pt-4 mb-3 posts-wrapper">
                @include('elements/message-alert')
                @include('elements.feed.post-box')
            </div>
        </div>
        <div class="col-12 col-md-4 d-none d-md-block pt-3">
            @include('elements.profile.widgets')
        </div>
    </div>
    @include('elements.photoswipe-container')
    @include('elements.feed.post-delete-dialog')
    @include('template.checkout')
@stop
