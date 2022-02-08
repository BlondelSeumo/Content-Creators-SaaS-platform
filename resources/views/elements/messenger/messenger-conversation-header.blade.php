<div class="conversation-header d-none">
    <div class="details-holder border-bottom">
        <div class="d-flex px-1">
            <div class="col-10 pl-0 d-flex">
                <div class="">
                    <img class="conversation-header-avatar" src="{{asset('/img/no-avatar.png')}}" />
                </div>
                <div class="mt-2 ml-3 conversation-header-user text-truncate">

                </div>
            </div>
            <div class="col-2 pt-2 pr-0 d-flex justify-content-end">
                <div class="dropdown {{Cookie::get('app_rtl') == 'rtl' ? 'dropright' : 'dropleft'}}">
                    <a class="btn btn-sm btn-outline-primary dropdown-toggle px-2 py-1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        @include('elements.icon',['icon'=>'ellipsis-horizontal-outline'])
                    </a>
                    <div class="dropdown-menu">
                        <!-- Dropdown menu links -->
                        <a class="dropdown-item d-flex align-items-center tip-btn"
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
                        >{{__('Send a tip')}}</a>
                        <a class="dropdown-item d-flex align-items-center conversation-profile-link" href="#" target="_blank">{{__('Go to profile')}}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item unfollow-btn" href="javascript:void(0);">{{__('Unfollow')}}</a>
                        <a class="dropdown-item block-btn" href="javascript:void(0);">{{__('Block')}}</a>
                        <a class="dropdown-item report-btn" href="javascript:void(0);">{{__('Report')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
