    <div class="row checkout-dialog">
        <div class="col-lg-6 mx-auto">
            {{-- Paypal and stripe actual buttons --}}
            <div class="paymentOption paymentPP d-none">
                <form id="pp-buyItem" method="post" action="{{route('payment.initiatePayment')}}">
                    @csrf
                    <input type="hidden" name="amount" id="payment-deposit-amount" value="">
                    <input type="hidden" name="transaction_type" id="payment-type" value="">
                    <input type="hidden" name="post_id" id="post" value="">
                    <input type="hidden" name="recipient_user_id" id="recipient" value="">
                    <input type="hidden" name="provider" id="provider" value="">
                    <input type="hidden" name="first_name" id="paymentFirstName" value="">
                    <input type="hidden" name="last_name" id="paymentLastName" value="">
                    <input type="hidden" name="billing_address" id="paymentBillingAddress" value="">
                    <input type="hidden" name="city" id="paymentCity" value="">
                    <input type="hidden" name="state" id="paymentState" value="">
                    <input type="hidden" name="postcode" id="paymentPostcode" value="">
                    <input type="hidden" name="country" id="paymentCountry" value="">
                    <input type="hidden" name="taxes" id="paymentTaxes" value="">

                    <button class="payment-button" type="submit"></button>
                </form>
            </div>

            <div class="paymentOption ml-2 paymentStripe d-none">
                <button id="stripe-checkout-button">{{__('Checkout')}}</button>
            </div>

            <!-- Modal -->
            <div class="checkout-popup modal fade" id="checkout-center" tabindex="-1" role="dialog" aria-labelledby="checkout" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="payment-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="{{__('Close')}}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="payment-body">
                                <div class="d-flex flex-row">
                                    <div class="ml-0 ml-md-2 mb-2">
                                            <img src="" class="rounded-circle user-avatar">
                                    </div>
                                    <div class="d-lg-block">
                                        <div class="pl-2 d-flex justify-content-center flex-column">
                                            <div class="ml-2 ">
                                                <div class="text-bold {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}} name"></div>
                                                <div class="text-muted username"><span>@</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-description mb-3 d-none"></div>
                                <div class="input-group mb-3 checkout-amount-input d-none">
                                    <div class="input-group-prepend">
                            <span class="input-group-text" id="amount-label">
                                @include('elements.icon',['icon'=>'cash-outline','variant'=>'medium','centered'=>false])
                            </span>
                                    </div>
                                    <input class="form-control" placeholder="{{__('Amount ($5 min, $500 max)',['min'=>5,'max'=>500,'currency'=>config('app.site.currency_symbol')])}}" aria-label="Username" aria-describedby="amount-label" id="checkout-amount" type="number" min="0" step="1" max="500">
                                    <div class="invalid-feedback">{{__('Please enter a valid amount.')}}</div>
                                </div>
                            </div>

                            <div id="accordion" class="mb-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between" id="headingOne" data-toggle="collapse" data-target="#billingInformation" aria-expanded="true" aria-controls="billingInformation">
                                        <h6 class="mb-0">
                                            {{__('Billing agreement details')}}
                                        </h6>
                                        <div class="ml-1 label-icon">
                                            @include('elements.icon',['icon'=>'chevron-down-outline','centered'=>false])
                                        </div>
                                    </div>
                                    <div id="billingInformation" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                        <div class="card-body">
                                            <form id="billing-agreement-form">
                                                <div class="tab-content">
                                                    <!-- credit card info-->
                                                    <div id="individual" class="tab-pane fade show active pt-1">
                                                        <div class="row form-group">
                                                            <div class="col-sm-6 col-6">
                                                                <label for="firstName">
                                                                    <span>{{__('First Name')}}</span>
                                                                </label>
                                                                <input type="text" name="firstName" placeholder="{{__('First name')}}" onblur="checkout.validateFirstNameField();" required class="form-control" minlength="3" maxlength="191">
                                                            </div>
                                                            <div class="col-sm-6 col-6">
                                                                <label for="lastName">
                                                                    <span>{{__('Last Name')}}</span>
                                                                </label>
                                                                <input type="text" name="lastName" placeholder="{{__('Last name')}}" onblur="checkout.validateLastNameField()" required class="form-control" minlength="3" maxlength="191">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="countrySelect">
                                                                <span>{{__('Country')}}</span>
                                                            </label>
                                                            <div class="input-group mb-3 input-holder">
                                                                <select class="country-select form-control input-sm" id="countrySelect" required onchange="checkout.validateCountryField()"></select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="billingCity">
                                                                <span>{{__('City')}}</span>
                                                            </label>
                                                            <input type="text" name="billingCity" placeholder="{{__('City')}}" onblur="checkout.validateCityField()" required class="form-control" minlength="2" maxlength="191">
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-sm-6 col-6">
                                                                <label for="billingState">
                                                                    <span>{{__('State')}}</span>
                                                                </label>
                                                                <input type="text" name="billingState" placeholder="{{__('State')}}" onblur="checkout.validateStateField()" required class="form-control" minlength="2" maxlength="191">
                                                            </div>
                                                            <div class="col-sm-6 col-6">
                                                                <label for="billingPostcode">
                                                                    <span>{{__('Postcode')}}</span>
                                                                </label>
                                                                <input type="text" name="billingPostcode" placeholder="{{__('Postcode')}}" onblur="checkout.validatePostcodeField()" required class="form-control" minlength="2" maxlength="191">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="cardNumber">
                                                                <span>{{__('Address')}}</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <textarea rows="2" type="text" name="billingAddress" onblur="checkout.validateBillingAddressField()" placeholder="{{__('Street address, apartment, suite, unit')}}" class="form-control w-100" minlength="10" maxlength="191" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="billing-agreement-error error text-danger d-none">{{__('Please complete all billing details')}}</div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6>{{__('Payment summary')}}</h6>
                                <div class="subtotal row">
                                    <span class="col-sm left"><b>{{__('Subtotal')}}:</b></span>
                                    <span class="subtotal-amount col-sm right text-right">
                                        <b>$0.00</b>
                                    </span>
                                </div>
                                <div class="taxes row">
                                    <span class="col-sm left"><b>{{__('Taxes')}}:</b></span>
                                    <span class="country-taxes col-sm right text-right">
                                        <b>$0.00</b>
                                    </span>
                                </div>
                                <div class="taxes-details"></div>
                                <div class="total row">
                                    <span class="col-sm left"><b>{{__('Total')}}:</b></span>
                                    <span class="total-amount col-sm right text-right">
                                        <b>$0.00</b>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <h6>{{__('Payment method')}}</h6>
                                <div class="d-flex text-left radio-group row">
                                    @if(getSetting('payments.stripe_secret_key') && getSetting('payments.stripe_public_key'))
                                        <div class="p-1 col-6 col-md-3" >
                                            <div class="radio mx-auto stripe-payment-provider d-flex align-items-center justify-content-center" data-value="stripe">
                                                <img src="{{asset('/img/logos/stripe.svg')}}">
                                            </div>
                                        </div>
                                    @endif
                                    @if(config('paypal.client_id') && config('paypal.secret'))
                                        <div class="p-1 col-6 col-md-3">
                                            <div class="radio mx-auto paypal-payment-provider d-flex align-items-center justify-content-center" data-value="paypal">
                                                <img src="{{asset('/img/logos/paypal.svg')}}">
                                            </div>
                                        </div>
                                    @endif
                                    @if(getSetting('payments.coinbase_api_key'))
                                        <div class="p-1 col-6 col-md-3 d-none coinbase-payment-method">
                                            <div class="radio mx-auto coinbase-payment-provider d-flex align-items-center justify-content-center" data-value="coinbase">
                                                <img src="{{asset('/img/logos/coinbase.svg')}}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="p-1 col-6 col-md-3">
                                        <div class="radio mx-auto credit-payment-provider d-flex align-items-center justify-content-center" data-value="credit">
                                            <div class="credit-provider-text">
                                                <b>{{__("Credit")}}</b>
                                                <div class="available-credit">(0{{\App\Providers\SettingsServiceProvider::getWebsiteCurrencySymbol()}})</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-error error text-danger d-none">{{__('Please select your payment method')}}</div>
                            <p class="text-muted"> {{__('Note: After clicking on the button, you will be directed to a secure gateway for payment. After completing the payment process, you will be redirected back to the website.')}} </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
                            <button type="submit" class="btn btn-primary checkout-continue-btn">{{__('Continue')}}
                                <div class="spinner-border spinner-border-sm ml-2 d-none" role="status">
                                    <span class="sr-only">{{__('Loading...')}}</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
