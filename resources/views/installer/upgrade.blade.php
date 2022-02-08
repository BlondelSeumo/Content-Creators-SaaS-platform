@extends('layouts.install')
@section('page_title', __('Update the script'))
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
                    <a href="{{route('installer.install')}}">
                        <img class="brand-logo" src="{{asset('/img/logo-black.png')}}">
                    </a>
                </div>
                <div class="col card shadow-sm">
                    <h4 class="card-title mt-4 ml-2">{{__("Update the platform")}}</h4>
                    <div class="card-body mt-2">
                        @if(!$canMigrate && !session('success'))
                            <div class="alert alert-warning text-white font-weight-bold mt-2" role="alert">
                                {{__("Looks like there are no available updates on the current installation.")}}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger text-white font-weight-bold mt-2" role="alert">
                                {{session('error')}}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if(session('success'))
                            <div class="alert alert-success text-white font-weight-bold mt-2" role="alert">
                                {{session('success')}}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div>
                            <p>{{__("Before proceeding with an update, please ensure that")}}:</p>
                            <ul>
                                <li>{{__("You've backed up your files.")}}</li>
                                <li>{{__("You've backed up your database.")}}</li>
                                <li>{{__("You've copied the updated files onto your public directory.")}}</li>
                            </ul>
                        </div>
                        <form method="POST" action="{{ route('installer.doUpdate') }}">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{route('home')}}" class="">{{__("Go home")}}</a>
                                <button type="submit" class="btn btn-primary m-0 {{$canMigrate ? '' : 'disabled'}}" {{$canMigrate ? '' : 'disabled'}}>{{__("Update")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
