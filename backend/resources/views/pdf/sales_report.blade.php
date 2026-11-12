<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Performance Report {{ $reportNumber }} — iRACK IT Solution</title>
    <style>
        @page {
            margin: 28px 36px 36px 36px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #172033;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .navy { color: #042562; }
        .blue { color: #1865C0; }
        .red { color: #E20913; }
        .green { color: #16A34A; }

        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #042562;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-logo {
            font-size: 24px;
            font-weight: 900;
            color: #042562;
            line-height: 1;
        }
        .header-logo .red-dot { color: #E20913; }
        .header-logo .sub {
            display: block;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #1865C0;
            margin-top: 3px;
        }
        .company-info {
            text-align: right;
            font-size: 8.5px;
            color: #475569;
            line-height: 1.35;
        }

        .title-bar {
            background-color: #042562;
            color: #FFFFFF;
            padding: 6px 12px;
            border-radius: 4px;
            margin-bottom: 14px;
        }
        .title-text {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .title-ref {
            font-size: 11px;
            font-weight: 900;
            font-family: monospace;
            text-align: right;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 14px;
        }
        .meta-box {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .meta-label {
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1865C0;
            margin-bottom: 2px;
        }
        .meta-value {
            font-size: 11px;
            font-weight: 800;
            color: #042562;
        }

        /* KPI Grid */
        .kpi-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .kpi-cell {
            background-color: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
            width: 25%;
        }
        .kpi-num {
            font-size: 14px;
            font-weight: 900;
            color: #042562;
            margin-top: 2px;
        }
        .kpi-title {
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748B;
        }

        /* Tables */
        .section-heading {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #042562;
            border-bottom: 1px solid #CBD5E1;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .data-table th {
            background-color: #F1F5F9;
            color: #334155;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 8px;
            border-top: 1px solid #CBD5E1;
            border-bottom: 1.5px solid #042562;
            text-align: left;
        }
        .data-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #E2E8F0;
            font-size: 8.5px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 800; }

        .notice-banner {
            background-color: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 8px;
            color: #1E40AF;
        }

        .footer-table {
            width: 100%;
            margin-top: 24px;
            border-top: 1px solid #CBD5E1;
            padding-top: 12px;
        }
        .signature-box {
            border-top: 1px dashed #64748B;
            padding-top: 4px;
            width: 180px;
            text-align: center;
            font-size: 8px;
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
                    <span class="sub">TECH-MART &bull; IT SOLUTIONS</span>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;" class="company-info">
                <strong>iRACK IT Solution Commercial Co.</strong><br>
                Commercial Registration (CR): 1010892019<br>
                VAT / Tax ID: 310982019200003<br>
                Kingdom of Saudi Arabia &bull; Riyadh Distribution HQ<br>
                Web: www.irack.com &bull; Tel: +966 11 492 8800
            </td>
        </tr>
    </table>

    <!-- Document Title Bar -->
    <table class="title-bar" style="width: 100%;">
        <tr>
            <td class="title-text">Official Sales & Revenue Performance Audit</td>
            <td class="title-ref">{{ $reportNumber }}</td>
        </tr>
    </table>

    <!-- Audit Metadata Grid -->
    <table class="meta-table">
        <tr>
            <td style="width: 32%; padding-right: 6px;">
                <div class="meta-box">
                    <div class="meta-label">Audit Analysis Period</div>
                    <div class="meta-value">{{ $periodLabel }}</div>
                </div>
            </td>
            <td style="width: 36%; padding-right: 6px;">
                <div class="meta-box">
                    <div class="meta-label">Fulfillment Store Scope</div>
                    <div class="meta-value" style="font-size: 10px;">{{ $storeName }}</div>
                </div>
            </td>
            <td style="width: 32%;">
                <div class="meta-box">
                    <div class="meta-label">Generated By / Timestamp</div>
                    <div class="meta-value" style="font-size: 9.5px;">{{ $generatedBy }} &bull; {{ $generatedAt }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Revenue Recognition Assurance Notice -->
    <div class="notice-banner">
        <strong>Revenue Recognition Standard:</strong> Figures strictly represent confirmed, processing, and completed sales orders. In compliance with enterprise financial governance, RFQ inquiries, active quotations, and cancelled orders are segregated and excluded from reported revenue.
    </div>

    <!-- Executive KPI Grid -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-cell">
                <div class="kpi-title">Recognized Sales Revenue</div>
                <div class="kpi-num green">SAR {{ number_format($summary['total_revenue'], 2) }}</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-title">Confirmed Orders</div>
                <div class="kpi-num">{{ $summary['total_orders'] }} Orders</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-title">Payments Collected</div>
                <div class="kpi-num blue">SAR {{ number_format($summary['total_collected'], 2) }}</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-title">Outstanding Receivables</div>
                <div class="kpi-num red">SAR {{ number_format($summary['outstanding_receivables'], 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- Performance by Category -->
    <div class="section-heading">Sales Performance by Product Classification Category</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">Category</th>
                <th style="width: 20%;" class="text-center">Units Sold</th>
                <th style="width: 25%;" class="text-right">Revenue (SAR)</th>
                <th style="width: 15%;" class="text-right">Revenue Share</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byCategory as $cat)
                <tr>
                    <td class="font-bold navy">{{ $cat['name'] }}</td>
                    <td class="text-center">{{ $cat['units_sold'] }}</td>
                    <td class="text-right font-bold">{{ number_format($cat['revenue'], 2) }}</td>
                    <td class="text-right">{{ $cat['percentage'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #94A3B8;">No transactions recorded in this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Performance by Manufacturer Brand -->
    <div class="section-heading">Sales Performance by Hardware Manufacturer Brand</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">Manufacturer Brand</th>
                <th style="width: 20%;" class="text-center">Units Sold</th>
                <th style="width: 25%;" class="text-right">Revenue (SAR)</th>
                <th style="width: 15%;" class="text-right">Revenue Share</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byBrand as $brand)
                <tr>
                    <td class="font-bold navy">{{ $brand['name'] }}</td>
                    <td class="text-center">{{ $brand['units_sold'] }}</td>
                    <td class="text-right font-bold">{{ number_format($brand['revenue'], 2) }}</td>
                    <td class="text-right">{{ $brand['percentage'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #94A3B8;">No transactions recorded in this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Underlying Sales Transactions Feed -->
    <div class="section-heading">Underlying Confirmed Sales Transactions Ledger</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 18%;">Order Number</th>
                <th style="width: 16%;">Confirmed Date</th>
                <th style="width: 26%;">Client / Organization</th>
                <th style="width: 16%;">Store Branch</th>
                <th style="width: 12%;" class="text-right">Total (SAR)</th>
                <th style="width: 12%;" class="text-center">Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td class="font-bold navy">{{ $t->order_number }}</td>
                    <td>{{ $t->confirmed_at ? $t->confirmed_at->format('Y-m-d H:i') : '' }}</td>
                    <td>
                        <strong>{{ $t->customer_name }}</strong><br>
                        <span style="color: #64748B; font-size: 7.5px;">{{ $t->company_name }}</span>
                    </td>
                    <td>{{ $t->store ? $t->store->name : 'HQ' }}</td>
                    <td class="text-right font-bold">{{ number_format($t->total_amount, 2) }}</td>
                    <td class="text-center">
                        <span style="font-size: 7.5px; font-weight: bold; padding: 2px 5px; border-radius: 3px; background-color: #F1F5F9;">
                            {{ $t->payment_status->value }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #94A3B8;">No sales orders confirmed in this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signoff Block -->
    <table class="footer-table">
        <tr>
            <td style="width: 50%;">
                <div class="signature-box">
                    <strong>Report Prepared & Audited By</strong><br>
                    {{ $generatedBy }}<br>
                    Enterprise Financial Operations
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="signature-box" style="margin-left: auto;">
                    <strong>Executive Board Approval</strong><br>
                    Director of Enterprise Solutions<br>
                    Official Corporate Stamp
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
