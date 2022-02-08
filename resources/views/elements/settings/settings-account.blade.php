@if(!Auth::user()->email_verified_at) @include('elements.resend-verification-email-box') @endif

<form method="POST" action="{{route('my.settings.account.save')}}">
@csrf
    @if(session('success'))
        <div class="alert alert-success text-white font-weight-bold mt-2" role="alert">
            {{session('success')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="{{__('Close')}}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="form-group">
        <label for="username">{{__('Password')}}</label>
        <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="username" name="password" type="password">
        @if($errors->has('password'))
            <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('password')}}</strong>
            </span>
        @endif
    </div>

    <div class="form-group">
        <label for="username">{{__('New password')}}</label>
        <input class="form-control {{ $errors->has('new_password') ? 'is-invalid' : '' }}" id="username" name="new_password" type="password">
        @if($errors->has('new_password'))
            <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('new_password')}}</strong>
            </span>
        @endif
    </div>

    <div class="form-group">
        <label for="username">{{__('Confirm password')}}</label>
        <input class="form-control {{ $errors->has('confirm_password') ? 'is-invalid' : '' }}" id="username" name="confirm_password" type="password">
        @if($errors->has('confirm_password'))
            <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('confirm_password')}}</strong>
            </span>
        @endif
    </div>
    <button class="btn btn-primary btn-block rounded mr-0" type="submit">{{__('Save')}}</button>

</form>
