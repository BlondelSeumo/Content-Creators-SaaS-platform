@extends('layouts.user-no-nav')

@section('page_title', __('Messenger'))

@section('styles')
    {!!
        Minify::stylesheet([
            '/libs/selectize/dist/css/selectize.css',
            '/libs/selectize/dist/css/selectize.bootstrap3.css',
            '/libs/dropzone/dist/dropzone.css',
            '/libs/photoswipe/dist/photoswipe.css',
            '/libs/photoswipe/dist/default-skin/default-skin.css',
            '/css/pages/messenger.css',
            '/css/pages/checkout.css'
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/js/pages/messenger.js',
            '/libs/selectize/dist/js/standalone/selectize.min.js',
            '/libs/dropzone/dist/dropzone.js',
            '/js/FileUpload.js',
            '/js/plugins/media/photoswipe.js',
            '/libs/photoswipe/dist/photoswipe-ui-default.min.js',
            '/js/plugins/media/mediaswipe.js',
            '/js/plugins/media/mediaswipe-loader.js',
            '/js/pages/lists.js',
            '/js/pages/checkout.js',
            '/libs/pusher-js-auth/lib/pusher-auth.js'
         ])->withFullUrl()
    !!}
@stop

@section('content')
    @include('elements.uploaded-file-preview-template')
    @include('elements.photoswipe-container')
    @include('elements.report-user-or-post',['reportStatuses' => ListsHelper::getReportTypes()])
    @include('elements.feed.post-delete-dialog')
    @include('elements.feed.post-list-management')
    @include('template.checkout')
    <div class="row">

        <div class="min-vh-100 col-12">
            <div class="container messenger min-vh-100">
                <div class="row min-vh-100">
                    <div class="col-3 col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-2 border border-right-0 border-left-0 rounded-left conversations-wrapper min-vh-100 overflow-hidden border-top ">
                        <div class="d-flex justify-content-center justify-content-md-between pt-3 pr-1 pb-2">
                            <h5 class="d-none d-md-block text-truncate pl-3 pl-md-0 text-bold {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{__('Contacts')}}</h5>
                            <span data-toggle="tooltip" title="" class="pointer-cursor " data-original-title="{{trans_choice('Send a new message',['user' => 0])}}">
                                <a data-toggle="modal" data-target="#messageModal" title="" class="pointer-cursor" data-original-title="{{trans_choice('Send a new message',['user' => 0])}}">  <div class="mt-0 h5">@include('elements.icon',['icon'=>'create-outline','variant'=>'medium']) </div> </a>
                            </span>
                        </div>
                        <div class="conversations-list">
                            @if($lastContactID == false)
                                <div class="d-flex mt-3 mt-md-2 pl-3 pl-md-0 mb-3 pl-md-0"><span>{{__('Click the text bubble to send a new message.')}}</span></div>
                            @else
                                @include('elements.preloading.messenger-contact-box', ['limit'=>3])
                            @endif
                        </div>
                    </div>

                    <div class="col-9 col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-10 border conversation-wrapper rounded-right p-0 d-flex flex-column min-vh-100">
                        @include('elements.messenger.messenger-conversation-header')
                        @include('elements.preloading.messenger-conversation-header-box')
                        @include('elements.preloading.messenger-conversation-box')
                        <div class="conversation-content pt-4 pb-1 px-3 flex-fill">
                            @if(isset($lastContactID) && $lastContactID == false)
                                <div class="d-flex h-100 align-items-center justify-content-center">
                                    <div class="d-flex"><span>👋 {{__('You got no messages yet.')}} </span><span class="d-none d-md-block d-lg-block d-xl-block">&nbsp;{{__("Say 'Hi!' to someone!")}}</span></div>
                                </div>
                            @endif
                        </div>
                        <div class="dropzone-previews dropzone w-100 ppl-0 pr-0 pt-1 pb-1"></div>
                        <div class="conversation-writeup pt-1 pb-1 d-flex align-items-center mb-1 {{!$lastContactID ? 'hidden' : ''}}">
                            <form class="message-form w-100 pl-3">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="receiverID" id="receiverID" value="">
                                <textarea name="message" class="form-control messageBoxInput dropzone" placeholder="{{__('Write a message..')}}" onkeyup="messenger.textAreaAdjust(this)"></textarea>
                                {{--                                    <span class="invalid-feedback pl-4 text-bold" role="alert">Please enter a message</span>--}}
                            </form>
                            <div class="messenger-buttons-wrapper d-flex">
                                <button class="btn btn-outline-primary btn-rounded-icon messenger-button attach-file mx-2 file-upload-button">
                                    <div class="d-flex justify-content-center align-items-center">
                                        @include('elements.icon',['icon'=>'document','variant'=>''])
                                    </div>
                                </button>
                                <button class="btn btn-outline-primary btn-rounded-icon messenger-button send-message mr-2" onClick="messenger.sendMessage()">
                                    <div class="d-flex justify-content-center align-items-center">
                                        @include('elements.icon',['icon'=>'paper-plane','variant'=>''])
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('elements.messenger.send-user-message')
@stop
