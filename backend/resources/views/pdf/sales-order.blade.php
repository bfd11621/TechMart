<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Order {{ $order->order_number }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #172033; font-size: 11px; }
        .navy { color: #042562; }
        h1 { font-size: 22px; margin: 0; color: #042562; }
        .muted { color: #64748B; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        .bar { background: #042562; color: #fff; padding: 8px 12px; font-weight: bold; }
        th { background: #F4F6FA; color: #042562; text-align: left; padding: 6px; border-bottom: 1px solid #E2E8F0; font-size: 9px; }
        td { padding: 6px; border-bottom: 1px solid #EEF2F7; vertical-align: top; }
        .right { text-align: right; }
        .box { border: 1px solid #E2E8F0; padding: 10px; }
        .sign { margin-top: 28px; }
        .line { border-top: 1px solid #042562; width: 180px; margin-top: 36px; padding-top: 4px; font-size: 9px; color: #64748B; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td>
                <h1>iRACK <span style="color:#E20913;">TECH-MART</span></h1>
                <div class="muted">{{ $company['address'] }}<br>{{ $company['phone'] }} · {{ $company['email'] }}</div>
            </td>
            <td class="right">
                <div class="bar">SALES ORDER {{ $order->order_number }}</div>
                <div class="muted" style="margin-top:6px;">
                    Quote {{ $order->quotation?->quotation_number ?: 'Direct sale' }}<br>
                    Issued {{ $order->confirmed_at?->format('d M Y') ?: $order->created_at?->format('d M Y') }}
                </div>
            </td>
        </tr>
    </table>

    <table style="margin-top:16px;">
        <tr>
            <td class="box" style="width:50%;">
                <div class="muted">CLIENT</div>
                <strong class="navy">{{ $order->customer_name }}</strong><br>
                {{ $order->company_name }}<br>
                {{ $order->customer_email }}<br>
                {{ $order->customer_phone }}<br>
                {{ $order->shipping_address ?: $order->billing_address }}
            </td>
            <td class="box">
                <div class="muted">FULFILLMENT</div>
                Client PO: {{ $order->client_po_number ?: '—' }}<br>
                Store: {{ $order->store?->name }}<br>
                Payment: {{ $order->payment_method }} / {{ $order->payment_status?->label() ?? $order->payment_status }}<br>
                Reference: {{ $order->payment_reference ?: '—' }}<br>
                Expected delivery: {{ $order->expected_delivery_date?->format('d M Y') ?: '—' }}<br>
                Status: {{ $order->status?->label() ?? $order->status }}
            </td>
        </tr>
    </table>

    <table style="margin-top:16px;">
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Unit</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product?->name }}</strong><br>
                        <span class="muted">{{ $item->product?->sku }}</span>
                    </td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top:10px; width:42%; margin-left:58%;">
        <tr><td>Subtotal</td><td class="right">{{ number_format($order->subtotal, 2) }}</td></tr>
        <tr><td>Discount</td><td class="right">{{ number_format($order->discount_amount, 2) }}</td></tr>
        <tr><td>VAT</td><td class="right">{{ number_format($order->tax_amount, 2) }}</td></tr>
        <tr><td><strong class="navy">Grand total</strong></td><td class="right"><strong>{{ number_format($order->total_amount, 2) }}</strong></td></tr>
    </table>

    @if($order->notes)
        <p style="margin-top:14px;"><strong class="navy">Notes</strong><br>{{ $order->notes }}</p>
    @endif

    <table class="sign">
        <tr>
            <td><div class="line">Prepared by {{ $order->createdBy?->name }}</div></td>
            <td><div class="line">Client approval</div></td>
            <td><div class="line">Warehouse release</div></td>
        </tr>
    </table>
</body>
</html>
