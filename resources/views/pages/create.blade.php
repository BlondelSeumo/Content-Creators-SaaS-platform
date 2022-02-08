@extends('layouts.user-no-nav')
@section('page_title', __('New post'))

@section('styles')
    {!!
        Minify::stylesheet([
            '/css/posts/post.css',
            '/libs/dropzone/dist/dropzone.css',
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/js/Post.js',
            '/js/posts/create-helper.js',
            (Route::currentRouteName() =='posts.create' ? '/js/posts/create.js' : '/js/posts/edit.js'),
            '/libs/dropzone/dist/dropzone.js',
            '/js/FileUpload.js',
         ])->withFullUrl()
    !!}
@stop

@section('content')

    <div class="row">
        <div class="col-12">
            @include('elements.uploaded-file-preview-template')
            @include('elements.post-price-setup',['postPrice'=>(isset($post) ? $post->price : 0)])
            @include('elements.post-save-confirmation')
            <div class="d-flex justify-content-between pt-4 pb-3 px-3 border-bottom">
                <h5 class="text-truncate text-bold  {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{Route::currentRouteName() == 'posts.create' ? __('New post') : __('Edit post')}}</h5>
            </div>
            <div class="pl-3 pr-3 pt-2">
                @if(!GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks'))
                    <div class="alert alert-warning text-white font-weight-bold mt-2 mb-0" role="alert">
                        {{__("Before being able to publish an item, you need to complete your")}} <a class="text-white" href="{{route('my.settings',['type'=>'verify'])}}">{{__("profile verification")}}</a>.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="d-flex flex-column-reverse">
                    <div class="w-100">
                        <textarea  id="dropzone-uploader" name="input-text" class="form-control border dropzone w-100" rows="3" spellcheck="false" placeholder="{{__('Write a new post, drag and drop files to upload files.')}}" value="{{isset($post) ? $post->text : ''}}"></textarea>
                        <span class="invalid-feedback" role="alert">
                 <strong>{{__('Your post must contain more than 10 characters.')}}</strong>
                </span>
                        <div class="d-flex justify-content-between w-100 mb-3 mt-3">
                            <div class="d-flex">
                                <div class="mt-1">
                            <span class="h-pill h-pill-primary rounded file-upload-button" data-toggle="tooltip" data-placement="right" title="{{__('Add attachments')}}" >
                                @include('elements.icon',['icon'=>'document-outline','variant'=>'medium','centered'=>true])
                            </span>
                                </div>
                                <div class="mt-1 ml-2">
                            <span class="h-pill h-pill-primary post-price-button" onclick="PostCreate.showSetPricePostDialog()">
                                @include('elements.icon',['icon'=>'logo-usd','variant'=>'medium','centered'=>true])
                                <span class="d-none d-md-block">{{__("Set post price")}}</span>
                                <span class="d-block d-md-none">{{__("Price")}}</span>
                                <span class="post-price-label ml-1">{{(isset($post) && $post) > 0 ? "(".config('app.site.currency_symbol')."$post->price".(config('app.site.currency_symbol') ? '' : config('app.site.currency_code')).")" : ''}}</span>
                            </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center">
                                @if(Route::currentRouteName() == 'posts.create')
                                    <div class="d-none d-md-block">
                                        <a href="#" class="draft-clear-button mr-1 mr-md-3">{{__('Clear draft')}}</a>
                                    </div>
                                @endif
                                @if(!GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks'))
                                    <button class="btn btn-outline-primary disabled mb-0">{{__('Save')}}</button>
                                @else
                                    <button class="btn btn-outline-primary post-create-button mb-0">{{__('Save')}}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="dropzone-previews dropzone w-100 ppl-0 pr-0 pt-1 pb-1"></div>
                </div>
            </div>

        </div>
    </div>

@stop
