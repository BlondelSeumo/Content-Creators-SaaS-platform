@if(count($subscriptions))
    <div class="table-wrapper">
        @include('elements/message-alert')
        <div class="">
            <div class="col d-flex align-items-center py-3 border-bottom text-bold">
                <div class="col-3 col-md-4 text-truncate">{{__('To')}}</div>
                <div class="col-3 col-md-2 text-truncate">{{__('Status')}}</div>
                <div class="col-2 text-truncate">{{__('Paid with')}}</div>
                <div class="col-2 text-truncate">{{__('Renews')}}</div>
                <div class="col-2 text-truncate"></div>
            </div>
            <tbody>
            @foreach($subscriptions as $subscription)
                <div class="col d-flex align-items-center py-3 border-bottom">
                    <div class="col-3 col-md-4 text-truncate">
                        <span class="mr-2">
                            <img src="{{$subscription->creator->avatar}}" class="rounded-circle user-avatar" width="35">
                        </span>
                        <a href="{{route('profile',['username'=>$subscription->creator->username])}}" class="text-dark-r">
                            {{$subscription->creator->name}}
                        </a>
                    </div>
                    <div class="col-3 col-md-2">
                        @switch($subscription->status)
                            @case('pending')
                            @case('update-needed')
                            @case('canceled')
                            <span class="badge badge-warning">{{ucfirst(__($subscription->status))}}</span>
                            @break
                            @case('completed')
                            <span class="badge badge-success">{{ucfirst(__($subscription->status))}}</span>
                            @break
                            @case('suspended')
                            @case('expired')
                            <span class="badge badge-danger">{{ucfirst(__($subscription->status))}}</span>
                            @break
                        @endswitch
                    </div>
                    <div class="col-2 text-truncate">{{ucfirst($subscription->provider)}}</div>
                    <div class="col-2 text-truncate">{{isset($subscription->expires_at) ? ($subscription->status == 'canceled' && $subscription->expires_at < \Carbon\Carbon::now() ? '-' : $subscription->expires_at->format('M d Y')) : ''}}</div>
                    <div class="col-2 text-center">
                        <div class="dropdown {{Cookie::get('app_rtl') == 'rtl' ? 'dropright' : 'dropleft'}}">
                            <a class="btn btn-sm text-dark-r text-hover {{$subscription->status == 'canceled' ? 'disabled' : ''}} btn-outline-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light'))}} dropdown-toggle m-0 py-1 px-2" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                @include('elements.icon',['icon'=>'ellipsis-horizontal-outline','centered'=>false])
                            </a>
                            <div class="dropdown-menu">
                                <!-- Dropdown menu links -->
                                @if($subscription->status === \App\Model\Subscription::ACTIVE_STATUS)
                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)" onclick="SubscriptionsSettings.confirmSubCancelation({{$subscription->id}})">
                                        @include('elements.icon',['icon'=>'trash-outline','centered'=>false,'classes'=>'mr-2']) {{__('Cancel subscription')}}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex flex-row-reverse mt-3 mr-4">
            {{ $subscriptions->links() }}
        </div>
        @else
            <div class="p-3">
                <p>{{__('There are no active or cancelled subscriptions at the moment.')}}</p>
            </div>
@endif

@include('elements.settings.transaction-cancel-dialog')
