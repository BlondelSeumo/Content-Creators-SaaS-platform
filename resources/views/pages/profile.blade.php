@extends('layouts.user-no-nav')

@section('page_title', $user->name.'\'s profile')

@section('scripts')
    {!!
        Minify::javascript([
            '/js/PostsPaginator.js',
            '/js/CommentsPaginator.js',
            '/js/Post.js',
            '/js/pages/profile.js',
            '/js/pages/lists.js',
            '/js/pages/checkout.js',
            '/libs/swiper/swiper-bundle.min.js',
            '/js/plugins/media/photoswipe.js',
            '/libs/photoswipe/dist/photoswipe-ui-default.min.js',
            '/libs/@joeattardi/emoji-button/dist/index.js',
            '/js/plugins/media/mediaswipe.js',
            '/js/plugins/media/mediaswipe-loader.js',
            '/js/LoginModal.js',
            '/js/pages/messenger.js',
         ])->withFullUrl()
    !!}
@stop

@section('styles')
    {!!
        Minify::stylesheet([
            '/css/pages/profile.css',
            '/css/pages/checkout.css',
            '/css/pages/lists.css',
            '/libs/swiper/swiper-bundle.min.css',
            '/libs/photoswipe/dist/photoswipe.css',
            '/libs/photoswipe/dist/default-skin/default-skin.css',
            '/css/pages/profile.css',
            '/css/pages/lists.css',
            '/css/posts/post.css'
         ])->withFullUrl()
    !!}
@stop

@section('content')
    <div class="row">
        <div class="min-vh-100 col-12 col-md-8 border-right pr-md-0">

            <div class="">
                <div class="profile-cover-bg">
                    <img class="card-img-top centered-and-cropped" src="{{$user->cover}}">
                </div>
            </div>

            <div class="container d-flex justify-content-between align-items-center">
                <div class="z-index-3 avatar-holder">
                    <img src="{{$user->avatar}}" class="rounded-circle">
                </div>
                <div>
                    @if(!Auth::check() || Auth::user()->id !== $user->id)
                        <div class="d-flex flex-row">
                            @if(Auth::check())
                                <div class="d-none d-sm-block">
                                <span class="p-pill ml-2 pointer-cursor to-tooltip"
                                      data-placement="top"
                                      title="{{__('Send a tip')}}"
                                      data-toggle="modal"
                                      data-target="#checkout-center"
                                      data-type="tip"
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
                                      data-recipient-id="{{$user->id}}">
                                 @include('elements.icon',['icon'=>'cash-outline'])
                                </span>
                                </div>
                                <div class="d-none d-sm-block df-">
                                    @if($hasSub)
                                        <span class="p-pill ml-2 pointer-cursor" data-toggle="tooltip" data-placement="top" title="{{__('Send a message')}}" onclick="messenger.showNewMessageDialog()">
                                            @include('elements.icon',['icon'=>'chatbubbles-outline'])
                                        </span>
                                    @else
                                        <span class="p-pill ml-2 pointer-cursor" data-toggle="tooltip" data-placement="top" title="{{__('DMs unavailable without subscription')}}">
                                        @include('elements.icon',['icon'=>'chatbubbles-outline'])
                                    </span>
                                    @endif
                                </div>
                                <span class="p-pill ml-2 pointer-cursor" data-toggle="tooltip" data-placement="top" title="{{__('Add to your lists')}}" onclick="Lists.showListAddModal();">
                                 @include('elements.icon',['icon'=>'list-outline'])
                            </span>
                            @endif
                            <span class="p-pill ml-2 pointer-cursor" data-toggle="tooltip" data-placement="top" title="{{__('Copy profile link')}}" onclick="shareOrCopyLink()">
                                 @include('elements.icon',['icon'=>'share-social-outline'])
                            </span>
                        </div>
                    @else
                        <div class="d-flex flex-row">
                            <div class="mr-2">
                                <a href="{{route('my.settings')}}" class="p-pill p-pill-text ml-2 pointer-cursor">
                                    @include('elements.icon',['icon'=>'settings-outline','classes'=>'mr-1'])
                                    <span class="d-none d-md-block">{{__('Edit profile')}}</span>
                                    <span class="d-block d-md-none">{{__('Edit')}}</span>
                                </a>
                            </div>
                            <div>
                                <span class="p-pill ml-2 pointer-cursor" data-toggle="tooltip" data-placement="top" title="{{__('Copy profile link')}}" onclick="shareOrCopyLink()">
                                    @include('elements.icon',['icon'=>'share-social-outline'])
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="container pt-2 pl-0 pr-0">

                <div class="pt-2 pl-4 pr-4">
                    <h5 class="text-bold d-flex align-items-center">{{$user->name}}
                        @if($user->email_verified_at && $user->birthdate && ($user->verification && $user->verification->status == 'verified'))
                            <span data-toggle="tooltip" data-placement="top" title="{{__('Verified user')}}">
                                @include('elements.icon',['icon'=>'checkmark-circle-outline','centered'=>true,'classes'=>'ml-1'])
                            </span>
                        @endif
                    </h5>
                    <h6 class="text-muted"><span class="text-bold"><span>@</span>{{$user->username}}</span> {{--- Last seen X time ago--}}</h6>
                </div>

                <div class="pt-2 pb-2 pl-4 pr-4 profile-description-holder">
                    <div class="description-content line-clamp-1">
                        {{$user->bio ? $user->bio : __('No description available.')}}
                    </div>
                    @if($user->bio && strlen($user->bio) >= 85)
                        <span class="text-primary pointer-cursor" onclick="Profile.toggleFullDescription()">
                            <span class="label-more">{{__('More info')}}</span>
                            <span class="label-less d-none">{{__('Show less')}}</span>
                        </span>
                    @endif
                </div>

                <div class="d-flex flex-column flex-lg-row pb-2 pl-4 pr-4 mb-3 mt-1">

                    @if($user->location)
                        <div class="d-flex align-items-center mr-2 text-truncate mb-0 mb-md-0">
                            @include('elements.icon',['icon'=>'location-outline','centered'=>false,'classes'=>'mr-1'])
                            <div class="text-truncate">
                                {{$user->location}}
                            </div>
                        </div>
                    @endif
                    @if($user->website)
                        <div class="d-flex align-items-center mr-2 text-truncate mb-0 mb-md-0">
                            @include('elements.icon',['icon'=>'globe-outline','centered'=>false,'classes'=>'mr-1'])
                            <div class="text-truncate">
                                <a href="{{$user->website}}" target="_blank" rel="nofollow">
                                    {{str_replace(['https://','http://','www.'],'',$user->website)}}
                                </a>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex align-items-center mr-2 text-truncate mb-0 mb-md-0">
                        @include('elements.icon',['icon'=>'calendar-outline','centered'=>false,'classes'=>'mr-1'])
                        <div class="text-truncate">
                            Joined {{$user->created_at->format('F d')}}
                        </div>
                    </div>

                </div>

                <div class="bg-separator border-top border-bottom"></div>

                @include('elements/message-alert')
                @if($user->paid_profile)
                    @if( (!Auth::check() || Auth::user()->id !== $user->id) && !$hasSub)
                        <div class=" p-4 subscription-holder">
                            <h6 class="font-weight-bold text-uppercase mb-3">{{__('Subscription')}}</h6>
                            @if(count($offer))
                                <h5 class="m-0 text-bold">{{__('Limited offer main label',['discount'=> round($offer['discountAmount']), 'days_remaining'=> $offer['daysRemaining'] ])}}</h5>
                                <small class="">{{__('Offer ends label',['date'=>$offer['expiresAt']->format('d M')])}}</small>
                            @endif
                            @if($hasSub)
                                <button class="btn btn-round btn-lg btn-primary btn-block mt-3 mb-2 text-center">
                                    <span>{{__('Subscribed')}}</span>
                                </button>
                            @else
                                <button class="btn btn-round btn-lg btn-primary btn-block d-flex justify-content-md-between  justify-content-center mt-3 mb-2 px-5"
                                        @if(Auth::check())
                                        data-toggle="modal"
                                        data-target="#checkout-center"
                                        data-type="one-month-subscription"
                                        data-recipient-id="{{$user->id}}"
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
                                        @else
                                        data-toggle="modal"
                                        data-target="#login-dialog"
                                    @endif
                                >
                                    <span>{{__('Subscribe')}}</span>
                                    <span class="d-none d-sm-block">{{config('app.site.currency_symbol') ?? config('app.site.currency_symbol')}}{{$user->profile_access_price}}{{config('app.site.currency_symbol') ? '' : ' ' .config('app.site.currency_code')}} {{__('for')}} {{trans_choice('days', 30,['number'=>30])}}</span>
                                </button>
                                <div class="d-flex justify-content-between">
                                    @if($user->profile_access_price_6_months || $user->profile_access_price_12_months)
                                        <small>
                                            <div class="pointer-cursor d-flex align-items-center" onclick="Profile.toggleBundles()">
                                                <div class="label-more">{{__('Subscriptions bundles')}}</div>
                                                <div class="label-less d-none">{{__('Hide bundles')}}</div>
                                                <div class="ml-1 label-icon">
                                                    @include('elements.icon',['icon'=>'chevron-down-outline','centered'=>false])
                                                </div>
                                            </div>
                                        </small>
                                    @endif
                                    @if(count($offer))
                                        <small class="">{{__('Regular price label',['currency'=>'USD','amount'=>$user->offer->old_profile_access_price])}}</small>
                                    @endif
                                </div>

                                @if($user->profile_access_price_6_months || $user->profile_access_price_12_months)
                                    <div class="subscription-bundles d-none mt-4">
                                        @if($user->profile_access_price_6_months)
                                            <button class="btn btn-round btn-outline-primary btn-block d-flex justify-content-between mt-2 mb-3 px-5"
                                                    @if(Auth::check())
                                                    data-toggle="modal"
                                                    data-target="#checkout-center"
                                                    data-type="six-months-subscription"
                                                    data-recipient-id="{{$user->id}}"
                                                    data-amount="{{$user->profile_access_price_6_months ? $user->profile_access_price_6_months * 6 : 0}}"
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
                                                    @else
                                                    data-toggle="modal"
                                                    data-target="#login-dialog"
                                                @endif
                                            >
                                                <span>{{__('Subscribe')}}</span>
                                                <span>{{config('app.site.currency_symbol') ?? config('app.site.currency_symbol')}}{{$user->profile_access_price_6_months}}{{config('app.site.currency_symbol') ? '' : ' ' .config('app.site.currency_code')}} {{__('for')}} {{trans_choice('months', 6,['number'=>6])}}</span>
                                            </button>
                                        @endif

                                        @if($user->profile_access_price_12_months)
                                            <button class="btn btn-round btn-outline-primary btn-block d-flex justify-content-between mt-2 mb-2 px-5"
                                                    @if(Auth::check())
                                                    data-toggle="modal"
                                                    data-target="#checkout-center"
                                                    data-type="yearly-subscription"
                                                    data-recipient-id="{{$user->id}}"
                                                    data-amount="{{$user->profile_access_price_12_months ? $user->profile_access_price_12_months * 12 : 0}}"
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
                                                    @else
                                                    data-toggle="modal"
                                                    data-target="#login-dialog"
                                                @endif
                                            >
                                                <span>{{__('Subscribe')}}</span>
                                                <span>{{config('app.site.currency_symbol') ?? config('app.site.currency_symbol')}}{{$user->profile_access_price_12_months}}{{config('app.site.currency_symbol') ? '' : ' ' .config('app.site.currency_code')}} {{__('for')}} {{trans_choice('months', 12,['number'=>12])}}</span>
                                            </button>
                                        @endif

                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="bg-separator border-top border-bottom"></div>
                    @endif
                @else
                    <div class=" p-4 subscription-holder">
                        <h6 class="font-weight-bold text-uppercase mb-3">{{__('Follow this creator')}}</h6>
                        @if(Auth::check())
                            <button class="btn btn-round btn-lg btn-primary btn-block mt-3 mb-0 manage-follow-button" onclick="Lists.manageFollowsAction('{{$user->id}}')">
                                <span class="manage-follows-text">{{\App\Providers\ListsHelperServiceProvider::getUserFollowingType($user->id, true)}}</span>
                            </button>
                        @else
                            <button class="btn btn-round btn-lg btn-primary btn-block mt-3 mb-0 text-center"
                                    data-toggle="modal"
                                    data-target="#login-dialog"
                            >
                                <span class="d-none d-md-block d-xl-block d-lg-block">{{__('Follow')}}</span>
                            </button>
                        @endif
                    </div>
                    <div class="bg-separator border-top border-bottom"></div>
                @endif
                <div class="mt-3 inline-border-tabs">
                    <nav class="nav nav-pills nav-justified text-bold">
                        <a class="nav-item nav-link {{$activeFilter == false ? 'active' : ''}}" href="{{route('profile',['username'=> $user->username])}}">{{trans_choice('posts', $posts->total(), ['number'=>$posts->total()])}} </a>

                        @if($filterTypeCounts['image'] > 0)
                            <a class="nav-item nav-link {{$activeFilter == 'image' ? 'active' : ''}}" href="{{route('profile',['username'=> $user->username]) . '?filter=image'}}">{{trans_choice('images', $filterTypeCounts['image'], ['number'=>$filterTypeCounts['image']])}}</a>
                        @endif

                        @if($filterTypeCounts['video'] > 0)
                            <a class="nav-item nav-link {{$activeFilter == 'video' ? 'active' : ''}}" href="{{route('profile',['username'=> $user->username]) . '?filter=video'}}">{{trans_choice('videos', $filterTypeCounts['video'], ['number'=>$filterTypeCounts['video']])}}</a>

                        @endif

                        @if($filterTypeCounts['audio'] > 0)
                            <a class="nav-item nav-link {{$activeFilter == 'audio' ? 'active' : ''}}" href="{{route('profile',['username'=> $user->username]) . '?filter=audio'}}">{{trans_choice('audio', $filterTypeCounts['audio'], ['number'=>$filterTypeCounts['audio']])}}</a>
                        @endif

                    </nav>
                </div>
                <div class="justify-content-center align-items-center mt-4">
                    @include('elements.feed.posts-load-more')
                    <div class="feed-box mt-0 posts-wrapper">
                        @include('elements.feed.posts-wrapper',['posts'=>$posts])
                    </div>
                    @include('elements.feed.posts-loading-spinner')
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 d-none d-md-block pt-3">
            @include('elements.profile.widgets')
        </div>
    </div>

    @if(Auth::check())
        @include('elements.lists.list-add-user-dialog',['user_id' => $user->id, 'lists' => ListsHelper::getUserLists()])
        @include('template.checkout')
        @include('elements.messenger.send-user-message',['receiver'=>$user])
    @else
        @include('elements.modal-login')
    @endif

@stop
