@if(count($payments))
    <div class="table-wrapper ">
        <div class="">
            <div class="col d-flex align-items-center py-3 border-bottom text-bold">
                <div class="col-lg-3 text-truncate">{{__('Type')}}</div>
                <div class="col-lg-2 text-truncate">{{__('Status')}}</div>
                <div class="col-lg-2 text-truncate">{{__('Amount')}}</div>
                <div class="col-lg-2 text-truncate d-none d-md-block">{{__('From')}}</div>
                <div class="col-lg-2 text-truncate d-none d-md-block">{{__('To')}}</div>
                <div class="col-lg-1 text-truncate"></div>
            </div>
            @foreach($payments as $payment)
                <div class="col d-flex align-items-center py-3 border-bottom">
                    <div class="col-lg-3 text-truncate">
                        {{ucfirst(__($payment->type))}}
                    </div>
                    <div class="col-lg-2">
                        @switch($payment->status)
                            @case('approved')
                            <span class="badge badge-success">
                                {{ucfirst(__($payment->status))}}
                            </span>
                            @break
                            @case('initiated')
                            @case('pending')
                            <span class="badge badge-info">
                                {{ucfirst(__($payment->status))}}
                            </span>
                            @break
                            @case('canceled')
                            @case('refunded')
                            <span class="badge badge-warning">
                                {{ucfirst(__($payment->status))}}
                            </span>
                            @break
                            @case('declined')
                            <span class="badge badge-danger">
                                {{ucfirst(__($payment->status))}}
                            </span>
                            @break
                        @endswitch
                    </div>
                    <div class="col-lg-2 text-truncate">{{config('app.site.currency_symbol')}}{{$payment->amount}}</div>
                    <div class="col-lg-2 text-truncate d-none d-md-block">
                        <a href="{{route('profile',['username'=>$payment->sender->username])}}" class="text-dark-r">
                            {{$payment->sender->name}}
                        </a>
                    </div>
                    <div class="col-lg-2 text-truncate d-none d-md-block">
                        <a href="{{route('profile',['username'=>$payment->receiver->username])}}" class="text-dark-r">
                            {{$payment->receiver->name}}
                        </a>
                    </div>
                    <div class="col-lg-1 d-flex justify-content-center">
                        @if($payment->invoice_id && $payment->invoice_id != null)
                        <div class="dropdown {{Cookie::get('app_rtl') == 'rtl' ? 'dropright' : 'dropleft'}}">
                            <a class="btn btn-sm text-dark-r text-hover btn-outline-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light'))}} dropdown-toggle m-0 py-1 px-2" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                @include('elements.icon',['icon'=>'ellipsis-horizontal-outline','centered'=>false])
                            </a>
                            <div class="dropdown-menu">
                                <!-- Dropdown menu links -->
                                @if($payment->invoice_id && $payment->invoice_id != null)
                                    <a class="dropdown-item d-flex align-items-center" href="{{route('invoices.get', ['id' => $payment->invoice_id])}}">
                                        @include('elements.icon',['icon'=>'document-outline','centered'=>false,'classes'=>'mr-2']) {{__('View invoice')}}
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="d-flex flex-row-reverse mt-3 mr-4">
        {{ $payments->links() }}
    </div>
@else
    <div class="p-3">
        <p>{{__('There are no payments on this account.')}}</p>
    </div>
@endif
