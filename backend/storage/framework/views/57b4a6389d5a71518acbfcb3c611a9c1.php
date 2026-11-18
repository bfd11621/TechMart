<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation <?php echo e($quotation->quotation_number); ?> — iRACK IT Solution</title>
    <style>
        @page {
            margin: 28px 36px 36px 36px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #172033;
            font-size: 11px;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        /* Corporate Palette */
        .navy { color: #042562; }
        .blue { color: #1865C0; }
        .red { color: #E20913; }

        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #042562;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .header-logo {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #042562;
            line-height: 1;
        }
        .header-logo .red-dot {
            color: #E20913;
        }
        .header-logo .sub {
            display: block;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #1865C0;
            margin-top: 3px;
        }
        .company-info {
            text-align: right;
            font-size: 9px;
            color: #475569;
            line-height: 1.35;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-box {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 10px 14px;
        }
        .meta-title {
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1865C0;
            margin-bottom: 4px;
        }
        .client-name {
            font-size: 13px;
            font-weight: 800;
            color: #042562;
        }

        .doc-title-bar {
            background-color: #042562;
            color: #FFFFFF;
            padding: 6px 12px;
            border-radius: 4px;
            margin-bottom: 14px;
        }
        .doc-title {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .doc-ref {
            font-size: 12px;
            font-weight: 900;
            font-family: monospace;
            text-align: right;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .items-table th {
            background-color: #042562;
            color: #FFFFFF;
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 8px;
            border: 1px solid #042562;
        }
        .items-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #E2E8F0;
            font-size: 9.5px;
            vertical-align: top;
        }
        .items-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }
        .item-name {
            font-weight: 700;
            color: #042562;
        }
        .item-sub {
            font-size: 8.5px;
            color: #64748B;
            font-family: monospace;
        }

        /* Summary Box */
        .summary-wrapper {
            width: 100%;
            margin-bottom: 18px;
        }
        .terms-cell {
            width: 58%;
            vertical-align: top;
            padding-right: 16px;
        }
        .calc-cell {
            width: 42%;
            vertical-align: top;
        }
        .calc-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
        }
        .calc-table td {
            padding: 5px 10px;
            font-size: 9.5px;
        }
        .total-row {
            background-color: #042562;
            color: #FFFFFF;
            font-weight: 800;
            font-size: 12px;
        }
        .total-row td {
            padding: 8px 10px;
            color: #FFFFFF;
        }

        .terms-box {
            background-color: #FAFAFA;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 8.5px;
            color: #475569;
            line-height: 1.4;
        }
        .terms-header {
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #042562;
            margin-bottom: 4px;
        }

        /* Signatory Section */
        .signature-table {
            width: 100%;
            margin-top: 24px;
            border-top: 1px dashed #CBD5E1;
            padding-top: 14px;
        }
        .sig-block {
            width: 46%;
            vertical-align: top;
        }
        .sig-line {
            border-bottom: 1.5px solid #042562;
            height: 38px;
            margin-bottom: 4px;
        }
        .sig-title {
            font-size: 8.5px;
            font-weight: 700;
            color: #042562;
            text-transform: uppercase;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94A3B8;
            text-align: center;
            border-top: 1px solid #E2E8F0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="header-logo">
                    <span class="red-dot">i</span>RACK
                    <span class="sub">TECH-MART SOLUTIONS</span>
                </div>
                <div style="font-size: 8.5px; color: #64748B; margin-top: 4px;">
                    Enterprise IT Infrastructure & Systems Integration
                </div>
            </td>
            <td style="width: 50%;" class="company-info">
                <strong><?php echo e($company['name']); ?></strong><br>
                <?php echo e($company['headquarters']); ?><br>
                Hotline: <?php echo e($company['phone']); ?><br>
                Procurement: <?php echo e($company['email']); ?><br>
                Portal: <?php echo e($company['website']); ?> | Tax ID: <?php echo e($company['tax_id']); ?>

            </td>
        </tr>
    </table>

    <!-- Title Bar -->
    <table style="width: 100%; margin-bottom: 12px;">
        <tr>
            <td class="doc-title-bar">
                <table style="width: 100%;">
                    <tr>
                        <td class="doc-title">Formal Equipment Quotation Proposal</td>
                        <td class="doc-ref"><?php echo e($quotation->quotation_number); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Metadata: Customer & Quotation Particulars -->
    <table class="meta-table">
        <tr>
            <!-- Customer Particulars -->
            <td style="width: 50%; padding-right: 8px; vertical-align: top;">
                <div class="meta-box">
                    <div class="meta-title">Prepared Exclusively For:</div>
                    <div class="client-name"><?php echo e($quotation->customer_name); ?></div>
                    <?php if($quotation->company_name): ?>
                        <div style="font-weight: 700; color: #334155; font-size: 10px;"><?php echo e($quotation->company_name); ?></div>
                    <?php endif; ?>
                    <div style="color: #64748B; font-size: 9px; margin-top: 3px;">
                        Email: <?php echo e($quotation->customer_email); ?><br>
                        Phone: <?php echo e($quotation->customer_phone); ?><br>
                        <?php if($quotation->billing_address): ?>
                            Billing Site: <?php echo e($quotation->billing_address); ?>

                        <?php endif; ?>
                    </div>
                </div>
            </td>

            <!-- Quotation Particulars -->
            <td style="width: 50%; padding-left: 8px; vertical-align: top;">
                <div class="meta-box">
                    <div class="meta-title">Quotation Parameters:</div>
                    <table style="width: 100%; font-size: 9px;">
                        <tr>
                            <td style="color: #64748B;">Date of Issuance:</td>
                            <td style="font-weight: 700; text-align: right;"><?php echo e($quotation->created_at->format('M d, Y')); ?></td>
                        </tr>
                        <tr>
                            <td style="color: #64748B;">Price Lock Guarantee:</td>
                            <td style="font-weight: 700; text-align: right; color: #16A34A;">30 Calendar Days</td>
                        </tr>
                        <tr>
                            <td style="color: #64748B;">Valid Until:</td>
                            <td style="font-weight: 800; text-align: right; color: #E20913;"><?php echo e($quotation->valid_until ? $quotation->valid_until->format('M d, Y') : 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td style="color: #64748B;">Fulfillment Hub:</td>
                            <td style="font-weight: 700; text-align: right;"><?php echo e($quotation->store?->name ?? 'Central Distribution Hub (HQ)'); ?></td>
                        </tr>
                        <tr>
                            <td style="color: #64748B;">Solutions Architect:</td>
                            <td style="font-weight: 700; text-align: right;"><?php echo e($quotation->preparedBy?->name ?? 'Senior Systems Architect'); ?></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 47%;">Equipment Description & Specifications</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 13%; text-align: right;">Unit Price ($)</th>
                <th style="width: 10%; text-align: center;">Disc %</th>
                <th style="width: 15%; text-align: right;">Total ($)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $quotation->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="text-align: center; color: #64748B;"><?php echo e($index + 1); ?></td>
                    <td>
                        <div class="item-name"><?php echo e($item->product?->name ?? 'Hardware Model'); ?></div>
                        <div class="item-sub">SKU: <?php echo e($item->product?->sku); ?> | Model: <?php echo e($item->product?->model_number); ?> | Brand: <?php echo e($item->product?->brand?->name); ?></div>
                        <?php if($item->notes): ?>
                            <div style="font-size: 8px; color: #1865C0; margin-top: 2px;">Config: <?php echo e($item->notes); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center; font-weight: 700;"><?php echo e($item->quantity); ?></td>
                    <td style="text-align: right; font-family: monospace;"><?php echo e(number_format($item->unit_price, 2)); ?></td>
                    <td style="text-align: center; color: <?php echo e($item->discount_percent > 0 ? '#16A34A' : '#64748B'); ?>;">
                        <?php echo e($item->discount_percent > 0 ? $item->discount_percent . '%' : '-'); ?>

                    </td>
                    <td style="text-align: right; font-weight: 700; font-family: monospace;">
                        <?php echo e(number_format($item->total_price, 2)); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- Terms and Calculations Section -->
    <table class="summary-wrapper">
        <tr>
            <!-- Commercial Terms -->
            <td class="terms-cell">
                <div class="terms-box">
                    <div class="terms-header">Standard B2B Procurement Terms & Conditions:</div>
                    <?php echo nl2br(e($quotation->terms_conditions)); ?>

                </div>
            </td>

            <!-- Financial Calculations -->
            <td class="calc-cell">
                <table class="calc-table">
                    <tr>
                        <td style="color: #64748B;">Equipment Subtotal:</td>
                        <td style="text-align: right; font-weight: 700; font-family: monospace;">
                            $<?php echo e(number_format($quotation->subtotal, 2)); ?>

                        </td>
                    </tr>
                    <?php if($quotation->discount_amount > 0): ?>
                        <tr>
                            <td style="color: #16A34A;">
                                Commercial Discount (<?php echo e($quotation->discount_type === 'percentage' ? $quotation->discount_value . '%' : 'Fixed'); ?>):
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #16A34A; font-family: monospace;">
                                -$<?php echo e(number_format($quotation->discount_amount, 2)); ?>

                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="color: #64748B;">Estimated VAT / Tax (<?php echo e(number_format($quotation->tax_rate, 1)); ?>%):</td>
                        <td style="text-align: right; font-weight: 700; font-family: monospace;">
                            $<?php echo e(number_format($quotation->tax_amount, 2)); ?>

                        </td>
                    </tr>
                    <tr class="total-row">
                        <td>TOTAL AMOUNT (USD):</td>
                        <td style="text-align: right; font-family: monospace;">
                            $<?php echo e(number_format($quotation->total_amount, 2)); ?>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Authorized Signatures -->
    <table class="signature-table">
        <tr>
            <td class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-title">Authorized Solutions Officer — iRACK IT Solution</div>
                <div style="font-size: 8px; color: #64748B;"><?php echo e($quotation->preparedBy?->name ?? 'Alexander Vance'); ?> | Senior Solutions Architect</div>
            </td>
            <td style="width: 8%;"></td>
            <td class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-title">Client Acceptance & Purchase Authorization</div>
                <div style="font-size: 8px; color: #64748B;">Authorized Signature & Corporate Seal</div>
            </td>
        </tr>
    </table>

    <!-- Document Footer -->
    <div class="footer">
        iRACK IT Solution Commercial Document Ref: <?php echo e($quotation->quotation_number); ?> | Page 1 of 1 | This quotation is governed by iRACK IT Solution Master Services Agreement.
    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\TechMart\backend\resources\views/pdf/quotation.blade.php ENDPATH**/ ?>