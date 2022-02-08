@extends('layouts.install')
@section('page_title', __('Install the script'))
@section('scripts')
    {!!
        Minify::javascript([
            '/js/Installer.js',
         ])->withFullUrl()
    !!}
@stop
@section('content')
    <div class="container-fluid installer-bg">

        <div class="row no-gutter d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-4">
                <div class="d-flex justify-content-center pb-5">
                    <a href="{{route('installer.savedb')}}">
                        <img class="brand-logo" src="{{asset('/img/logo-black.png')}}">
                    </a>
                </div>
                <div class="col card shadow-sm">
                    <h4 class="card-title mt-4 ml-2">{{__('Database connection')}}</h4>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger text-white font-weight-bold mt-2" role="alert">
                                {{session('error')}}
                                <button type="button" class="close" data-dismiss="alert" aria-label="{{__('Close')}}">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('installer.savedb') }}">
                            @csrf
                            <div class="form-group ">
                                <label for="db_host" class="col-form-label">{{ __('Database host') }}</label>
                                <div class="">
                                    <input id="db_host" type="db_host" class="form-control @error('db_host') is-invalid @enderror"  name="db_host" value="{{ old('db_host') ? old('db_host') : (session('db_host') ? session('db_host') : '') }}" autocomplete="db_host" autofocus>
                                    @error('db_host')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="db_name" class="col-form-label">{{ __('Database name') }}</label>
                                <div class="">
                                    <input id="db_name" type="db_name" class="form-control @error('db_name') is-invalid @enderror"  name="db_name" value="{{ old('db_name') ? old('db_name') : (session('db_name') ? session('db_name') : '') }}" autocomplete="db_name" autofocus>
                                    @error('db_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="db_username" class="col-form-label">{{ __('Database username') }}</label>
                                <div class="">
                                    <input id="db_username" type="db_username" class="form-control @error('db_username') is-invalid @enderror"  name="db_username" value="{{ old('db_username') ? old('db_username') : (session('db_username') ? session('db_username') : '') }}" autocomplete="db_username" autofocus>
                                    @error('db_username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group ">
                                <label for="password" class="col-form-label">{{ __('Database password') }}</label>
                                <div class="">
                                    <div class="d-flex">
                                        <input type="password" id="db_password" class="form-control @error('db_password') is-invalid @enderror"  name="db_password" value="{{ old('db_password') ? old('db_password') : (session('db_password') ? session('db_password') : '') }}" autocomplete="db_password" autofocus>
                                        <div class="h-pill h-pill-primary ml-2 rounded" data-toggle="tooltip" data-placement="top" title="{{__('Show password')}}" onclick="Installer.togglePasswordField('db_password');">
                                            <div class="hide-pass d-none">
                                                @include('elements.icon',['icon'=>'eye-off-outline', 'variant' => 'medium'])
                                            </div>
                                            <div class="show-pass">
                                                @include('elements.icon',['icon'=>'eye-outline', 'variant' => 'medium'])
                                            </div>
                                        </div>
                                    </div>
                                    @error('db_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{route('installer.install').'?step=1'}}" class="">{{__("Back")}}</a>
                                <button type="submit" class="btn btn-primary m-0">{{__("Next")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
