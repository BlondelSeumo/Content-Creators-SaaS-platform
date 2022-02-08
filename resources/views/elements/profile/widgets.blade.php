<div class="profile-widgets-area pb-3">

    <div class="card recent-media rounded-lg">
        <div class="card-body m-0 pb-0">
        </div>
        <h5 class="card-title pl-3 mb-0">{{__('Recent')}}</h5>
        <div class="card-body {{$recentMedia ?? 'text-center'}}">
            @if($recentMedia && count($recentMedia))
                @foreach($recentMedia as $media)
                    <a href="{{$media->path}}" rel="mswp" title="">
                        <img src="{{AttachmentHelper::getThumbnailPathForAttachmentByResolution($media, 150, 150)}}" class="rounded mb-2 mb-md-2 mb-lg-2 mb-xl-0 img-fluid">
                    </a>
                @endforeach
            @else
                <p class="m-0">{{__('Latest media not available.')}}</p>
            @endif

        </div>
    </div>

    @if($user->paid_profile)
        @if(Auth::check())
            @if( !(isset($hasSub) && $hasSub) && !(isset($post) && PostsHelper::hasActiveSub(Auth::user()->id, $post->user->id)) && Auth::user()->id !== $user->id)
                <div class="card mt-3 rounded-lg">
                    <div class="card-body">
                        <h5 class="card-title">{{__('Subscription')}}</h5>
                        <button class="btn btn-round btn-outline-primary btn-block d-flex align-items-center justify-content-center justify-content-lg-between mt-3 mb-0"
                                data-toggle="modal"
                                data-target="#checkout-center"
                                data-type="one-month-subscription"
                                data-recipient-id="{{$user->id ? $user->id : ''}}"
                                data-amount="{{$user->profile_access_price ? $user->profile_access_price : 0}}"
                                data-first-name="{{Auth::user()->first_name}}"
                                data-last-name="{{Auth::user()->last_name}}"
                                data-billing-address="{{Auth::user()->billing_address}}"
                                data-country="{{Auth::user()->country}}"
                                data-city="{{Auth::user()->city}}"
                                data-state="{{Auth::user()->state}}"
                                data-postcode="{{Auth::user()->postcode}}"
                                data-available-credit="{{Auth::user()->wallet->total}}"
                                data-username="{{$user->username}}"
                                data-name="{{$user->name}}"
                                data-avatar="{{$user->avatar}}"
                        >
                            <span class="d-none d-md-block d-xl-block d-lg-block">{{__('Subscribe')}}</span>
                            <span class="d-none d-lg-block">{{config('app.site.currency_symbol') ?? config('app.site.currency_symbol')}}{{$user->profile_access_price}}{{config('app.site.currency_symbol') ? '' : ' ' .config('app.site.currency_code')}} {{__('for')}} {{trans_choice('days',30,['number'=>30])}}</span>
                        </button>
                    </div>
                </div>
            @endif
        @else
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">{{__('Subscription')}}</h5>
                    <button class="btn btn-round btn-outline-primary btn-block d-flex align-items-center justify-content-center justify-content-lg-between mt-3 mb-0"
                            data-toggle="modal"
                            data-target="#login-dialog"
                    >
                        <span class="d-none d-md-block d-xl-block d-lg-block">{{__('Subscribe')}}</span>
                        <span class="d-none d-lg-block">{{config('app.site.currency_symbol') ?? config('app.site.currency_symbol')}}{{$user->profile_access_price}}{{config('app.site.currency_symbol') ? '' : ' ' .config('app.site.currency_code')}} {{__('for')}} {{trans_choice('days',30,['number'=>30])}}</span>
                    </button>
                </div>
            </div>
        @endif
    @else
        @if(Auth::check())
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">{{__('Follow this creator')}}</h5>
                    <button class="btn btn-round btn-outline-primary btn-block mt-3 mb-0 manage-follow-button" onclick="Lists.manageFollowsAction('{{$user->id}}')">
                        <span class="manage-follows-text">{{\App\Providers\ListsHelperServiceProvider::getUserFollowingType($user->id, true)}}</span>
                    </button>
                </div>
            </div>
        @else
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">{{__('Follow this creator')}}</h5>
                    <button class="btn btn-round btn-outline-primary btn-block mt-3 mb-0 text-center"
                            data-toggle="modal"
                            data-target="#login-dialog"
                    >
                        <span class="d-none d-md-block d-xl-block d-lg-block">{{__('Follow')}}</span>
                    </button>
                </div>
            </div>
        @endif
    @endif

    @if(getSetting('ad-spaces.sidebar_ad_spot'))
        <div class="d-flex justify-content-center align-items-center mt-3">
            {!! getSetting('ad-spaces.sidebar_ad_spot') !!}
        </div>
    @endif

</div>
