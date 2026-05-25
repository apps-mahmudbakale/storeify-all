<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Receipt</title>
    <style>
        * { font-size: 12px; font-family: 'Times New Roman'; }
        td, th, tr, table { border-top: 1px solid black; border-collapse: collapse; }
        td.description, th.description { width: 75px; max-width: 75px; }
        td.quantity, th.quantity { width: 40px; max-width: 40px; word-break: break-all; }
        td.price, th.price { width: 40px; max-width: 40px; word-break: break-all; }
        .centered { text-align: center; align-content: center; }
        .ticket { width: 155px; max-width: 155px; }
        img { max-width: inherit; width: inherit; }
        @media print { .hidden-print, .hidden-print * { display: none !important; } }
        
        /* Loading screen styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            font-size: 16px;
            color: #333;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Processing your receipt...</div>
    </div>

    <div class="ticket" align="center" style="max-width: 1000px; width: 328px;">
        <img src="{{ !empty(app(App\Settings\StoreSettings::class)->store_logo) ? asset('storage/store/' . app(App\Settings\StoreSettings::class)->store_logo) : asset('assets/img/logo.png') }}"
            alt="Logo" style="width: 100px">
        <br>
        {{ app(App\Settings\StoreSettings::class)->store_name ?: 'Storeify' }}
        <p class="centered">PURCHASE RECEIPT
            <br>{{ app(App\Settings\StoreSettings::class)->store_address }}
            <br>Date: {{ date('d/m/Y') }} &nbsp; {{ $invoice }}
        </p>
        <table style="font-size: 24px; font-weight: bold; width: inherit;">
            <thead>
                <tr>
                    <th class="description">Description</th>
                    <th class="quantity">Q.</th>
                    <th class="price">{!! app(App\Settings\StoreSettings::class)->currency !!}</th>
                    <th class="price" style="max-width:50px;width:51px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td class="description" style="text-align:center;">{{ $item->product }}</td>
                        <td class="quantity" style="text-align:center;">{{ $item->quantity }}</td>
                        <td class="price" style="text-align:center;">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ $item->selling_price }}</td>
                        <td class="price" style="text-align:center;">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ $item->amount }}</td>
                    </tr>
                @endforeach

                @if(isset($returns) && $returns->count() > 0)
                    <tr>
                        <td colspan="4" style="text-align:center;background-color:#eee;font-weight:bold;">RETURNED ITEMS</td>
                    </tr>
                    @foreach ($returns as $return)
                        <tr style="color:#666;font-style:italic;">
                            <td class="description" style="text-align:center;">{{ $return->product }}</td>
                            <td class="quantity" style="text-align:center;">{{ $return->return_qty }}</td>
                            <td class="price" style="text-align:center;">-{!! app(App\Settings\StoreSettings::class)->currency !!} {{ $return->selling_price }}</td>
                            <td class="price" style="text-align:center;">-{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($return->return_qty * $return->selling_price, 2) }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr>
                    <td colspan="3" style="font-weight:bold;text-align:right;">Subtotal:</td>
                    <td style="font-weight:bold;text-align:center;">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($sum->subtotal ?? $sum->sum, 2) }}</td>
                </tr>
                @if(($sum->discount ?? 0) > 0)
                <tr>
                    <td colspan="3" style="font-weight:bold;text-align:right;color:#c0392b;">Discount:</td>
                    <td style="font-weight:bold;text-align:center;color:#c0392b;">- {!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($sum->discount, 2) }}</td>
                </tr>
                @endif
                @if(($sum->vat ?? 0) > 0)
                <tr>
                    <td colspan="3" style="font-weight:bold;text-align:right;">VAT (7.5%):</td>
                    <td style="font-weight:bold;text-align:center;">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($sum->vat, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" style="font-weight:bold;text-align:right;">Total:</td>
                    <td style="font-weight:bold;text-align:center;">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($sum->sum, 2) }}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <p class="centered">Transaction Processed By
            <br>{{ ucfirst($user->name ?? 'N/A') }}
        </p>
        <p class="centered">Thanks for your purchase!
            <br>{!! app(App\Settings\StoreSettings::class)->store_name ?: 'Storeify' !!}
        </p>
    </div>

    <div class="hidden-print" style="text-align:center; margin: 16px 0; display: flex; gap: 8px; justify-content: center;">
        <button onclick="window.print()" style="padding: 8px 20px; background:#28a745; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:13px;">
            🖨 Print
        </button>
        <button onclick="window.close()" style="padding: 8px 20px; background:#6c757d; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:13px;">
            ✕ Close
        </button>
    </div>

    <script>
        window.onload = function() {
            // Hide loading overlay after 1.5 seconds
            setTimeout(function() {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.style.display = 'none';
                }
                // Auto-print only on first save, not on reprint
                if (!window.location.search.includes('reprint=1')) {
                    window.print();
                }
            }, 1500);
        };
    </script>
</body>
</html>
