<!DOCTYPE html>
<html lang="en"><head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sahad Pharmaceuticals - Invoice #{{$invoice}}</title>

    <link href="{{asset('all.min.css')}}" rel="stylesheet">
    <link href="{{asset('theme.min.css')}}" rel="stylesheet">
    <link href="{{asset('fontawesome-all.min.css')}}" rel="stylesheet">
    <link href="{{asset('invoice.min.css')}}" rel="stylesheet">
    <script src="{{asset('scripts.min.js')}}scripts.min.js"></script>

</head>
<body data-new-gr-c-s-check-loaded="8.933.0" data-gr-ext-installed="">

<div class="container-fluid invoice-container">


    <div class="row invoice-header">
        <div class="col-12 col-sm-6 justify-content-sm-between text-center text-sm-left invoice-col">

            <p><img src="{{asset('logo.png') . '?v=' . time()}} " title=""></p>
            <h3>Invoice #{{$invoice}}</h3>

        </div>

    </div>

    <hr>


    <div class="row justify-content-sm-between">
        <div class="col-12 col-sm-6 order-sm-last text-sm-right invoice-col right">
            <strong>Pay To</strong>
            <address class="small-text">
               Sahad Pharmaceuticals <br>
                (TIN: 32378872-0001)<br>
{{--                To transfer from your bank account, <br>--}}
{{--                choose either Paystack or Rave payment <br>--}}
{{--                gateway and use the bank transfer option<br>--}}
{{--                to get your invoice paid instantly.--}}
            </address>
        </div>
        <div class="col-12 col-sm-6 invoice-col">
            <strong>Invoiced To</strong>
            <address class="small-text">
                Customer Name: <input name="name" id="" class="form-control"><br>
                Address: <textarea name="address" class="form-control" id="address"></textarea>
                <br>
                Nigeria
            </address>
            @if(isset($buyer) && ($buyer->buyer_name || $buyer->buyer_dept))
            <br>
            <strong>Buyer Information</strong>
            <address class="small-text">
                @if($buyer->buyer_name)
                Name: {{ $buyer->buyer_name }}<br>
                @endif
                @if($buyer->buyer_dept)
                Department: {{ $buyer->buyer_dept }}
                @endif
            </address>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-6 order-sm-last text-sm-right invoice-col right">
            <strong>Payment Method</strong><br>
            <span class="small-text float-sm-right" data-role="paymethod-info">
                                                    Bank Transfer                                           </span>
            <br><br>
        </div>
        <div class="col-12 col-sm-6 invoice-col">
            <strong>Invoice Date</strong><br>
            <span class="small-text">
                        {{date('d/m/y')}}<br><br>
                    </span>
        </div>
    </div>

    <br>



    <div class="card bg-default">
        <div class="card-header">
            <h3 class="card-title mb-0 font-size-24"><strong>Invoice Items</strong></h3>
        </div>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                <tr>
                    <td>#</td>
                    <td><strong>Description</strong></td>
                    <td><strong>QTY</strong></td>
                    <td><strong>Unit Price</strong></td>
                    <td><strong>Amount</strong></td>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$item->product}}</td>
                    <td>{{$item->quantity}}</td>
                    <td class="text-center">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ $item->selling_price }}</td>
                    <td>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ $item->amount }}</td>
                </tr>
                @endforeach
                @php
                    $subtotal = $items->sum('amount');
                    $vat = 0;
                    $total = $subtotal + $vat;
                @endphp
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="total-row text-right"><strong>Sub Total</strong></td>
                    <td class="total-row text-center">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="total-row text-right"><strong>VAT (0%)</strong></td>
                    <td class="total-row text-center">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($vat, 2) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="total-row text-right"><strong>Credit</strong></td>
                    <td class="total-row text-center">{!! app(App\Settings\StoreSettings::class)->currency !!} 0.00</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="total-row text-right"><strong>Total</strong></td>
                    <td class="total-row text-center">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($total, 2) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>


    <div class="float-right btn-group btn-group-sm d-print-none">
        <a href="javascript:window.print()" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
{{--        <a href="https://www.whogohost.com/host/dl.php?type=i&amp;id=2110465" class="btn btn-default"><i class="fas fa-download"></i> Download</a>--}}
    </div>


</div>

{{--<p class="text-center d-print-none"><a href="https://www.whogohost.com/host/clientarea.php?action=invoices">« Back to Client Area</a></p><p class="text-center d-print-none"><a href="https://www.whogohost.com/host/clientarea.php?action=invoices">« Back to Client Area</a></p>--}}

<div id="fullpage-overlay" class="w-hidden" style="display: none;">
    <div class="outer-wrapper">
        <div class="inner-wrapper">
            <img src="Go54(Formerly%20WhoGoHost)%20-%20Invoice%20%23_2110465_files/overlay-spinner.svg" alt="">
            <br>
            <span class="msg"></span>
        </div>
    </div>
</div>



<div id="lightboxOverlay" class="lightboxOverlay" style="display: none;"></div><div id="lightbox" class="lightbox" style="display: none;"><div class="lb-outerContainer"><div class="lb-container"><img class="lb-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=="><div class="lb-nav"><a class="lb-prev" href=""></a><a class="lb-next" href=""></a></div><div class="lb-loader"><a class="lb-cancel"></a></div></div></div><div class="lb-dataContainer"><div class="lb-data"><div class="lb-details"><span class="lb-caption"></span><span class="lb-number"></span></div><div class="lb-closeContainer"><a class="lb-close"></a></div></div></div></div></body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration></html>
