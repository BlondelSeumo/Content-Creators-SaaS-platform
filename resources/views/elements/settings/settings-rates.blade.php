@if(session('success'))
    <div class="alert alert-success text-white font-weight-bold mt-2" role="alert">
        {{session('success')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{__('Close')}}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form method="POST" action="{{route('my.settings.rates.save')}}">
    @csrf
    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="paid-profile" name="paid-profile"
                    {{isset(Auth::user()->paid_profile) ? (Auth::user()->paid_profile == '1' ? 'checked' : '') : false}}>
            <label class="custom-control-label" for="paid-profile">{{__('Paid profile')}}</label>
        </div>
    </div>
    <div class="paid-profile-rates {{isset(Auth::user()->paid_profile) ? (Auth::user()->paid_profile == '1' ? '' : 'd-none') : ''}}">
        <div class="form-group">
            <label for="name">{{__('Your profile subscription price')}}</label>
            <input class="form-control {{ $errors->has('profile_access_price') ? 'is-invalid' : '' }}" id="profile_access_price" name="profile_access_price" aria-describedby="emailHelp" value="{{Auth::user()->profile_access_price}}">
            @if($errors->has('profile_access_price'))
                <span class="invalid-feedback" role="alert">
                <strong>{{__($errors->first('profile_access_price'))}}</strong>
            </span>
            @endif
        </div>
        <div class="form-group">
            <label for="name">{{__('6 months subscription price')}}</label>
            <input class="form-control {{ $errors->has('profile_access_price_6_months') ? 'is-invalid' : '' }}" id="profile_access_price" name="profile_access_price_6_months" aria-describedby="emailHelp" value="{{Auth::user()->profile_access_price_6_months}}">
            @if($errors->has('profile_access_price_6_months'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{__($errors->first('profile_access_price_6_months'))}}</strong>
                </span>
            @endif
        </div>
        <div class="form-group">
            <label for="name">{{__('12 months subscription price')}}</label>
            <input class="form-control {{ $errors->has('profile_access_price_12_months') ? 'is-invalid' : '' }}" id="profile_access_price_12_months" name="profile_access_price_12_months" aria-describedby="emailHelp" value="{{Auth::user()->profile_access_price_12_months}}">
            @if($errors->has('profile_access_price_12_months'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{__($errors->first('profile_access_price_12_months'))}}</strong>
                </span>
            @endif
        </div>
        <div class="form-group">
            <label for="name">{{__('Is offer until')}}</label>
            <div class="input-group-prepend">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" aria-label="Checkbox for following text input" name="is_offer" id="is_offer" {{Auth::user()->offer ? 'checked' : ''}}>
                    </div>
                </div>
                <input type="date" class="form-control {{ $errors->has('profile_access_offer_date') ? 'is-invalid' : '' }}" id="profile_access_offer_date" name="profile_access_offer_date" aria-describedby="emailHelp" value="{{Auth::user()->offer ? Auth::user()->offer->expires_at->format('Y-m-d') : ''}}">
            </div>
            @if($errors->has('profile_access_offer_date'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{__($errors->first('profile_access_offer_date'))}}</strong>
            </span>
            @endif
        </div>
        <button class="btn btn-primary btn-block rounded mr-0" type="submit">{{__('Save')}}</button>
    </div>
</form>


