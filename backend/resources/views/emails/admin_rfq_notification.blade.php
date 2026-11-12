<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New RFQ Request Received</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #F4F6FA; color: #172033; margin: 0; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0;">
        <tr>
            <td style="background-color: #042562; padding: 24px; text-align: left;">
                <h1 style="color: #FFFFFF; font-size: 20px; margin: 0; font-weight: 800;">iRACK IT Solution — Admin Alert</h1>
                <p style="color: #93C5FD; font-size: 12px; margin: 4px 0 0; text-transform: uppercase; letter-spacing: 1px;">New Request for Quotation (RFQ)</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px;">
                <div style="background-color: #EFF6FF; border-left: 4px solid #1865C0; padding: 12px 16px; margin-bottom: 20px; border-radius: 0 8px 8px 0;">
                    <strong style="color: #042562; font-size: 14px;">Reference: {{ $rfq->rfq_number }}</strong>
                    <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Submitted at: {{ $rfq->submitted_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i') }} UTC</div>
                </div>

                <h3 style="color: #042562; font-size: 14px; margin-top: 0; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">Customer & Company Information</h3>
                <table width="100%" style="font-size: 13px; margin-bottom: 20px;">
                    <tr>
                        <td width="35%" style="color: #64748B; padding: 4px 0;">Customer Name:</td>
                        <td style="font-weight: 600; color: #172033;">{{ $rfq->customer_name }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748B; padding: 4px 0;">Company / Org:</td>
                        <td style="font-weight: 600; color: #172033;">{{ $rfq->company_name ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748B; padding: 4px 0;">Email:</td>
                        <td><a href="mailto:{{ $rfq->customer_email }}" style="color: #1865C0;">{{ $rfq->customer_email }}</a></td>
                    </tr>
                    <tr>
                        <td style="color: #64748B; padding: 4px 0;">Phone:</td>
                        <td style="color: #172033;">{{ $rfq->customer_phone }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748B; padding: 4px 0;">Location:</td>
                        <td style="color: #172033;">{{ $rfq->location ?: ($rfq->city ?: 'N/A') }}</td>
                    </tr>
                    @if($rfq->preferredStore)
                    <tr>
                        <td style="color: #64748B; padding: 4px 0;">Target Branch:</td>
                        <td style="color: #042562; font-weight: 600;">{{ $rfq->preferredStore->name }}</td>
                    </tr>
                    @endif
                </table>

                <h3 style="color: #042562; font-size: 14px; margin-top: 0; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">Requested Equipment Items</h3>
                <table width="100%" cellpadding="6" cellspacing="0" style="font-size: 12px; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="background-color: #F8FAFC; text-align: left; color: #64748B;">
                            <th style="border-bottom: 1px solid #E2E8F0; padding: 8px;">Product Model</th>
                            <th style="border-bottom: 1px solid #E2E8F0; padding: 8px;">SKU</th>
                            <th style="border-bottom: 1px solid #E2E8F0; padding: 8px; text-align: center;">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rfq->items as $item)
                        <tr>
                            <td style="border-bottom: 1px solid #F1F5F9; padding: 8px; font-weight: 600; color: #042562;">
                                {{ $item->product->name ?? 'Custom Item' }}
                            </td>
                            <td style="border-bottom: 1px solid #F1F5F9; padding: 8px; color: #64748B; font-family: monospace;">
                                {{ $item->product->sku ?? 'N/A' }}
                            </td>
                            <td style="border-bottom: 1px solid #F1F5F9; padding: 8px; text-align: center; font-weight: bold; color: #172033;">
                                {{ $item->quantity }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($rfq->customer_notes)
                <h3 style="color: #042562; font-size: 14px; margin-top: 0; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">Customer Technical Notes</h3>
                <div style="background-color: #F8FAFC; padding: 12px; border-radius: 6px; font-size: 12px; color: #475569; margin-bottom: 20px;">
                    {{ $rfq->customer_notes }}
                </div>
                @endif

                <div style="text-align: center; margin-top: 24px;">
                    <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/admin/rfqs" style="display: inline-block; background-color: #042562; color: #FFFFFF; padding: 12px 24px; border-radius: 6px; font-size: 13px; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 1px;">
                        Open RFQ in Staff Portal
                    </a>
                </div>
            </td>
        </tr>
        <tr>
            <td style="background-color: #F8FAFC; padding: 16px; text-align: center; font-size: 11px; color: #94A3B8; border-top: 1px solid #E2E8F0;">
                iRACK IT Solution Management System • Central Operations Notification
            </td>
        </tr>
    </table>
</body>
</html>
