@extends('layouts.install')
@section('page_title', __('Install the script'))
@section('content')
    <div class="container-fluid installer-bg">
        <div class="row no-gutter d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-4">
                <div class="d-flex justify-content-center pb-5">
                    <a href="{{route('installer.install')}}">
                        <img class="brand-logo" src="{{asset('/img/logo-black.png')}}">
                    </a>
                </div>
                <div class="col card shadow-sm">
                    <div class="card-body">
                        <div class="text-bold mb-2">
                                {{__("Mandatory requirements")}}
                        </div>
                        <div class="row mb-1">
                            <div class="col-8">{{__("PHP Version")}}: {{phpversion()}}</div>
                            <div class="col-4 d-flex justify-content-end">
                                @if(version_compare(phpversion(), '7.2.5') >= 0)
                                    @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium', 'classes'=>'text-success'])
                                @else
                                    @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'medium', 'classes'=>'text-warning'])
                                @endif
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-8">PHP allow_url_fopen</div>
                            <div class="col-4 d-flex justify-content-end">
                                @if(ini_get('allow_url_fopen'))
                                    @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium', 'classes'=>'text-success'])
                                @else
                                    @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'medium', 'classes'=>'text-danger'])
                                @endif
                            </div>
                        </div>
                        @foreach($requiredExtensions as $PHPExtension)
                            <div class="row mb-1">
                                <div class="col-8">{{$PHPExtension}}</div>
                                <div class="col-4 d-flex justify-content-end">
                                    @if(extension_loaded($PHPExtension))
                                        @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium', 'classes'=>'text-success'])
                                    @else
                                        @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'medium', 'classes'=>'text-danger'])
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <hr>
                        <div class="text-bold mb-2">
                            {{__("Optional requirements")}}
                        </div>
                        <div class="row mb-1">
                            <div class="col-8">FFMpeg</div>
                            <div class="col-4 d-flex justify-content-end">
                                <div data-toggle="tooltip" data-placement="top" title="{{__("FFMpeg paths are configured in admin panel. If not available, videos formats will fallback to mp4, uncompressed videos.")}}">
                                    @include('elements.icon',['icon'=>'information-circle-outline','variant'=>'medium', 'classes'=>'text-primary'])
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row-reverse">
                            @if($passesRequirements)
                                <a href="{{route('installer.install').'?step=2'}}" class="btn btn-primary mt-4">{{__("Next")}}</a>
                            @else
                                <span class="btn btn-primary mt-4 disabled">{{__("Next")}}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
