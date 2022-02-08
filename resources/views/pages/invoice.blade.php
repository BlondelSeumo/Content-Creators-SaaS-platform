<!doctype html>
<html class="h-100" dir="{{Cookie::get('app_rtl') == 'rtl' ? 'rtl' : 'ltr'}}" lang="{{session('locale')}}">
<head>
    @section('page_title', __('Invoice'))
    @section('styles')
        {!!
            Minify::stylesheet([
                '/css/pages/invoices.css',
             ])->withFullUrl()
        !!}
    @stop
    @include('template.head')
</head>
<body class="d-flex flex-column">
<div class="flex-fill invoice-body invoice-body-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light'))}}">
    <div class="container">
        <div class="invoice-logo text-center">
            <img class="brand-logo text-center" src="{{asset( (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo')) : (Cookie::get('app_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo'))) )}}">
        </div>
        @if(isset($invoice->data))
            <div class="row invoice row-printable d-flex justify-content-center align-items-center">
                <div class="col-md-10 invoice-content invoice-border-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light'))}}">
                    <!-- col-lg-12 start here -->
                    <div class="panel panel-default plain" id="dash_0">
                        <!-- Start .panel -->
                        <div class="panel-body p30 mt-5">
                            <!-- Start .row -->
                            <div class="row">
                            <!-- col-lg-6 end here -->
                                <div class="col-lg-12">
                                    <!-- col-lg-12 start here -->
                                    <div class="invoice-details mt25">
                                        <div class="well">
                                            <ul class="list-unstyled mb0">
                                                <li><strong>{{__('Invoice')}}</strong>
                                                    #{{$invoice->data['invoicePrefix'] ? $invoice->data['invoicePrefix']. '_' : ''  }}{{$invoice->invoice_id}}
                                                </li>
                                                <li><strong>{{__('Invoice')}}
                                                        {{__('Date')}}
                                                        :</strong> {{ \Carbon\Carbon::parse($invoice->created_at)->format('D M Y')}}
                                                </li>
                                                <li><strong>{{__('Due')}}
                                                        {{__('Date')}}
                                                        :</strong> {{ \Carbon\Carbon::parse($invoice->created_at)->format('D M Y')}}
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt25">
                                        <ul class="list-unstyled left">
                                            <li><strong>{{__('Invoiced To')}}</strong></li>
                                            <li>{{$invoice->data['billingDetails']['receiverFirstName']}} {{$invoice->data['billingDetails']['receiverLastName']}}</li>
                                            <li>{{$invoice->data['billingDetails']['receiverBillingAddress']}},
                                                {{$invoice->data['billingDetails']['receiverState']}},
                                                {{$invoice->data['billingDetails']['receiverPostcode']}}
                                            </li>
                                            <li>{{$invoice->data['billingDetails']['receiverCity']}}</li>
                                            <li>{{$invoice->data['billingDetails']['receiverCountryName']}}</li>
                                        </ul>
                                        <ul class="list-unstyled text-right right">
                                            <li><strong>{{__('Invoice From')}}</strong></li>
                                            <li>{{$invoice->data['billingDetails']['senderName']}}</li>
                                            <li>{{$invoice->data['billingDetails']['senderAddress']}} {{$invoice->data['billingDetails']['senderState']}} {{$invoice->data['billingDetails']['senderPostcode']}}</li>
                                            <li> {{$invoice->data['billingDetails']['senderCity']}}</li>
                                            <li>{{$invoice->data['billingDetails']['senderCountry']}}</li>
                                            <li>{{__('VAT')}}
                                                {{__('Number')}} {{$invoice->data['billingDetails']['senderCompanyNumber']}}</li>
                                        </ul>
                                    </div>
                                    <div class="invoice-items mb-5">
                                        <div class="table-responsive"
                                             tabindex="0">
                                            <table class="table table-bordered">
                                                <thead>

                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th colspan="2" class="per70 text-center">{{__('Description')}}</th>
                                                    {{--<th class="per5 text-center">Qty</th>--}}
                                                    <th class="per25 text-center">{{__('Total')}}</th>
                                                </tr>
                                                <tr>
                                                    <td class="text-center"
                                                        colspan="2">{{\App\Providers\InvoiceServiceProvider::getInvoiceDescriptionByTransaction($invoice->transaction)}}
                                                    </td>
                                                    {{--<td class="text-center">1</td>--}}
                                                    <td class="text-center">{{config('app.site.currency_symbol')}}{{$invoice->data['subtotal']}}
                                                        {{config('app.site.currency_code')}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="text-right">{{__('Total taxes')}}:</th>
                                                    <th class="text-center">{{config('app.site.currency_symbol')}}{{ $invoice->data['taxesTotalAmount']}}
                                                        {{config('app.site.currency_code')}}
                                                    </th>
                                                </tr>
                                                @if(isset($invoice->data['taxes']) && isset($invoice->data['taxes']['data']))
                                                    @foreach($invoice->data['taxes']['data'] as $tax)
                                                        <tr>
                                                            <th colspan="2" class="text-right">
                                                                {{$tax['taxName']}} ({{$tax['taxPercentage']}}
                                                                %{{$tax['taxType'] === 'inclusive' ? ' incl.' : ''}})
                                                            </th>
                                                            <th class="text-center">${{$tax['taxAmount']}} {{config('app.site.currency_code')}}</th>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <th colspan="2" class="text-right">{{__('Total:')}}</th>
                                                    <th class="text-center">{{config('app.site.currency_symbol')}}{{$invoice->data['totalAmount']}} {{config('app.site.currency_code')}}</th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- col-lg-12 end here -->
                            </div>
                            <!-- End .row -->
                        </div>
                        <div class="invoice-footer mt25">
                            <p class="text-center">{{__('Generated on')}} {{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y')}} </p>
                        </div>
                        <p class="d-flex justify-content-center align-items-center">
                            <a href="{{route('my.settings',['type'=>'payments'])}}" class="mr-3">{{__("Back")}}</a>
                            <a href="#" onclick="window.print()" class="btn btn-default ml15 m-0 "> {{__('Print')}}</a>
                        </p>
                    </div>
                    <!-- End .panel -->
                </div>
                <!-- col-lg-12 end here -->
            </div>
        @endif
    </div>
</div>

@include('template.jsVars')
@include('template.jsAssets')

</body>
</html>
