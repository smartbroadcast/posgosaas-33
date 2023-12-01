@if( !empty($purchases) && count($purchases) > 0)
    <div class="container">
        <div class="row invoice">
            <div class="col invoice-details">
                <h1 class="invoice-id h4">{{ $details['invoice_id'] }}</h1>
                <div class="date mb-3">{{ __('Date of Invoice') }}: {{ $details['date'] }}</div>
                <div class="date mb-3 float-right">{!! DNS2D::getBarcodeHTML(route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($details['invoice_id'])), "QRCODE",2,2) !!}</div>
                <span class="clearfix" style="clear: both; display: block;"></span>
            </div>
        </div>
        <div class="row invoice">
            <div class="col contacts">
                <div class="invoice-to">
                    <div class="text-gray-light text-uppercase">{{ __('Invoice From:') }}</div>
                    {!! $details['vendor']['details']  !!}
                </div>
                <div class="company-details">
                    <div class="text-gray-light text-uppercase">{{ __('Invoice To:') }}</div>
                    {!! $details['user']['details']  !!}
                </div>
            </div>
           
           
            
            <div class="col-12 col-md-12">
                <div class="invoice-table">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-left">{{ __('Items') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th class="text-right">{{ __('Price') }}</th>
                            <th class="text-right">{{ __('Tax') }}</th>
                            <th class="text-right">{{ __('Tax Amount') }}</th>
                            <th class="text-right">{{ __('Total') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $total = 0 ?>
                        @foreach($purchases['data'] as $key => $value)
                            <tr>
                                <td class="cart-summary-table text-left">
                                    {{ $value['name'] }}
                                </td>
                                <td class="cart-summary-table">
                                    {{ $value['quantity'] }}
                                </td>
                                <td class="text-right cart-summary-table">
                                    {{ $value['price'] }}
                                </td>
                                <td class="text-right cart-summary-table">
                                    {{ $value['tax'] }}
                                </td>
                                <td class="text-right cart-summary-table">
                                    {{ $value['tax_amount'] }}
                                </td>
                                <td class="text-right cart-summary-table">
                                    {{ $value['subtotal'] }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="text-left font-weight-bold">{{ __('Total') }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right font-weight-bold">{{ $purchases['total'] }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                @if($details['pay'] == 'show')
                    <button class="btn btn-primary btn-sm btn-done-payment text-right float-right rounded-pill" data-url="{{route('purchases.store')}}">{{ __('Done Payment') }}</button>
                @endif
            </div>
        </div>
    </div>
@endif
