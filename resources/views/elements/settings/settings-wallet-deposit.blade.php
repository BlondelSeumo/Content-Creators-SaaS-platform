<h5 class="mt-3">{{__('Proceed with payment')}}</h5>
<div class="input-group mb-3 mt-3">
    <div class="input-group-prepend">
        <span class="input-group-text" id="amount-label">@include('elements.icon',['icon'=>'cash-outline','variant'=>'medium'])</span>
    </div>
    <input class="form-control" placeholder="{{\App\Providers\PaymentsServiceProvider::getDepositLimitAmounts()}}"
           aria-label="{{__('Username')}}"
           aria-describedby="amount-label"
           id="deposit-amount"
           type="number"
           min="{{\App\Providers\PaymentsServiceProvider::getDepositMinimumAmount()}}"
           step="1"
           max="{{\App\Providers\PaymentsServiceProvider::getDepositMaximumAmount()}}">
    <div class="invalid-feedback">{{__('Please enter a valid amount.')}}</div>
</div>

<div>
    <div class="payment-method">
        @if(config('paypal.client_id') && config('paypal.secret'))
            <div class="custom-control custom-radio mb-1">
                <input type="radio" id="customRadio1" name="payment-radio-option" class="custom-control-input"
                       value="payment-paypal">
                <label class="custom-control-label" for="customRadio1">Paypal</label>
            </div>
        @endif
        @if(getSetting('payments.stripe_secret_key') && getSetting('payments.stripe_public_key'))
            <div class="custom-control custom-radio mb-1">
                <input type="radio" id="customRadio2" name="payment-radio-option" class="custom-control-input"
                       value="payment-stripe">
                <label class="custom-control-label stepTooltip" for="customRadio2" title=""
                       data-original-title="{{__('You need to login first')}}">Stripe</label>
            </div>
        @endif
        @if(getSetting('payments.coinbase_api_key'))
            <div class="custom-control custom-radio mb-1">
                <input type="radio" id="customRadio3" name="payment-radio-option" class="custom-control-input"
                       value="payment-coinbase">
                <label class="custom-control-label stepTooltip" for="customRadio3" title="">Coinbase</label>
            </div>
        @endif
    </div>
    <div class="payment-error error text-danger d-none mt-3">{{__('Please select your payment method')}}</div>
    <button class="btn btn-primary btn-block rounded mr-0 mt-4 deposit-continue-btn" type="submit">{{__('Add funds')}}
        <div class="spinner-border spinner-border-sm ml-2 d-none" role="status">
            <span class="sr-only">{{__('Loading...')}}</span>
        </div>
    </button>
</div>



