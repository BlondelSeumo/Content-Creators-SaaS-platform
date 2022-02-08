<div class="px-3 px-md-0">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-10">
            <div class="">
                <img src="{{asset('/img/post-locked.svg')}}">
            </div>
        </div>
    </div>
    @if($type == 'post')
        <div class="col-12">
            <button class="btn btn-outline-primary btn-block"
                    @if(Auth::check())
                    data-toggle="modal"
                    data-target="#checkout-center"
                    data-type="post-unlock"
                    data-recipient-id="{{$post->user->id}}"
                    data-amount="{{$post->price}}"
                    data-first-name="{{Auth::user()->first_name}}"
                    data-last-name="{{Auth::user()->last_name}}"
                    data-billing-address="{{Auth::user()->billing_address}}"
                    data-country="{{Auth::user()->country}}"
                    data-city="{{Auth::user()->city}}"
                    data-state="{{Auth::user()->state}}"
                    data-postcode="{{Auth::user()->postcode}}"
                    data-available-credit="{{Auth::user()->wallet->total}}"
                    data-username="{{$post->user->username}}"
                    data-name="{{$post->user->name}}"
                    data-avatar="{{$post->user->avatar}}"
                    data-post-id="{{$post->id}}"
                    @else
                    data-toggle="modal"
                    data-target="#login-dialog"
                    @endif
            >{{__('Unlock post for')}} {{config('app.site.currency_symbol') ?? config('app.site.currency_symbol')}}{{$post->price}}{{config('app.site.currency_symbol') ? '' : ' ' .config('app.site.currency_code')}}</button>
        </div>
    @endif
</div>
