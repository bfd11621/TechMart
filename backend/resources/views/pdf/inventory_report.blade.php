<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Audit Report {{ $reportNumber }} — iRACK IT Solution</title>
    <style>
        @page {
            margin: 24px 30px 30px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #172033;
            font-size: 9.5px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        .navy { color: #042562; }
        .blue { color: #1865C0; }
        .red { color: #DC2626; }
        .amber { color: #D97706; }
        .green { color: #16A34A; }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #042562;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header-logo {
            font-size: 22px;
            font-weight: 900;
            color: #042562;
            line-height: 1;
        }
        .header-logo .red-dot { color: #E20913; }
        .header-logo .sub {
            display: block;
            font-size: 7.5px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #1865C0;
            margin-top: 2px;
        }
        .company-info {
            text-align: right;
            font-size: 8px;
            color: #475569;
            line-height: 1.3;
        }

        .title-bar {
            background-color: #042562;
            color: #FFFFFF;
            padding: 5px 10px;
            border-radius: 4px;
            margin-bottom: 12px;
        }
        .title-text {
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .title-ref {
            font-size: 10.5px;
            font-weight: 900;
            font-family: monospace;
            text-align: right;
        }

        /* KPI Row */
        .kpi-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .kpi-cell {
            background-color: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-radius: 5px;
            padding: 6px 8px;
            text-align: center;
            width: 25%;
        }
        .kpi-num {
            font-size: 13px;
            font-weight: 900;
            color: #042562;
            margin-top: 2px;
        }
        .kpi-title {
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748B;
        }

        .section-heading {
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #042562;
            border-bottom: 1px solid #CBD5E1;
            padding-bottom: 3px;
            margin-top: 12px;
            margin-bottom: 6px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #F1F5F9;
            color: #334155;
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 5px 6px;
            border-top: 1px solid #CBD5E1;
            border-bottom: 1.5px solid #042562;
            text-align: left;
        }
        .data-table td {
            padding: 4.5px 6px;
            border-bottom: 1px solid #E2E8F0;
            font-size: 8px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 800; }

        .badge {
            display: inline-block;
            font-size: 7px;
            font-weight: 800;
            padding: 1.5px 4px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-instock { background-color: #DCFCE7; color: #166534; }
        .badge-lowstock { background-color: #FEF3C7; color: #92400E; }
        .badge-outstock { background-color: #FEE2E2; color: #991B1B; }

        .footer-table {
            width: 100%;
            margin-top: 18px;
            border-top: 1px solid #CBD5E1;
            padding-top: 10px;
        }
        .signature-box {
            border-top: 1px dashed #64748B;
            padding-top: 3px;
            width: 160px;
            text-align: center;
            font-size: 7.5px;
            color: #475569;
        }
    </style>
</head>
<body>

    <!-- Header Letterhead -->
    <table class="header-table">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="header-logo">
                    <span class="red-dot">&#9679;</span> iRACK
                    <span class="sub">TECH-MART &bull; MULTI-STORE LOGISTICS</span>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;" class="company-info">
                <strong>iRACK IT Solution Commercial Co.</strong><br>
                Central Logistics & Inventory Control Division<br>
                Commercial Registration: 1010892019 &bull; VAT: 310982019200003<br>
                Branch Audit Scope: <strong>{{ $storeName }}</strong>
            </td>
        </tr>
    </table>

    <!-- Document Title Bar -->
    <table class="title-bar" style="width: 100%;">
        <tr>
            <td class="title-text">Multi-Store Physical Inventory & Valuation Audit Report</td>
            <td class="title-ref">{{ $reportNumber }}</td>
        </tr>
    </table>

    <!-- Executive KPI Grid -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-cell">
                <div class="kpi-title">Total On-Hand Stock</div>
                <div class="kpi-num">{{ number_format($totalUnits) }} Units</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-title">Total Stock Valuation</div>
                <div class="kpi-num green">SAR {{ number_format($totalValuation, 2) }}</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-title">Low Stock Threshold Warnings</div>
                <div class="kpi-num amber">{{ $lowStockCount }} SKUs</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-title">Depleted / Out-of-Stock SKUs</div>
                <div class="kpi-num red">{{ $outOfStockCount }} SKUs</div>
            </td>
        </tr>
    </table>

    <!-- Master Inventory Stock Valuation Matrix -->
    <div class="section-heading">Consolidated Store Inventory Balances & Valuation</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">SKU / Code</th>
                <th style="width: 14%;">Model Number</th>
                <th style="width: 24%;">Product Description</th>
                <th style="width: 14%;">Category & Brand</th>
                <th style="width: 12%;">Branch Store</th>
                <th style="width: 6%;" class="text-center">On-Hand</th>
                <th style="width: 8%;" class="text-right">Cost (SAR)</th>
                <th style="width: 10%;" class="text-right">Valuation (SAR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $inv)
                @php
                    $unitCost = (float) ($inv->product->cost_price ?: $inv->product->base_price);
                    $valuation = (float) $inv->quantity * $unitCost;
                @endphp
                <tr>
                    <td class="font-bold navy">{{ $inv->product->sku }}</td>
                    <td>{{ $inv->product->model_number }}</td>
                    <td>{{ $inv->product->name }}</td>
                    <td>
                        {{ $inv->product->category?->name ?? 'IT Solutions' }}<br>
                        <span style="color: #64748B; font-size: 7px;">{{ $inv->product->brand?->name }}</span>
                    </td>
                    <td>{{ $inv->store?->name ?? 'HQ' }}</td>
                    <td class="text-center font-bold">{{ $inv->quantity }}</td>
                    <td class="text-right">{{ number_format($unitCost, 2) }}</td>
                    <td class="text-right font-bold navy">{{ number_format($valuation, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="color: #94A3B8;">No inventory records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Slow-Moving / Dead Stock Capital Risk Analysis -->
    @if(isset($slowMoving['items']) && count($slowMoving['items']) > 0)
        <div class="section-heading">Slow-Moving & Stagnant Stock Capital Risk Analysis (Threshold: {{ $slowMoving['threshold_days'] }} Days)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 14%;">SKU</th>
                    <th style="width: 26%;">Product Name</th>
                    <th style="width: 14%;">Store Branch</th>
                    <th style="width: 8%;" class="text-center">On-Hand</th>
                    <th style="width: 10%;" class="text-center">Stock Age</th>
                    <th style="width: 12%;" class="text-center">Last Sale</th>
                    <th style="width: 16%;" class="text-right">Tied Capital (SAR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($slowMoving['items'] as $item)
                    <tr>
                        <td class="font-bold navy">{{ $item['sku'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['store_name'] }}</td>
                        <td class="text-center font-bold">{{ $item['on_hand_quantity'] }}</td>
                        <td class="text-center">{{ $item['stock_age_days'] }} days</td>
                        <td class="text-center">{{ $item['last_sold_at'] ?? 'Never Sold' }}</td>
                        <td class="text-right font-bold red">{{ number_format($item['tied_up_capital'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Audit Signoff Block -->
    <table class="footer-table">
        <tr>
            <td style="width: 50%;">
                <div class="signature-box">
                    <strong>Report Compiled By</strong><br>
                    {{ $generatedBy }}<br>
                    Inventory & Logistics Audit Officer
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="signature-box" style="margin-left: auto;">
                    <strong>Verification & Signoff</strong><br>
                    Director of Multi-Store Logistics<br>
                    iRACK Central Hub Seal
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
