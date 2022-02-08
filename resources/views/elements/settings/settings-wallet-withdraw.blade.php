<div class="d-flex justify-content-between align-items-center mt-3">
    <h5>{{__('Manual payouts')}}</h5>
    <div class="d-flex align-items-center">
        @include('elements.icon',['icon'=>'information-circle-outline','variant'=>'small','centered'=>false,'classes'=>'mr-2'])
        <span class="text-right" id="pending-balance" title="{{__("The payouts are manually and it usually take up to 24 hours for a withdrawal to be processed, we will notify you as soon as your request is processed.")}}">
            {{__('Pending balance')}} (<b class="wallet-pending-amount">{{config('app.site.currency_symbol')}}{{number_format(Auth::user()->wallet->pendingBalance, 2, '.', '')}}</b>)
        </span>
    </div>
</div>
<div class="input-group mb-3 mt-3">
    <div class="input-group-prepend">
        <span class="input-group-text" id="amount-label">@include('elements.icon',['icon'=>'cash-outline','variant'=>'medium'])</span>
    </div>
    <input class="form-control"
           placeholder="{{ \App\Providers\PaymentsServiceProvider::getWithdrawalAmountLimitations() }}"
           aria-label="Username"
           aria-describedby="amount-label"
           id="withdrawal-amount"
           type="number"
           min="{{\App\Providers\PaymentsServiceProvider::getWithdrawalMinimumAmount()}}"
           step="1"
           max="{{\App\Providers\PaymentsServiceProvider::getWithdrawalMaximumAmount()}}">
    <div class="invalid-feedback">{{__('Please enter a valid amount')}}</div>
    <div class="input-group mb-3 mt-3">
        <div class="form-group w-100">
            <label for="withdrawal-message">{{__('Message (Optional)')}}</label>
            <textarea placeholder="{{__('Bank account, notes, etc')}}" class="form-control" id="withdrawal-message" rows="2"></textarea>
        </div>
    </div>

    <div class="payment-error error text-danger d-none mt-3">{{__('Add all required info')}}</div>
    <button class="btn btn-primary btn-block rounded mr-0 withdrawal-continue-btn" type="submit">{{__('Request withdrawal')}}
        <div class="spinner-border spinner-border-sm ml-2 d-none" role="status">
            <span class="sr-only">{{__('Loading...')}}</span>
        </div>
    </button>
</div>
