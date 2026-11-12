<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>iRACK IT Solution — Request for Quotation Received</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #F4F6FA; color: #172033; margin: 0; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0;">
        <tr>
            <td style="background-color: #042562; padding: 28px; text-align: left;">
                <h1 style="color: #FFFFFF; font-size: 22px; margin: 0; font-weight: 800;">iRACK TECH-MART</h1>
                <p style="color: #93C5FD; font-size: 12px; margin: 4px 0 0; text-transform: uppercase; letter-spacing: 1px;">Official Quotation Request Acknowledgment</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 28px;">
                <p style="font-size: 14px; margin-top: 0; color: #172033;">
                    Dear <strong>{{ $rfq->customer_name }}</strong>,
                </p>
                <p style="font-size: 13px; line-height: 1.6; color: #475569;">
                    Thank you for submitting your equipment bill-of-materials request to iRACK IT Solution. Your inquiry has been registered in our enterprise fulfillment network with reference number:
                </p>

                <div style="background-color: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px; padding: 16px; text-align: center; margin: 20px 0;">
                    <span style="font-size: 11px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;">Unique RFQ Reference Number</span>
                    <strong style="font-size: 20px; font-family: monospace; color: #042562;">{{ $rfq->rfq_number }}</strong>
                </div>

                <h3 style="color: #042562; font-size: 14px; margin-top: 24px; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">Requested IT Hardware Line Items</h3>
                <table width="100%" cellpadding="6" cellspacing="0" style="font-size: 12px; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="background-color: #F8FAFC; text-align: left; color: #64748B;">
                            <th style="border-bottom: 1px solid #E2E8F0; padding: 8px;">Equipment Model</th>
                            <th style="border-bottom: 1px solid #E2E8F0; padding: 8px;">SKU</th>
                            <th style="border-bottom: 1px solid #E2E8F0; padding: 8px; text-align: center;">Requested Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rfq->items as $item)
                        <tr>
                            <td style="border-bottom: 1px solid #F1F5F9; padding: 8px; font-weight: 600; color: #042562;">
                                {{ $item->product->name ?? 'Enterprise Equipment' }}
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

                <div style="background-color: #EFF6FF; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 12px; color: #1E40AF; line-height: 1.5;">
                    <strong>Next Steps & SLAs:</strong><br />
                    • A dedicated enterprise solutions architect is reviewing stock allocations across our 3 regional hubs.<br />
                    • A formal, numbered Quotation Document with a <strong>30-day price lock guarantee</strong> will be sent to this email address within 24 business hours.<br />
                    • <em>Note:</em> An RFQ is an inquiry for commercial pricing and does not constitute a confirmed financial commitment or charged purchase order.
                </div>

                <p style="font-size: 12px; color: #64748B; margin-bottom: 0;">
                    Need immediate assistance or urgent same-day delivery? Contact our technical desk at <strong style="color: #042562;">+1 (555) 019-2831</strong> quoting reference <strong>{{ $rfq->rfq_number }}</strong>.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #F8FAFC; padding: 20px; text-align: center; font-size: 11px; color: #94A3B8; border-top: 1px solid #E2E8F0;">
                iRACK IT Solution • Silicon Valley Gateway, San Jose, CA<br />
                © 2026 iRACK IT Solution. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
