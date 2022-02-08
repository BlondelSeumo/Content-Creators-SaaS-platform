<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-group ">
        <label for="name" class="col-form-label">{{ __('Name') }}</label>
        <div class="">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"  autocomplete="name" autofocus>
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group ">
        <label for="email" class=" col-form-label ">{{ __('E-Mail Address') }}</label>
        <div class="">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group ">
        <label for="password" class=" col-form-label ">{{ __('Password') }}</label>
        <div class="">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" required name="password" autocomplete="new-password">

            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group ">
        <label for="password-confirm" class=" col-form-label ">{{ __('Confirm Password') }}</label>

        <div class="">
            <input id="password-confirm" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" required name="password_confirmation" autocomplete="new-password">
            @error('password_confirmation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <div class="">
                <input class="custom-control-input @error('terms') is-invalid @enderror" id="tosAgree" type="checkbox" name="terms" value="1" placeholder="{{ __('Terms and Conditions') }}">
                <label class="custom-control-label" for="tosAgree">
                    <span>{{ __('I agree to the') }} <a href="#">{{ __('Terms of Use') }}</a> {{ __('and') }} <a href="#">{{ __('Privacy Policy') }}</a>.</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col">
            <button type="submit" class="btn btn-grow btn-lg btn-primary bg-gradient-primary btn-block">
                {{ __('Register') }}
            </button>
        </div>
    </div>

</form>

<hr>
<div class=" text-center">
    <p class="mb-4">
        {{__('Already got an account?')}}
        @if(isset($mode) && $mode == 'ajax')
            <a href="javascript:void(0);" onclick="LoginModal.changeActiveTab('login')" class="text-primary text-gradient font-weight-bold">{{__('Sign in')}}</a>
        @else
            <a href="{{route('login')}}" class="text-primary text-gradient font-weight-bold">{{__('Sign in')}}</a>
        @endif
    </p>
</div>
