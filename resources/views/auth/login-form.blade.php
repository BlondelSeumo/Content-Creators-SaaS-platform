<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group ">
        <label for="email" class="col-form-label">{{ __('E-Mail Address') }}</label>
        <div class="">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"  name="email" value="{{ old('email') }}" autocomplete="email" autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="password" class="col-form-label">{{ __('Password') }}</label>
        <div class="">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"  name="password" autocomplete="current-password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="loginHelpers form-group d-flex flex-row-reverse">
        @if (Route::has('password.request'))
            <div class="pull-right">
                @if(isset($mode) && $mode == 'ajax')
                    <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('forgot')" class="" id="forgotPass-label">{{ __('Forgot Your Password?') }}</a>
                @else
                    <a href="{{ route('password.request') }}" class="" id="forgotPass-label">{{ __('Forgot Your Password?') }}</a>
                @endif
            </div>
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="form-group row mb-0 mt-4">
        <div class="col">
            <button type="submit" class="btn btn-grow btn-lg btn-primary bg-gradient-primary btn-block">
                {{__('Login')}}
            </button>
        </div>
    </div>

</form>
<hr>
<div class=" text-center">
    <p class="">
        {{__("Don't have an account?")}}
        @if(isset($mode) && $mode == 'ajax')
            <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('register')" class="text-primary text-gradient font-weight-bold">{{__('Sign up')}}</a>
        @else
            <a href="{{route('register')}}" class="text-primary text-gradient font-weight-bold">{{__('Sign up')}}</a>
        @endif
    </p>
</div>
