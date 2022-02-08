{{-- Paypal and stripe actual buttons --}}
<div class="paymentOption paymentPP d-none">
    <form id="wallet-deposit" method="post" action="{{route('payment.initiatePayment')}}" >
        @csrf
        <input type="hidden" name="amount" id="wallet-deposit-amount" value="1">
        <input type="hidden" name="transaction_type" id="payment-type" value="">
        <input type="hidden" name="provider" id="provider" value="">

        <button class="payment-button" type="submit"></button>
    </form>
</div>

<div class="paymentOption ml-2 paymentStripe d-none">
    <button id="stripe-checkout-button">{{__('Checkout')}}</button>
</div>

{{-- Actual form --}}
<div>
    @include('elements/message-alert')

    <div class="alert alert-primary text-white font-weight-bold" role="alert">
        <div class="d-flex"><h3 class="font-weight-bold wallet-total-amount">{{\App\Providers\SettingsServiceProvider::getWebsiteCurrencySymbol()}}{{number_format(Auth::user()->wallet->total, 2, '.', '')}}</h3> <small class="ml-2"></small> </div>
        <p class="mb-0">{{__('Available funds. You can deposit more money or become a creator to earn more.')}}</p>
    </div>

    <div class="mt-3 inline-border-tabs">
        <nav class="nav nav-pills nav-justified">
            @foreach(['deposit', 'withdraw'] as $tab)
                <a class="nav-item nav-link {{$activeTab == $tab ? 'active' : ''}}" href="{{route('my.settings',['type' => 'wallet', 'active' => $tab])}}">

                    <div class="d-flex align-items-center justify-content-center">
                        @if($tab == 'deposit')
                            @include('elements.icon',['icon'=>'wallet','variant'=>'medium','classes'=>'mr-2'])
                        @else
                            @include('elements.icon',['icon'=>'card','variant'=>'medium','classes'=>'mr-2'])
                        @endif
                        {{__(ucfirst($tab))}}

                    </div>
                </a>
            @endforeach
        </nav>
    </div>

    @if($activeTab != null && $activeTab === 'withdraw')
        @include('elements/settings/settings-wallet-withdraw')
    @else
        @include('elements/settings/settings-wallet-deposit')
    @endif

</div>
