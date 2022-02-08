@extends('layouts.generic')
@section('page_title', __('Internal error occurred'))

@section('content')
    <div class="container">
        <div class=" d-flex justify-content-center align-items-center min-vh-65" >
            <div class="error-container d-flex flex-column">

                <div class="d-flex justify-content-center align-items-center">
                    <img src="{{asset('/img/500.svg')}}">
                </div>

                <div class="text-center">

                    <h3 class="text-bold">{{__('An internal error occurred')}}</h3>
                    <p class="mb-1">{{__('Sorry, something happened on our side.')}}</p>
                    <p>{{__('Please try again or contact us if the error persists.')}}</p>
                    <div class="d-flex flex-row-reverse mt-2">
                        <a href="{{route('home')}}" class="right">{{__('Go home')}}</a>
                    </div>
                </div>


            </div>
        </div>
    </div>
@stop
