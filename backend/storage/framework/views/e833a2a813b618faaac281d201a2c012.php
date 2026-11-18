<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>iRACK IT Solution — Quotation <?php echo e($quotation->quotation_number); ?></title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #F4F6FA; color: #172033; margin: 0; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0;">
        <tr>
            <td style="background-color: #042562; padding: 28px; text-align: left;">
                <h1 style="color: #FFFFFF; font-size: 22px; margin: 0; font-weight: 800;">iRACK TECH-MART</h1>
                <p style="color: #93C5FD; font-size: 12px; margin: 4px 0 0; text-transform: uppercase; letter-spacing: 1px;">Formal Quotation</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 28px;">
                <p style="font-size: 14px; margin-top: 0; color: #172033;">
                    Dear <strong><?php echo e($quotation->customer_name); ?></strong>,
                </p>
                <?php if(!empty($clientMessage)): ?>
                    <p style="font-size: 13px; line-height: 1.6; color: #475569; white-space: pre-line;"><?php echo e($clientMessage); ?></p>
                <?php else: ?>
                    <p style="font-size: 13px; line-height: 1.6; color: #475569;">
                        Please find our formal quotation attached. The prices, discounts, and VAT in the PDF are the commercial offer for your requested hardware.
                    </p>
                <?php endif; ?>

                <div style="background-color: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 8px; padding: 16px; margin: 20px 0;">
                    <span style="font-size: 11px; font-weight: 700; color: #1E40AF; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;">Quotation reference</span>
                    <strong style="font-size: 20px; font-family: monospace; color: #042562;"><?php echo e($quotation->quotation_number); ?></strong>
                    <p style="font-size: 12px; color: #334155; margin: 10px 0 0;">
                        Total <?php echo e(number_format((float) $quotation->total_amount, 2)); ?>

                        <?php if($quotation->valid_until): ?>
                            · Valid until <?php echo e($quotation->valid_until->format('M d, Y')); ?>

                        <?php endif; ?>
                    </p>
                </div>

                <p style="font-size: 12px; color: #64748B; margin-bottom: 0;">
                    The branded PDF is attached to this message. Reply to this email to approve or request a revision.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #F8FAFC; padding: 20px; text-align: center; font-size: 11px; color: #94A3B8; border-top: 1px solid #E2E8F0;">
                iRACK IT Solution<br />
                © <?php echo e(date('Y')); ?> iRACK IT Solution. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\TechMart\backend\resources\views/emails/quotation_sent.blade.php ENDPATH**/ ?>